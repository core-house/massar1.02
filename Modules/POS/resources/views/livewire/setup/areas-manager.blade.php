<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Modules\POS\Models\DeliveryArea;

new class extends Component {
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public float|string $delivery_fee = 0;
    public bool $is_active = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\Computed]
    public function areas()
    {
        return DeliveryArea::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'editingId']);
        $this->delivery_fee = 0;
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $area = DeliveryArea::findOrFail($id);
        $this->editingId = $area->id;
        $this->name = $area->name;
        $this->delivery_fee = $area->delivery_fee;
        $this->is_active = (bool) $area->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
        ]);

        $data = [
            'name' => $this->name,
            'delivery_fee' => $this->delivery_fee,
            'is_active' => $this->is_active ? 1 : 0,
        ];

        if ($this->editingId) {
            DeliveryArea::where('id', $this->editingId)->update($data);
        } else {
            DeliveryArea::create($data);
        }

        $this->showModal = false;
        $this->reset(['name', 'editingId']);
        $this->delivery_fee = 0;
        $this->is_active = true;
        session()->flash('area_success', __('pos.saved_successfully'));
    }

    public function delete(int $id): void
    {
        DeliveryArea::findOrFail($id)->delete();
        session()->flash('area_success', __('pos.deleted_successfully'));
    }
}; ?>

<div>
    @if (session('area_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('area_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <input wire:model.live.debounce.300ms="search"
               type="text"
               class="form-control w-auto"
               placeholder="{{ __('pos.search') ?? 'بحث...' }}">
        <button wire:click="openCreate" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> {{ __('pos.add_new') ?? 'إضافة جديد' }}
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('pos.name') ?? 'الاسم' }}</th>
                    <th>{{ __('pos.delivery_fee') ?? 'رسوم التوصيل' }}</th>
                    <th>{{ __('pos.status') ?? 'الحالة' }}</th>
                    <th width="150px">{{ __('pos.action') ?? 'تحكم' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->areas as $i => $area)
                    <tr wire:key="area-{{ $area->id }}">
                        <td>{{ $this->areas->firstItem() + $i }}</td>
                        <td>{{ $area->name }}</td>
                        <td>{{ number_format((float)$area->delivery_fee, 2) }}</td>
                        <td>
                            @if ($area->is_active)
                                <span class="badge bg-success">{{ __('pos.active') ?? 'نشط' }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('pos.inactive') ?? 'غير نشط' }}</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="openEdit({{ $area->id }})" class="btn btn-primary btn-sm mx-1">
                                <i class="las la-edit"></i>
                            </button>
                            <button wire:click="delete({{ $area->id }})"
                                    wire:confirm="{{ __('pos.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}"
                                    class="btn btn-danger btn-sm mx-1">
                                <i class="las la-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">{{ __('pos.no_records_found') ?? 'لا توجد بيانات' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $this->areas->links() }}
    </div>

    @if ($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5)">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit="save">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editingId ? (__('pos.edit') ?? 'تعديل') : (__('pos.add_new') ?? 'إضافة جديد') }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">{{ __('pos.name') ?? 'الاسم' }} <span class="text-danger">*</span></label>
                            <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('pos.delivery_fee') ?? 'رسوم التوصيل' }} <span class="text-danger">*</span></label>
                            <input wire:model="delivery_fee" type="number" step="0.01" class="form-control @error('delivery_fee') is-invalid @enderror">
                            @error('delivery_fee') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input wire:model="is_active" class="form-check-input" type="checkbox" id="area_is_active">
                            <label class="form-check-label" for="area_is_active">{{ __('pos.active') ?? 'نشط' }}</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('pos.save') ?? 'حفظ' }}</span>
                            <span wire:loading>{{ __('pos.saving_btn') ?? 'جاري الحفظ...' }}</span>
                        </button>
                        <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">
                            {{ __('pos.cancel') ?? 'إلغاء' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
