<?php

use Livewire\Volt\Component;
use App\Models\ContractType;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $contractTypeId = null;
    public $name = '';
    public $description = '';
    public $showModal = false;
    public $isEdit = false;
    public string $search = '';

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function with(): array
    {
        return [
            'contractTypes' => ContractType::where('name', 'like', '%' . $this->search . '%')
                ->orderByDesc('id')
                ->paginate(10),
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetValidation();
        $this->reset(['name', 'description', 'contractTypeId']);
        $this->isEdit = false;
        $this->showModal = true;
        $this->dispatch('show-create-modal');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $contractType = ContractType::findOrFail($id);
        $this->contractTypeId = $contractType->id;
        $this->name = $contractType->name;
        $this->description = $contractType->description;
        $this->isEdit = true;
        $this->showModal = true;
        $this->dispatch('show-edit-modal');
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->isEdit) {
            ContractType::find($this->contractTypeId)->update($validated);
            session()->flash('success', __('تم تحديث نوع العقد بنجاح'));
        } else {
            ContractType::create($validated);
            session()->flash('success', __('تم إنشاء نوع العقد بنجاح'));
        }

        $this->showModal = false;
        $this->reset(['name', 'description', 'contractTypeId', 'isEdit']);
        $this->dispatch('hide-modals');
    }

    public function delete($id)
    {
        ContractType::findOrFail($id)->delete();
        session()->flash('success', __('تم حذف نوع العقد بنجاح'));
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['name', 'description', 'contractTypeId', 'isEdit']);
        $this->dispatch('hide-modals');
    }
}; ?>

<div class="container-fluid">
    @if (session()->has('success'))
        <div class="alert alert-success" x-data x-init="setTimeout(() => $el.remove(), 3000)">
            {{ session('success') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-1">
        @can('إضافة انواع العقود')
            <button class="btn btn-primary" wire:click="create">
                <i class="las la-plus"></i> إضافة نوع عقد جديد
            </button>
        @endcan
        <div class="mb-3">
            <input type="text" wire:model.live="search" class="form-control" placeholder="بحث بالاسم...">
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">
                <x-table-export-actions table-id="contracts-type-table" filename="contracts-type-table"
                    excel-label="تصدير Excel" pdf-label="تصدير PDF" print-label="طباعة" />

                <table id="contracts-type-table" class="table table-striped text-center mb-0"
                    style="min-width: 1200px;">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>الوصف</th>
                            @canany(['حذف انواع العقود', 'تعديل انواع العقود'])
                                <th>الإجراءات</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contractTypes as $index => $type)
                            <tr>
                                <td>{{ $contractTypes->firstItem() + $index }}</td>
                                <td>{{ $type->name }}</td>
                                <td>{{ $type->description }}</td>

                                @canany(['حذف انواع العقود', 'تعديل انواع العقود'])
                                    <td>
                                        @can('تعديل انواع العقود')
                                            <button class="btn btn-success btn-sm me-1" wire:click="edit({{ $type->id }})">
                                                <i class="las la-edit"></i>
                                            </button>
                                        @endcan
                                        @can('حذف انواع العقود')
                                            <button class="btn btn-danger btn-sm" wire:click="delete({{ $type->id }})"
                                                onclick="return confirm('هل أنت متأكد أنك تريد الحذف؟')">
                                                <i class="las la-trash"></i>
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
                                        لا توجد أنواع عقود
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $contractTypes->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    <div wire:ignore.self class="modal fade" id="contractTypeModal" tabindex="-1" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <form wire:submit.prevent="save">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'تعديل نوع العقد' : 'إضافة نوع عقد جديد' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">الاسم</label>
                            <input wire:model="name" type="text" class="form-control" id="name">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea wire:model="description" class="form-control" id="description" rows="3"></textarea>
                            @error('description')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEdit ? 'تحديث' : 'حفظ' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@script
    <script>
        document.addEventListener('livewire:initialized', () => {
            const modal = new bootstrap.Modal(document.getElementById('contractTypeModal'));

            window.addEventListener('show-create-modal', event => {
                modal.show();
            });

            window.addEventListener('show-edit-modal', event => {
                modal.show();
            });

            window.addEventListener('hide-modals', event => {
                modal.hide();
            });
        });
    </script>
@endscript
