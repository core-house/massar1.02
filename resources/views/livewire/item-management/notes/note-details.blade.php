<?php

use Livewire\Volt\Component;
use App\Models\NoteDetails;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $noteId;
    public $noteDetails;
    public $noteDetailsName;
    public $noteDetailsId;
    public $isNoteDetailsEdit = false;
    public $showModal = false;
    public $parentNoteName = '';

    protected function rules()
    {
        return [
            'noteDetailsName' => 'required|string|max:60|unique:note_details,name,' . $this?->noteDetailsId ?? '',
        ];
    }

    public function messages()
    {
        return [
            'noteDetailsName.required' => __('validation.name_required'),
            'noteDetailsName.string' => __('validation.name_must_be_string'),
            'noteDetailsName.max' => __('validation.name_max_length'),
            'noteDetailsName.unique' => __('validation.name_already_exists'),
        ];
    }

    public function mount($noteId)
    {
        $this->noteId = $noteId;

        $this->loadNoteDetails();
        $parentNote = Note::find($this->noteId);
        if ($parentNote) {
            $this->parentNoteName = $parentNote->name;
        }
    }

    public function loadNoteDetails(): void
    {
        $this->noteDetails = NoteDetails::where('note_id', $this->noteId)->orderBy('id')->get();
    }
    private function getPermissionPrefix(): string
    {
        return match ($this->noteId) {
            1 => 'groups', // Groups
            2 => 'Categories', // Categories
            default => 'items', // fallback
        };
    }

    public function createNoteDetails()
    {
        $permission = 'create ' . $this->getPermissionPrefix();
        abort_unless(Auth::user()->can($permission), 403);

        $this->resetValidation('noteDetailsName');
        $this->reset(['noteDetailsName', 'noteDetailsId']);
        $this->isNoteDetailsEdit = false;
        $this->showModal = true;
        $this->dispatch('showNoteDetailsModal');
    }

    public function editNoteDetails(NoteDetails $noteDetails)
    {
        $permission = 'edit ' . $this->getPermissionPrefix();
        abort_unless(Auth::user()->can($permission), 403);

        $this->noteDetailsId = $noteDetails->id;
        $this->noteDetailsName = $noteDetails->name;
        $this->isNoteDetailsEdit = true;
        $this->showModal = true;
        $this->dispatch('showNoteDetailsModal');
    }

    public function saveNoteDetails()
    {
        $permissionPrefix = $this->getPermissionPrefix();
        $permission = $this->isNoteDetailsEdit ? 'edit ' . $permissionPrefix : 'create ' . $permissionPrefix;
        abort_unless(Auth::user()->can($permission), 403);

        $validated = $this->validate(['noteDetailsName' => 'required|string|max:60|unique:note_details,name,' . $this->noteDetailsId]);
        if ($this->isNoteDetailsEdit) {
            $noteDetails = NoteDetails::find($this->noteDetailsId);
            $oldName = $noteDetails->name; // Save old name
            $noteDetails->name = $this->noteDetailsName;
            $noteDetails->save();

            // تحديث الاسم في جدول item_notes
            \DB::table('item_notes')
                ->where('note_id', $this->noteId)
                ->where('note_detail_name', $oldName)
                ->update(['note_detail_name' => $this->noteDetailsName]);

            session()->flash('success', __('items.note_detail_updated_successfully'));
        } else {
            NoteDetails::create([
                'name' => $this->noteDetailsName,
                'note_id' => $this->noteId,
            ]);
            session()->flash('success', __('items.note_detail_created_successfully'));
        }

        // Clear cache to ensure filters show updated data
        \Cache::forget('note_groups');
        \Cache::forget('note_categories');

        $this->showModal = false;
        $this->dispatch('closeModal');
        $this->loadNoteDetails();
    }

    public function deleteNoteDetails(NoteDetails $noteDetails)
    {
        $permission = 'delete ' . $this->getPermissionPrefix();
        abort_unless(Auth::user()->can($permission), 403);

        // منع الحذف لو المجموعة/التصنيف مرتبطة بأصناف
        $linkedItemsCount = \DB::table('item_notes')
            ->where('note_id', $this->noteId)
            ->where('note_detail_name', $noteDetails->name)
            ->count();

        if ($linkedItemsCount > 0) {
            session()->flash('error', __('items.cannot_delete_note_detail_linked_to_items', ['count' => $linkedItemsCount]));
            return;
        }

        $noteDetails->delete();

        // Clear cache to ensure filters show updated data
        \Cache::forget('note_groups');
        \Cache::forget('note_categories');

        session()->flash('success', __('items.note_detail_deleted_successfully'));
        $this->loadNoteDetails();
    }
}; ?>

