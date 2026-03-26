<?php

use Livewire\Volt\Component;
use App\Models\Price;
use Illuminate\Support\Facades\Validator;

new class extends Component {
    public $prices;
    public $name;
    public $priceId;
    public $showModal = false;
    public $isEdit = false;

    public function rules()
    {
        return [
            'name' => 'required|string|max:60|unique:prices,name,' . $this->priceId,
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
        $this->prices = Price::all();
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['name', 'priceId']);
        $this->isEdit = false;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function edit($priceId)
    {
        $this->resetValidation();
        $price = Price::findOrFail($priceId);
        $this->priceId = $price->id;
        $this->name = $price->name;
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('showModal');
    }

    public function save()
    {
        $validated = $this->validate();
        if ($this->isEdit) {
            Price::find($this->priceId)->update($validated);
            session()->flash('success', __('items.price_updated_successfully'));
        } else {
            Price::create([
                'name' => $this->name,
            ]);
            session()->flash('success', __('items.price_created_successfully'));
        }

        $this->showModal = false;
        $this->dispatch('closeModal');
        $this->prices = Price::latest()->get();
    }

    public function delete($priceId)
    {
        try {
            $price = Price::findOrFail($priceId);
            if ($price->items()->count() > 0) {
                session()->flash('error', __('items.price_has_items_error'));
                return;
            }
            $price->delete();
            session()->flash('success', __('items.price_deleted_successfully'));
            $this->prices = Price::latest()->get();
        } catch (\Exception $e) {
            session()->flash('error', __('items.price_has_items_error'));
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
                <div class="card-header">
                    @can('create prices')
                        <button wire:click="create" type="button" class="btn btn-main font-hold fw-bold">
                            {{ __('items.add_price') }}
                            <i class="fas fa-plus me-2"></i>
                        </button>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-striped mb-0 text-center" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th class="font-hold fw-bold">#</th>
                                    <th class="font-hold fw-bold">{{ __('common.name') }}</th>
                                    @canany(['edit prices', 'delete prices'])
                                        <th class="font-hold fw-bold">{{ __('common.actions') }}</th>
                                    @endcanany

                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prices as $price)
                                    <tr>
                                        <td class="font-hold fw-bold">{{ $loop->iteration }}</td>
                                        <td class="font-hold fw-bold">{{ $price->name }}</td>
                                        @canany(['edit prices', 'delete prices'])
                                            <td>
                                                @can('edit prices')
                                                    <button type="button" wire:click="edit({{ $price->id }})" class="btn btn-success btn-sm">
                                                        <i class="las la-edit fa-lg"></i>
                                                    </button>
                                                @endcan
                                                @can('delete prices')
                                                    <button type="button" wire:click="delete({{ $price->id }})" 
                                                        wire:confirm="{{ __('items.confirm_delete_price') }}"
                                                        class="btn btn-danger btn-sm">
                                                        <i class="las la-trash fa-lg"></i>
                                                    </button>
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
                                                {{ __('items.no_prices_found') }}
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
    <div class="modal fade" wire:ignore.self id="priceModal" tabindex="-1" aria-labelledby="priceModalLabel"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-hold fw-bold" id="priceModalLabel">
                        {{ $isEdit ? __('items.edit_price') : __('items.add_price') }}
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
            const modalElement = document.getElementById('priceModal');

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
