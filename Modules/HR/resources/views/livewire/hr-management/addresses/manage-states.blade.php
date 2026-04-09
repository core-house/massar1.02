<?php

declare(strict_types=1);

use Livewire\Volt\Component;
use Modules\HR\Models\State;
use Modules\HR\Models\Country;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

new class extends Component {
    use WithPagination;

    public string $title = '';
    public ?int $country_id = null;
    public ?int $stateId = null;
    public bool $showModal = false;
    public bool $isEdit = false;
    public string $search = '';

    /**
     * Get validation rules for state form.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:2|max:255|unique:states,title,' . $this->stateId,
            'country_id' => 'required|exists:countries,id',
        ];
    }

    /**
     * Initialize component on mount.
     */
    public function mount(): void
    {
        // Component initialized
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Get all countries for dropdown.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Country>
     */
    #[Computed]
    public function countries()
    {
        return Country::query()->orderBy('title')->get();
    }

    /**
     * Get filtered states list.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, State>
     */
    #[Computed]
    public function states()
    {
        return State::query()
            ->with('country')
            ->when($this->search, fn($query) => $query->where('title', 'like', "%{$this->search}%"))
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Open create modal and reset form.
     */
    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['title', 'country_id', 'stateId']);
        $this->isEdit = false;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    /**
     * Open edit modal and load state data.
     *
     * @param int $id
     */
    public function edit(int $id): void
    {
        $this->resetValidation();
        $state = State::findOrFail($id);
        $this->stateId = $state->id;
        $this->title = $state->title;
        $this->country_id = $state->country_id;
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    /**
     * Save state (create or update).
     */
    public function save(): void
    {
        $validated = $this->validate();

        if ($this->isEdit) {
            State::findOrFail($this->stateId)->update($validated);
            session()->flash('success', __('hr::hr.state_updated_successfully'));
        } else {
            State::create($validated);
            session()->flash('success', __('hr::hr.state_created_successfully'));
        }

        $this->showModal = false;
        $this->dispatch('closeModal');
        $this->reset(['title', 'country_id', 'stateId', 'isEdit']);
    }

    /**
     * Delete state.
     *
     * @param int $id
     */
    public function delete(int $id): void
    {
        $state = State::findOrFail($id);
        $state->delete();
        session()->flash('success', __('hr::hr.state_deleted_successfully'));
    }
}; ?>

<div class="states-management" style="font-family: 'Cairo', sans-serif; direction: rtl;">
    <div class="row">
        @if (session()->has('success'))
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    @can('create States')
                        <button wire:click="create" type="button" class="btn btn-main font-hold fw-bold">
                            <i class="fas fa-plus me-2"></i>
                            {{ __('hr::hr.add_state') }}
                        </button>
                    @endcan
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           class="form-control w-auto"
                           style="min-width: 200px;"
                           placeholder="{{ __('hr::hr.search_by_name') }}">
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th class="font-hold fw-bold">#</th>
                                    <th class="font-hold fw-bold">{{ __('hr::hr.name') }}</th>
                                    <th class="font-hold fw-bold">{{ __('hr::hr.country') }}</th>
                                    @canany(['edit States', 'delete States'])
                                        <th class="font-hold fw-bold">{{ __('hr::hr.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($this->states as $state)
                                    <tr>
                                        <td class="font-hold fw-bold text-center">{{ $loop->iteration }}</td>
                                        <td class="font-hold fw-bold text-center">{{ $state->title }}</td>
                                        <td class="font-hold fw-bold text-center">{{ $state->country->title ?? '-' }}</td>
                                        @canany(['edit States', 'delete States'])
                                            <td class="font-hold fw-bold text-center">
                                                <div class="btn-group" role="group">
                                                    @can('edit States')
                                                        <button type="button"
                                                                wire:click="edit({{ $state->id }})"
                                                                class="btn btn-success btn-sm"
                                                                title="{{ __('hr::hr.edit') }}">
                                                            <i class="las la-edit"></i>
                                                        </button>
                                                    @endcan
                                                    @can('delete States')
                                                        <button type="button"
                                                                wire:click="delete({{ $state->id }})"
                                                                wire:confirm="{{ __('hr::hr.are_you_sure_you_want_to_delete_this_state') }}"
                                                                class="btn btn-danger btn-sm"
                                                                title="{{ __('hr::hr.delete') }}">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()->canany(['edit States', 'delete States']) ? '4' : '3' }}"
                                            class="text-center font-hold fw-bold py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('hr::hr.no_states_found') }}
                                            </div>
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

    <!-- Modal (Create/Edit) -->
    <div class="modal fade"
         wire:ignore.self
         id="stateModal"
         tabindex="-1"
         aria-labelledby="stateModalLabel"
         aria-hidden="true"
         data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-hold fw-bold" id="stateModalLabel" style="color: white !important;">
                        {{ $isEdit ? __('hr::hr.edit_state') : __('hr::hr.add_state') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="title" class="form-label font-hold fw-bold">
                                {{ __('hr::hr.name') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror font-hold fw-bold"
                                   id="title"
                                   wire:model.blur="title"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="country_id" class="form-label font-hold fw-bold">
                                {{ __('hr::hr.country') }} <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('country_id') is-invalid @enderror font-hold fw-bold"
                                    id="country_id"
                                    wire:model.defer="country_id"
                                    required>
                                <option value="">{{ __('hr::hr.select_country') }}</option>
                                @foreach ($this->countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->title }}</option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal">
                                {{ __('hr::hr.cancel') }}
                            </button>
                            <button type="submit" class="btn btn-main">
                                {{ $isEdit ? __('hr::hr.update') : __('hr::hr.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            let modalInstance = null;
            const modalElement = document.getElementById('stateModal');

            if (!modalElement) {
                return;
            }

            Livewire.on('showModal', () => {
                if (!modalInstance) {
                    modalInstance = new bootstrap.Modal(modalElement);
                }
                modalInstance.show();
            });

            Livewire.on('closeModal', () => {
                if (modalInstance) {
                    modalInstance.hide();
                }
            });

            modalElement.addEventListener('hidden.bs.modal', () => {
                modalInstance = null;
                @this.call('$refresh');
            });
        });
    </script>
</div>