<div>
    <div class="row">
        @if (session()->has('success'))
            <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                {{ session('error') }}
            </div>
        @endif
        <div class="col-lg-12">

            @php
                $permissionPrefix = match ($noteId) {
                    1 => 'groups',
                    2 => 'Categories',
                    default => 'items',
                };
            @endphp

            <div class="card">
                <div class="card-header">
                    @can('create ' . $permissionPrefix)
                        <button wire:click="createNoteDetails" type="button" class="btn btn-main font-hold fw-bold ">
                            {{ __('items.add_new') }}
                            <i class="fas fa-plus me-2"></i>
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-striped text-center mb-0" style="min-width: 1200px;">
                            <thead class="table-light align-middle">

                                <tr>
                                    <th class="font-hold fw-bold">#</th>
                                    <th class="font-hold fw-bold">{{ __('common.name') }}</th>
                                    @php
                                        $permissionPrefix = match ($noteId) {
                                            1 => 'groups',
                                            2 => 'Categories',
                                            default => 'items',
                                        };
                                    @endphp
                                    @canany(['edit ' . $permissionPrefix, 'delete ' . $permissionPrefix])
                                        <th class="font-hold fw-bold">{{ __('common.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($noteDetails as $noteDetail)
                                    <tr>
                                        <td class="font-hold fw-bold">{{ $loop->iteration }}</td>
                                        <td class="font-hold fw-bold">{{ $noteDetail->name }}</td>

                                        @php
                                            $permissionPrefix = match ($noteId) {
                                                1 => 'groups',
                                                2 => 'Categories',
                                                default => 'items',
                                            };
                                        @endphp
                                        @canany(['edit ' . $permissionPrefix, 'delete ' . $permissionPrefix])
                                            <td>
                                                @can('edit ' . $permissionPrefix)
                                                    <a wire:click="editNoteDetails({{ $noteDetail->id }})"
                                                        class='btn btn-success m-1 btn-icon-square-sm'>
                                                        <i class="las la-edit fa-lg"></i>
                                                    </a>
                                                @endcan

                                                @can('delete ' . $permissionPrefix)
                                                    <a wire:click="deleteNoteDetails({{ $noteDetail->id }})"
                                                        onclick="confirm('{{ __('items.confirm_delete') }}') || event.stopImmediatePropagation()"
                                                        class="btn btn-danger m-1 btn-icon-square-sm">
                                                        <i class="las la-trash fa-lg"></i>
                                                    </a>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    @php
                                        $permissionPrefix = match ($noteId) {
                                            1 => 'groups',
                                            2 => 'Categories',
                                            default => 'items',
                                        };
                                        $colspan =
                                            auth()
                                                ->user()
                                                ->can('edit ' . $permissionPrefix) ||
                                            auth()
                                                ->user()
                                                ->can('delete ' . $permissionPrefix)
                                                ? '3'
                                                : '2';
                                    @endphp
                                    <tr>
                                        <td colspan="{{ $colspan }}" class="text-center font-hold fw-bold">
                                            {{ __('items.no_data') }}
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Note Details Modal -->
    <div class="modal fade" wire:ignore.self id="noteDetailsModal" tabindex="-1"
        aria-labelledby="noteDetailsModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-hold fw-bold" id="noteDetailsModalLabel">
                        {{ $isNoteDetailsEdit ? __('items.edit_note_detail') : __('items.add_note_detail_to', ['name' => $parentNoteName ?? '']) }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="saveNoteDetails">
                        <div class="mb-3">
                            <label for="noteDetailsName"
                                class="form-label font-hold fw-bold">{{ __('common.name') }}<span
                                    class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control @error('noteDetailsName') is-invalid @enderror font-hold fw-bold"
                                id="noteDetailsName" wire:model="noteDetailsName">
                            @error('noteDetailsName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                            <button type="submit" class="btn btn-main">{{ __('common.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('livewire:initialized', () => {
            let modalInstance = null;
            const noteDetailsModalElement = document.getElementById('noteDetailsModal');

            Livewire.on('showModal', () => {
                if (modalElement) {
                    // Always get or create the instance for the specific modal
                    modalInstance = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(
                        modalElement);
                    modalInstance.show();
                }
            });

            Livewire.on('showNoteDetailsModal', () => {
                if (noteDetailsModalElement) {
                    // Always get or create the instance for the specific modal
                    modalInstance = bootstrap.Modal.getInstance(noteDetailsModalElement) || new bootstrap
                        .Modal(noteDetailsModalElement);
                    modalInstance.show();
                }
            });

            Livewire.on('closeModal', () => {
                if (modalInstance) {
                    modalInstance.hide();
                }
            });

            if (modalElement) {
                modalElement.addEventListener('hidden.bs.modal', function() {
                    // 'this' refers to modalElement here
                    const bsInstance = bootstrap.Modal.getInstance(this);
                    // Only nullify modalInstance if it was tracking this specific modal
                    if (modalInstance === bsInstance) {
                        modalInstance = null;
                    }
                });
            }

            if (noteDetailsModalElement) {
                noteDetailsModalElement.addEventListener('hidden.bs.modal', function() {
                    // 'this' refers to noteDetailsModalElement here
                    const bsInstance = bootstrap.Modal.getInstance(this);
                    // Only nullify modalInstance if it was tracking this specific modal
                    if (modalInstance === bsInstance) {
                        modalInstance = null;
                    }
                });
            }
        });
    </script>
</div>
