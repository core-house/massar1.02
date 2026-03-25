<?php

use Livewire\Volt\Component;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;

new class extends Component {
    public $units;
    public $name;
    public $unitId;
    public $search = '';
    public $showModal = false;
    public $isEdit = false;

    public function rules()
    {
        return [
            'name' => 'required|string|max:60|unique:units,name,' . $this->unitId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.name_required'),
            'name.string'   => __('validation.name_must_be_string'),
            'name.max'      => __('validation.name_max_length'),
            'name.unique'   => __('validation.name_already_exists'),
        ];
    }

    public function mount()
    {
        $this->loadUnits();
    }

    public function updatedSearch(): void
    {
        $this->loadUnits();
    }

    private function loadUnits(): void
    {
        $this->units = Unit::with('items')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->get();
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['name', 'unitId']);
        $this->isEdit = false;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    private function incrementLastUnitCode()
    {
        $lastUnit = Unit::orderByDesc('code')->first();
        $newCode = $lastUnit ? $lastUnit->code + 1 : 1;
        return $newCode;
    }

    public function edit(Unit $unit)
    {
        $this->resetValidation();
        $this->unitId = $unit->id;
        $this->name = $unit->name;
        $this->code = $unit->code;
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function save()
    {
        $validated = $this->validate();
        if ($this->isEdit) {
            Unit::find($this->unitId)->update($validated);
            session()->flash('success', __('items.unit_updated_successfully'));
        } else {
            Unit::create([
                'code' => $this->incrementLastUnitCode(),
                'name' => $this->name,
            ]);
            session()->flash('success', __('items.unit_created_successfully'));
        }

        $this->showModal = false;
        $this->dispatch('closeModal');
        $this->loadUnits();
    }

    public function delete(Unit $unit)
    {
        try {
            if ($unit->items->count() > 0) {
                session()->flash('error', __('items.unit_has_items_error'));
                return;
            }
            $unit->delete();
            session()->flash('success', __('items.unit_deleted_successfully'));
            $this->loadUnits();
        } catch (\Exception $e) {
            session()->flash('error', __('items.unit_has_items_error'));
        }
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

            <div class="card">

                <div class="card-header ">
                    <div class="d-flex row justify-content-between gap-2">
                    @can('create units')
                        <button wire:click="create" type="button" class="btn btn-main col-2 font-hold fw-bold">
                            {{ __('items.add_unit') }}
                            <i class="fas fa-plus me-2"></i>
                        </button>
                    @endcan
                    <input type="text" wire:model.live.debounce.300ms="search"
                        class="form-control font-hold"
                        placeholder="{{ __('common.search') }}..."
                        style="max-width: 280px;">
                </div>
            </div>
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-striped text-center  mb-0" style="min-width: 1200px;">
                            <thead class="table-light align-middle">
                                <tr>
                                    <th class="font-hold fw-bold">#</th>
                                    <th class="font-hold fw-bold">{{ __('common.code') }}</th>
                                    <th class="font-hold fw-bold">{{ __('common.name') }}</th>
                                    @canany(['edit units', 'delete units'])
                                        <th class="font-hold fw-bold">{{ __('common.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($units as $unit)
                                    <tr>

                                        <td class="font-hold fw-bold">{{ $loop->iteration }}</td>
                                        <td class="font-hold fw-bold">{{ $unit->code }}</td>
                                        <td class="font-hold fw-bold">{{ $unit->name }}</td>
                                        @canany(['edit units', 'delete units'])
                                            <td>
                                                @can('edit units')
                                                    <a wire:click="edit({{ $unit->id }})" class="btn btn-success btn-sm">
                                                        <i class="las la-edit fa-lg"></i>
                                                    </a>
                                                @endcan
                                                @can('delete units')
                                                    <a wire:click="delete({{ $unit->id }})" class="btn btn-danger btn-sm"
                                                        onclick="confirm('{{ __('items.confirm_delete_unit') }}') || event.stopImmediatePropagation()">
                                                        <i class="las la-trash fa-lg"></i>
                                                    </a>
                                                @endcan
                                            </td>
                                        @endcanany

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('items.no_units_found') }}
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

    <!-- Modal -->
    <div class="modal fade" wire:ignore.self id="unitModal" tabindex="-1" aria-labelledby="unitModalLabel"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-hold fw-bold" id="unitModalLabel">
                        {{ $isEdit ? __('items.edit_unit') : __('items.add_unit') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label for="name" class="form-label font-hold fw-bold">{{ __('common.name') }}<span class="text-danger">*</span></label>
                            <input type="text"
                                class="form-control @error('name') is-invalid @enderror font-hold fw-bold"
                                id="name" wire:model="name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
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
            const modalElement = document.getElementById('unitModal');

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

            // Optional: Reset modalInstance when modal is fully hidden
            modalElement.addEventListener('hidden.bs.modal', function() {
                modalInstance = null;
            });
        });
    </script>

</div>
