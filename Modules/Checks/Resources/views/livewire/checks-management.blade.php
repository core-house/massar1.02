<div>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="page-title">
                        <i class="fas fa-check-square"></i> إدارة الشيكات
                    </h2>
                    <button class="btn btn-primary" wire:click="openModal">
                        <i class="fas fa-plus"></i> إضافة شيك جديد
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">البحث</label>
                                <input type="text" class="form-control" wire:model.live="search" 
                                       placeholder="رقم الشيك، البنك، أو اسم صاحب الحساب">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">الحالة</label>
                                <select class="form-select" wire:model.live="statusFilter">
                                    <option value="">جميع الحالات</option>
                                    @foreach($statuses as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">النوع</label>
                                <select class="form-select" wire:model.live="typeFilter">
                                    <option value="">جميع الأنواع</option>
                                    @foreach($types as $key => $type)
                                        <option value="{{ $key }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">من تاريخ</label>
                                <input type="date" class="form-control" wire:model.live="startDate">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">إلى تاريخ</label>
                                <input type="date" class="form-control" wire:model.live="endDate">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checks Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>رقم الشيك</th>
                                        <th>البنك</th>
                                        <th>المبلغ</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th>النوع</th>
                                        <th>صاحب الحساب</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($checks as $check)
                                        <tr class="{{ $check->isOverdue() ? 'table-warning' : '' }}">
                                            <td>
                                                <strong>{{ $check->check_number }}</strong>
                                                @if($check->reference_number)
                                                    <br><small class="text-muted">مرجع: {{ $check->reference_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $check->bank_name }}
                                                <br><small class="text-muted">{{ $check->account_number }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($check->amount, 2) }} ر.س</strong>
                                            </td>
                                            <td>
                                                {{ $check->due_date->format('Y-m-d') }}
                                                @if($check->isOverdue())
                                                    <br><small class="text-danger">متأخر</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $check->status_color }}">
                                                    {{ $statuses[$check->status] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $check->type === 'incoming' ? 'success' : 'info' }}">
                                                    {{ $types[$check->type] }}
                                                </span>
                                            </td>
                                            <td>{{ $check->account_holder_name }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            wire:click="editCheck({{ $check->id }})"
                                                            title="تعديل">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    
                                                    @if($check->status === 'pending')
                                                        <button class="btn btn-sm btn-outline-success" 
                                                                wire:click="confirmAction('clear', {{ $check->id }}, 'هل أنت متأكد من تصفية هذا الشيك؟')"
                                                                title="تصفية">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-warning" 
                                                                wire:click="confirmAction('bounce', {{ $check->id }}, 'هل أنت متأكد من تمييز هذا الشيك كمرتد؟')"
                                                                title="مرتد">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                        </button>
                                                    @endif
                                                    
                                                    <button class="btn btn-sm btn-outline-danger" 
                                                            wire:click="confirmAction('delete', {{ $check->id }}, 'هل أنت متأكد من حذف هذا الشيك؟ لا يمكن التراجع عن هذا الإجراء.')"
                                                            title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">لا توجد شيكات مطابقة للبحث</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $checks->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Check Modal -->
    @if($showModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-check-square"></i> {{ $modalTitle }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>

                    <div class="modal-body">
                        @if (session()->has('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="saveCheck">
                            <div class="row">
                                <!-- Basic Information -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم الشيك <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('check_number') is-invalid @enderror"
                                           wire:model="check_number" required>
                                    @error('check_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اسم البنك <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror"
                                           wire:model="bank_name" required>
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم الحساب <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror"
                                           wire:model="account_number" required>
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">صاحب الحساب <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror"
                                           wire:model="account_holder_name" required>
                                    @error('account_holder_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" min="0.01" 
                                           class="form-control @error('amount') is-invalid @enderror"
                                           wire:model="amount" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">تاريخ الإصدار <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('issue_date') is-invalid @enderror"
                                           wire:model="issue_date" required>
                                    @error('issue_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">تاريخ الاستحقاق <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                           wire:model="due_date" required>
                                    @error('due_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">الحالة</label>
                                    <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                        @foreach($statuses as $key => $status)
                                            <option value="{{ $key }}">{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">النوع</label>
                                    <select class="form-select @error('type') is-invalid @enderror" wire:model="type">
                                        @foreach($types as $key => $type)
                                            <option value="{{ $key }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">تاريخ الدفع</label>
                                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                                           wire:model="payment_date">
                                    @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اسم المستفيد</label>
                                    <input type="text" class="form-control @error('payee_name') is-invalid @enderror"
                                           wire:model="payee_name">
                                    @error('payee_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اسم الدافع</label>
                                    <input type="text" class="form-control @error('payer_name') is-invalid @enderror"
                                           wire:model="payer_name">
                                    @error('payer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم المرجع</label>
                                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror"
                                           wire:model="reference_number">
                                    @error('reference_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">ملاحظات</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror"
                                              wire:model="notes" rows="3"></textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Attachments -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">المرفقات</label>
                                    <input type="file" class="form-control" wire:model="tempAttachments" multiple>
                                    
                                    @if(!empty($attachments))
                                        <div class="mt-2">
                                            <strong>الملفات المرفقة:</strong>
                                            <ul class="list-group">
                                                @foreach($attachments as $index => $attachment)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        {{ $attachment['name'] ?? 'ملف مرفق' }}
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                                wire:click="removeAttachment({{ $index }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="fas fa-times"></i> إلغاء
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveCheck">
                            <i class="fas fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Confirmation Modal -->
    @if($showConfirmation)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تأكيد العملية</h5>
                        <button type="button" class="btn-close" wire:click="showConfirmation = false"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ $confirmationMessage }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="showConfirmation = false">
                            إلغاء
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="executeConfirmedAction">
                            تأكيد
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading.flex class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" 
         style="background-color: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>