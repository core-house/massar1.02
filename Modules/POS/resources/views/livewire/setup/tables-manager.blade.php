<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Modules\POS\Models\RestaurantTable;

new class extends Component {
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $name = '';
    public ?int $capacity = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\Computed]
    public function tables()
    {
        return RestaurantTable::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'capacity', 'editingId']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $table = RestaurantTable::findOrFail($id);
        $this->editingId = $table->id;
        $this->name = $table->name;
        $this->capacity = $table->capacity;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1',
        ]);

        $data = [
            'name' => $this->name,
            'capacity' => $this->capacity,
        ];

        if ($this->editingId) {
            RestaurantTable::where('id', $this->editingId)->update($data);
        } else {
            RestaurantTable::create($data);
        }

        $this->showModal = false;
        $this->reset(['name', 'capacity', 'editingId']);
        session()->flash('table_success', __('pos.saved_successfully'));
    }

    public function delete(int $id): void
    {
        RestaurantTable::findOrFail($id)->delete();
        session()->flash('table_success', __('pos.deleted_successfully'));
    }
}; ?>

<div>
    @if (session('table_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('table_success') }}
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
                    <th>{{ __('pos.seats') ?? 'عدد المقاعد' }}</th>
                    <th width="150px">{{ __('pos.action') ?? 'تحكم' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->tables as $i => $table)
                    <tr wire:key="table-{{ $table->id }}">
                        <td>{{ $this->tables->firstItem() + $i }}</td>
                        <td>{{ $table->name }}</td>
                        <td>{{ $table->capacity ?? '-' }}</td>
                        <td>
                            <button wire:click="openEdit({{ $table->id }})" class="btn btn-primary btn-sm mx-1">
                                <i class="las la-edit"></i>
                            </button>
                            <button wire:click="delete({{ $table->id }})"
                                    wire:confirm="{{ __('pos.confirm_delete') ?? 'هل أنت متأكد من الحذف؟' }}"
                                    class="btn btn-danger btn-sm mx-1">
                                <i class="las la-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">{{ __('pos.no_records_found') ?? 'لا توجد بيانات' }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $this->tables->links() }}
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
                            <label class="form-label">{{ __('pos.seats') ?? 'عدد المقاعد' }}</label>
                            <input wire:model="capacity" type="number" min="1" class="form-control">
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
