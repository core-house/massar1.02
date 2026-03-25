<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Modules\POS\Models\Driver;

new class extends Component {
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public string $phone = '';
    public string $vehicle_type = '';
    public bool $is_available = true;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\Computed]
    public function drivers()
    {
        return Driver::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'phone', 'vehicle_type', 'editingId']);
        $this->is_available = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $driver = Driver::findOrFail($id);
        $this->editingId = $driver->id;
        $this->name = $driver->name;
        $this->phone = $driver->phone ?? '';
        $this->vehicle_type = $driver->vehicle_type ?? '';
        $this->is_available = (bool) $driver->is_available;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:100',
        ]);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'vehicle_type' => $this->vehicle_type,
            'is_available' => $this->is_available ? 1 : 0,
        ];

        if ($this->editingId) {
            Driver::where('id', $this->editingId)->update($data);
        } else {
            Driver::create($data);
        }

        $this->showModal = false;
        $this->reset(['name', 'phone', 'vehicle_type', 'editingId']);
        $this->is_available = true;
        session()->flash('driver_success', __('pos.saved_successfully'));
    }

    public function delete(int $id): void
    {
        Driver::findOrFail($id)->delete();
        session()->flash('driver_success', __('pos.deleted_successfully'));
    }
}; ?>

<div>
    @if (session('driver_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('driver_success') }}
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
                    <th>{{ __('pos.phone') ?? 'الهاتف' }}</th>
                    <th>{{ __('pos.vehicle_type') ?? 'نوع المركبة' }}</th>
                    <th>{{ __('pos.status') ?? 'الحالة' }}</th>
                    <th width="150px">{{ __('pos.action') ?? 'تحكم' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->drivers as $i => $driver)
                    <tr wire:key="driver-{{ $driver->id }}">
                        <td>{{ $this->drivers->firstItem() + $i }}</td>
                        <td>{{ $driver->name }}</td>
                        <td>{{ $driver->phone ?? '-' }}</td>
                        <td>{{ $driver->vehicle_type ?? '-' }}</td>
                        <td>
                            @if ($driver->is_available)
                                <span class="badge bg-success">{{ __('pos.active') ?? 'نشط' }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('pos.inactive') ?? 'غير نشط' }}</span>
                            @endif
                        </td>
                        <td>
                            <button wire:click="openEdit({{ $driver->id }})" class="btn btn-primary btn-sm mx-1">
                                <i class="las la-edit"></i>
                            </button>
                            <button wire:click="delete({{ $driver->id }})"
                                    wire:confirm="{{ __('pos.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}"
                                    class="btn btn-danger btn-sm mx-1">
                                <i class="las la-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">{{ __('pos.no_records_found') ?? 'لا توجد بيانات' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $this->drivers->links() }}
    </div>

    {{-- Modal --}}
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
                            <label class="form-label">{{ __('pos.phone') ?? 'الهاتف' }}</label>
                            <input wire:model="phone" type="text" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('pos.vehicle_type') ?? 'نوع المركبة' }}</label>
                            <input wire:model="vehicle_type" type="text" class="form-control">
                        </div>
                        <div class="mb-3 form-check form-switch">
                            <input wire:model="is_available" class="form-check-input" type="checkbox" id="driver_is_available">
                            <label class="form-check-label" for="driver_is_available">{{ __('pos.active') ?? 'نشط' }}</label>
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
