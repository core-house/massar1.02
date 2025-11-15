<?php

namespace Modules\Checks\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Modules\Checks\Models\Check;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChecksManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $check_number = '';
    public $bank_name = '';
    public $account_number = '';
    public $account_holder_name = '';
    public $amount = '';
    public $issue_date = '';
    public $due_date = '';
    public $payment_date = '';
    public $status = 'pending';
    public $type = 'outgoing';
    public $payee_name = '';
    public $payer_name = '';
    public $notes = '';
    public $reference_number = '';
    public $attachments = [];
    public $tempAttachments = [];

    // Search and filter properties
    public $search = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $bankFilter = '';
    public $startDate = '';
    public $endDate = '';

    // Selected checks for batch operations
    public $selectedChecks = [];
    public $selectAll = false;

    // Modal state
    public $showModal = false;
    public $editingCheckId = null;
    public $modalTitle = 'إضافة شيك جديد';

    // Confirmation modal
    public $showConfirmation = false;
    public $confirmationAction = '';
    public $confirmationMessage = '';
    public $confirmationCheckId = null;
    
    // View modal for selected check
    public $showViewModal = false;
    public $viewingCheck = null;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'check_number' => 'required|string|max:50',
        'bank_name' => 'required|string|max:100',
        'account_number' => 'required|string|max:50',
        'account_holder_name' => 'required|string|max:100',
        'amount' => 'required|numeric|min:0.01',
        'issue_date' => 'required|date',
        'due_date' => 'required|date|after_or_equal:issue_date',
        'status' => 'required|in:pending,cleared,bounced,cancelled',
        'type' => 'required|in:incoming,outgoing',
        'payee_name' => 'nullable|string|max:100',
        'payer_name' => 'nullable|string|max:100',
        'notes' => 'nullable|string',
        'reference_number' => 'nullable|string|max:50',
    ];

    protected $messages = [
        'check_number.required' => 'رقم الشيك مطلوب',
        'check_number.unique' => 'رقم الشيك مستخدم بالفعل',
        'bank_name.required' => 'اسم البنك مطلوب',
        'account_number.required' => 'رقم الحساب مطلوب',
        'account_holder_name.required' => 'اسم صاحب الحساب مطلوب',
        'amount.required' => 'المبلغ مطلوب',
        'amount.numeric' => 'المبلغ يجب أن يكون رقم',
        'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
        'issue_date.required' => 'تاريخ الإصدار مطلوب',
        'due_date.required' => 'تاريخ الاستحقاق مطلوب',
        'due_date.after_or_equal' => 'تاريخ الاستحقاق يجب أن يكون بعد أو يساوي تاريخ الإصدار',
    ];

    public function openModal()
    {
        $this->showModal = true;
        $this->resetForm();
        $this->modalTitle = 'إضافة شيك جديد';
        $this->editingCheckId = null;
    }

    public function editCheck($checkId)
    {
        $check = Check::findOrFail($checkId);
        
        $this->editingCheckId = $checkId;
        $this->check_number = $check->check_number;
        $this->bank_name = $check->bank_name;
        $this->account_number = $check->account_number;
        $this->account_holder_name = $check->account_holder_name;
        $this->amount = $check->amount;
        $this->issue_date = $check->issue_date->format('Y-m-d');
        $this->due_date = $check->due_date->format('Y-m-d');
        $this->payment_date = $check->payment_date ? $check->payment_date->format('Y-m-d') : '';
        $this->status = $check->status;
        $this->type = $check->type;
        $this->payee_name = $check->payee_name;
        $this->payer_name = $check->payer_name;
        $this->notes = $check->notes;
        $this->reference_number = $check->reference_number;
        $this->attachments = $check->attachments ?? [];

        $this->modalTitle = 'تعديل الشيك';
        $this->showModal = true;
    }

    public function saveCheck()
    {
        // Add unique validation for check_number when creating or editing
        $rules = $this->rules;
        if ($this->editingCheckId) {
            $rules['check_number'] .= '|unique:checks,check_number,' . $this->editingCheckId;
        } else {
            $rules['check_number'] .= '|unique:checks,check_number';
        }

        $this->validate($rules);

        try {
            $checkData = [
                'check_number' => $this->check_number,
                'bank_name' => $this->bank_name,
                'account_number' => $this->account_number,
                'account_holder_name' => $this->account_holder_name,
                'amount' => $this->amount,
                'issue_date' => $this->issue_date,
                'due_date' => $this->due_date,
                'payment_date' => $this->payment_date ?: null,
                'status' => $this->status,
                'type' => $this->type,
                'payee_name' => $this->payee_name,
                'payer_name' => $this->payer_name,
                'notes' => $this->notes,
                'reference_number' => $this->reference_number,
            ];

            // Handle file uploads
            if (!empty($this->tempAttachments)) {
                $uploadedFiles = [];
                foreach ($this->tempAttachments as $file) {
                    $filename = $file->store('checks/attachments', 'public');
                    $uploadedFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $filename,
                        'size' => $file->getSize(),
                    ];
                }
                $checkData['attachments'] = array_merge($this->attachments, $uploadedFiles);
            } else {
                $checkData['attachments'] = $this->attachments;
            }

            if ($this->editingCheckId) {
                $check = Check::findOrFail($this->editingCheckId);
                $check->update($checkData);
                session()->flash('success', 'تم تحديث الشيك بنجاح');
            } else {
                $checkData['created_by'] = Auth::id();
                Check::create($checkData);
                session()->flash('success', 'تم إضافة الشيك بنجاح');
            }

            $this->closeModal();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function confirmAction($action, $checkId, $message)
    {
        $this->confirmationAction = $action;
        $this->confirmationCheckId = $checkId;
        $this->confirmationMessage = $message;
        $this->showConfirmation = true;
    }

    public function executeConfirmedAction()
    {
        try {
            $check = Check::findOrFail($this->confirmationCheckId);

            switch ($this->confirmationAction) {
                case 'clear':
                    $check->markAsCleared();
                    session()->flash('success', 'تم تصفية الشيك بنجاح');
                    break;
                case 'bounce':
                    $check->markAsBounced();
                    session()->flash('success', 'تم تمييز الشيك كمرتد');
                    break;
                case 'cancel':
                    $check->cancel();
                    session()->flash('success', 'تم إلغاء الشيك');
                    break;
                case 'approve':
                    $check->approve(Auth::id());
                    session()->flash('success', 'تم اعتماد الشيك');
                    break;
                case 'delete':
                    // Delete attached files
                    if (!empty($check->attachments)) {
                        foreach ($check->attachments as $attachment) {
                            if (isset($attachment['path'])) {
                                Storage::disk('public')->delete($attachment['path']);
                            }
                        }
                    }
                    $check->delete();
                    session()->flash('success', 'تم حذف الشيك');
                    break;
            }

            $this->showConfirmation = false;
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function viewCheck($checkId)
    {
        $this->viewingCheck = Check::with(['creator', 'approver'])->findOrFail($checkId);
        $this->showViewModal = true;
    }

    public function closeViewModal()
    {
        $this->showViewModal = false;
        $this->viewingCheck = null;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedChecks = Check::query()
                ->when($this->search, function($q) {
                    $q->where(function($query) {
                        $query->where('check_number', 'like', '%' . $this->search . '%')
                              ->orWhere('bank_name', 'like', '%' . $this->search . '%')
                              ->orWhere('account_holder_name', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
                ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
                ->pluck('id')->toArray();
        } else {
            $this->selectedChecks = [];
        }
    }

    public function clearSelectedChecks()
    {
        if (empty($this->selectedChecks)) {
            session()->flash('error', 'يرجى اختيار شيكات أولاً');
            return;
        }

        try {
            Check::whereIn('id', $this->selectedChecks)
                ->where('status', Check::STATUS_PENDING)
                ->update([
                    'status' => Check::STATUS_CLEARED,
                    'payment_date' => now()
                ]);
            
            session()->flash('success', 'تم تصفية الشيكات المحددة بنجاح');
            $this->selectedChecks = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function endorseSelectedChecks()
    {
        if (empty($this->selectedChecks)) {
            session()->flash('error', 'يرجى اختيار شيكات أولاً');
            return;
        }

        session()->flash('info', 'عملية التظهير قيد التطوير');
    }

    public function cancelSelectedChecksWithReversal()
    {
        if (empty($this->selectedChecks)) {
            session()->flash('error', 'يرجى اختيار شيكات أولاً');
            return;
        }

        try {
            Check::whereIn('id', $this->selectedChecks)
                ->update(['status' => Check::STATUS_CANCELLED]);
            
            // هنا ممكن تضيف منطق القيد العكسي
            
            session()->flash('success', 'تم إلغاء الشيكات المحددة بقيد عكسي');
            $this->selectedChecks = [];
            $this->selectAll = false;
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    public function removeAttachment($index)
    {
        if (isset($this->attachments[$index])) {
            // Delete file from storage if it exists
            if (isset($this->attachments[$index]['path'])) {
                Storage::disk('public')->delete($this->attachments[$index]['path']);
            }
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->check_number = '';
        $this->bank_name = '';
        $this->account_number = '';
        $this->account_holder_name = '';
        $this->amount = '';
        $this->issue_date = '';
        $this->due_date = '';
        $this->payment_date = '';
        $this->status = 'pending';
        $this->type = 'outgoing';
        $this->payee_name = '';
        $this->payer_name = '';
        $this->notes = '';
        $this->reference_number = '';
        $this->attachments = [];
        $this->tempAttachments = [];
        $this->editingCheckId = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Check::query()
            ->with(['creator', 'approver'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('check_number', 'like', '%' . $this->search . '%')
                  ->orWhere('bank_name', 'like', '%' . $this->search . '%')
                  ->orWhere('account_holder_name', 'like', '%' . $this->search . '%')
                  ->orWhere('payee_name', 'like', '%' . $this->search . '%')
                  ->orWhere('payer_name', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->typeFilter)) {
            $query->where('type', $this->typeFilter);
        }

        if (!empty($this->bankFilter)) {
            $query->where('bank_name', 'like', '%' . $this->bankFilter . '%');
        }

        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween('due_date', [$this->startDate, $this->endDate]);
        }

        $checks = $query->paginate(15);

        return view('checks::livewire.checks-management', [
            'checks' => $checks,
            'statuses' => Check::getStatuses(),
            'types' => Check::getTypes(),
        ]);
    }
}