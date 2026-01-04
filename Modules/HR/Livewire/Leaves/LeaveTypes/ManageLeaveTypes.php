<?php

declare(strict_types=1);

namespace Modules\HR\Livewire\Leaves\LeaveTypes;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Modules\HR\Models\LeaveType;

#[Title('إدارة أنواع الإجازات')]
class ManageLeaveTypes extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $name = '';
    public string $code = '';
    public bool $is_paid = false;
    public bool $requires_approval = false;
    public int $max_per_request_days = 0;
    public bool $showModal = false;
    public bool $isEdit = false;
    public string $search = '';
    public ?int $leaveTypeId = null;

    /**
     * Validation rules.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:leave_types,name,' . $this->leaveTypeId,
            'code' => 'required|string|max:255|unique:leave_types,code,' . $this->leaveTypeId,
            'is_paid' => 'required|boolean',
            'requires_approval' => 'required|boolean',
            'max_per_request_days' => 'required|integer|min:0',
        ];
    }

    /**
     * Reset pagination when search changes.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Get filtered leave types list.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, LeaveType>
     */
    public function getLeaveTypesProperty()
    {
        return LeaveType::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderByDesc('id')
            ->get();
    }

    public function openModal(): void
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->name = '';
        $this->code = '';
        $this->is_paid = false;
        $this->requires_approval = false;
        $this->max_per_request_days = 0;
        $this->leaveTypeId = null;
        $this->resetErrorBag();
    }

    /**
     * Open edit modal and load leave type data.
     */
    public function edit(int $id): void
    {
        $leaveType = LeaveType::findOrFail($id);
        $this->leaveTypeId = $leaveType->id;
        $this->name = $leaveType->name;
        $this->code = $leaveType->code;
        $this->is_paid = (bool) $leaveType->is_paid;
        $this->requires_approval = (bool) $leaveType->requires_approval;
        $this->max_per_request_days = (int) $leaveType->max_per_request_days;
        $this->isEdit = true;
        $this->showModal = true;
    }

    /**
     * Save leave type (create or update).
     */
    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'is_paid' => $this->is_paid,
            'requires_approval' => $this->requires_approval,
            'max_per_request_days' => $this->max_per_request_days,
        ];

        if ($this->isEdit) {
            LeaveType::findOrFail($this->leaveTypeId)->update($data);
            session()->flash('message', __('hr.leave_type_updated_successfully'));
        } else {
            LeaveType::create($data);
            session()->flash('message', __('hr.leave_type_created_successfully'));
        }

        $this->closeModal();
    }

    /**
     * Delete leave type.
     */
    public function delete(int $id): void
    {
        LeaveType::findOrFail($id)->delete();
        session()->flash('message', __('hr.leave_type_deleted_successfully'));
    }

    public function render()
    {
        return view('hr::livewire.hr-management.leaves.leave-types.manage-leave-types', [
            'leaveTypes' => $this->leaveTypes,
        ]);
    }
}
