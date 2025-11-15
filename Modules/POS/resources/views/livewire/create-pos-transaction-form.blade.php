<div class="container-fluid p-0 d-flex flex-column" style="height: 100vh; overflow: hidden; background: #f8f9fa; position: fixed; top: 0; left: 0; right: 0; bottom: 0;">
    {{-- رأس النظام --}}
    <div class="bg-primary text-white py-3 shadow-sm" x-data="{
        proId: @entangle('pro_id'),
        currentDate: '{{ now()->format('Y-m-d H:i') }}',
        cashierName: '{{ auth()->user()->name }}'
    }">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cash-register fs-2 me-3"></i>
                        <h2 class="mb-0">نظام نقاط البيع</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column align-items-end">
                        <span class="badge bg-light text-dark fs-6 mb-1" x-text="'فاتورة رقم: ' + proId"></span>
                        <span class="small" x-text="currentDate"></span>
                        <span class="small" x-text="'الكاشير: ' + cashierName"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- معلومات الفاتورة --}}
    <div class="bg-white shadow-sm border-bottom" x-data="{
        acc1Id: @entangle('acc1_id'),
        acc2Id: @entangle('acc2_id'),
        empId: @entangle('emp_id'),
        cashBoxId: @entangle('cash_box_id'),
        currentBalance: @entangle('currentBalance'),
        balanceAfterInvoice: @entangle('balanceAfterInvoice'),
        showBalance: @entangle('showBalance')
    }">
        <div class="container-fluid py-3">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-bold">العميل:</label>
                    <select x-model="acc1Id" class="form-select">
                        @foreach ($acc1List as $client)
                            <option value="{{ $client->id }}">{{ $client->aname }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-bold">المخزن:</label>
                    <select x-model="acc2Id" class="form-select">
                        @foreach ($acc2List as $store)
                            <option value="{{ $store->id }}">{{ $store->aname }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-bold">الموظف:</label>
                    <select x-model="empId" class="form-select">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->aname }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-bold">طريقة الدفع:</label>
                    <select x-model="cashBoxId" class="form-select">
                        @foreach ($cashAccounts as $account)
                            <option value="{{ $account->id }}">{{ $account->aname }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div x-show="showBalance" class="col-md-2">
                    <label class="form-label fw-bold">رصيد العميل:</label>
                    <div class="p-2 rounded" :class="currentBalance >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'">
                        <span class="fw-bold" x-text="currentBalance.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ريال'"></span>
                    </div>
                </div>
                
                <div x-show="showBalance" class="col-md-2">
                    <label class="form-label fw-bold">الرصيد بعد الفاتورة:</label>
                    <div class="p-2 rounded" :class="balanceAfterInvoice >= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'">
                        <span class="fw-bold" x-text="balanceAfterInvoice.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ريال'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
    <div class="container-fluid">
        <div class="row g-0 flex-grow-1" style="overflow: hidden;">
            {{-- الجانب الأيسر - قائمة الأصناف والبحث --}}
            <div class="col-lg-8 bg-white border-end d-flex flex-column" style="overflow: hidden;">
                <div class="p-3 flex-grow-1 d-flex flex-column" style="overflow: hidden; height: 100%;">
                    {{-- التصنيفات والمجموعات --}}
                    <div class="mb-4" x-data="{
                        selectedCategory: @entangle('selectedCategory'),
                        categories: @js($categories)
                    }">
                        <h5 class="mb-3 text-primary">التصنيفات</h5>
                        <div class="row g-2">
                            <template x-for="category in categories" :key="category.id">
                                <div class="col-md-3 col-sm-6">
                                    <button type="button" 
                                            @click="selectedCategory = category.id"
                                            :class="{'btn-primary': selectedCategory === category.id, 'btn-outline-primary': selectedCategory !== category.id}"
                                            class="btn w-100 d-flex flex-column align-items-center py-3">
                                        <i class="fas fa-tag mb-2"></i>
                                        <span x-text="category.name"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    {{-- البحث - صف واحد --}}
                    <div class="row g-3 mb-3">
                        {{-- بحث بالاسم --}}
                        <div class="col-md-8">
                            <div class="position-relative">
                                <input type="text" 
                                       wire:model.live.debounce.300ms="searchTerm"
                                       class="form-control form-control-lg"
                                       placeholder="ابحث عن صنف..."
                                       autocomplete="off">
                                <i class="fas fa-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                            </div>
                        </div>

                        {{-- بحث بالباركود --}}
                        <div class="col-md-4" x-data="{
                            barcodeTerm: @entangle('barcodeTerm'),
                            addItemByBarcode() {
                                if (this.barcodeTerm.trim()) {
                                    $wire.addItemByBarcode();
                                }
                            }
                        }">
                            <div class="input-group">
                                <input type="text" 
                                       x-model="barcodeTerm"
                                       @keydown.enter="addItemByBarcode()"
                                       @input.debounce.500ms="$wire.addItemByBarcode()"
                                       class="form-control form-control-lg"
                                       placeholder="امسح الباركود..."
                                       autocomplete="off">
                                <button type="button" @click="addItemByBarcode()" class="btn btn-primary btn-lg">
                                    <i class="fas fa-barcode"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- نتائج البحث - تظهر مباشرة تحت البحث --}}
                    @if($searchResults && count($searchResults) > 0)
                        <div class="mb-3">
                            <h6 class="text-primary mb-2">نتائج البحث</h6>
                            <div class="card">
                                <div class="card-body p-0">
                                    @foreach($searchResults as $item)
                                        <div class="p-2 border-bottom cursor-pointer" wire:click="addItemFromSearch({{ $item['id'] }})" style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">{{ $item['name'] ?? '' }}</div>
                                                    <div class="small text-muted" style="font-size: 0.8rem;">{{ $item['code'] ?? '' }}</div>
                                                </div>
                                                <div class="text-success fw-bold" style="font-size: 0.8rem;">
                                                    <span>اضغط للإضافة</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @elseif($searchTerm && strlen($searchTerm) >= 2)
                        <div class="alert alert-info text-center py-2">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>لا توجد نتائج للبحث عن: <strong>{{ $searchTerm }}</strong></small>
                        </div>
                    @endif

                    {{-- عرض الأصناف عند اختيار التصنيف --}}
                    <div x-data="{
                        selectedCategory: @entangle('selectedCategory'),
                        categoryItems: [],
                        loadItems() {
                            if (this.selectedCategory) {
                                this.$wire.call('getCategoryItems', this.selectedCategory).then(result => {
                                    this.categoryItems = result;
                                });
                            } else {
                                this.categoryItems = [];
                            }
                        },
                        init() {
                            this.$watch('selectedCategory', () => {
                                this.loadItems();
                            });
                            this.loadItems();
                        }
                    }" x-show="selectedCategory" class="mb-3 flex-grow-1 d-flex flex-column">
                        <h6 class="text-primary mb-2">أصناف التصنيف المختار</h6>
                        <div class="row g-2 flex-grow-1" style="max-height: 250px; overflow-y: auto;">
                            <template x-for="(item, index) in categoryItems" :key="item.id">
                                <div class="col-md-3 col-sm-6">
                                    <button type="button" 
                                            @click="$wire.addItemFromSearch(item.id)"
                                            class="btn btn-outline-primary w-100 d-flex flex-column align-items-center justify-content-center py-2"
                                            style="min-height: 80px; transition: all 0.3s ease;">
                                        <div class="fw-bold mb-1" style="font-size: 0.85rem;" x-text="item.name.substring(0, 15)"></div>
                                        <div class="badge bg-primary bg-opacity-25 text-primary" style="font-size: 0.7rem;" x-text="item.code"></div>
                                    </button>
                                </div>
                            </template>
                            
                            <div x-show="categoryItems.length === 0" class="col-12 text-center py-3">
                                <i class="fas fa-info-circle fs-4 text-muted mb-2"></i>
                                <p class="text-muted mb-0" style="font-size: 0.9rem;">لا توجد أصناف في هذا التصنيف</p>
                            </div>
                        </div>
                    </div>

                    {{-- معلومات الصنف المختار --}}
                    <div x-show="currentSelectedItem" class="card mb-3" x-data="{
                        currentSelectedItem: @entangle('currentSelectedItem'),
                        selectedItemData: @entangle('selectedItemData')
                    }">
                        <div class="card-header py-2">
                            <h6 class="mb-0" style="font-size: 0.9rem;">معلومات الصنف</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="row g-1">
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="small text-muted">الاسم</div>
                                        <div class="fw-bold" style="font-size: 0.8rem;" x-text="selectedItemData.name"></div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="small text-muted">المتاح</div>
                                        <span class="badge bg-info" style="font-size: 0.7rem;" x-text="selectedItemData.available_quantity_in_store"></span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="small text-muted">السعر</div>
                                        <div class="text-success fw-bold" style="font-size: 0.8rem;" x-text="selectedItemData.price.toLocaleString() + ' ريال'"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الجانب الأيمن - سلة التسوق والمعاملة --}}
            <div class="col-lg-4 bg-light d-flex flex-column" style="overflow: hidden;">
                <div class="p-3 flex-grow-1 d-flex flex-column" style="overflow: hidden; height: 100%;">
                    {{-- سلة التسوق (كقسم/كونتينر مثل باقي الأقسام) --}}
                    <div class="card mb-2 flex-grow-1 d-flex flex-column">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0" style="font-size: 0.9rem;">سلة التسوق</h6>
                            <span class="badge bg-primary" style="font-size: 0.8rem;" x-data="{ invoiceItems: @entangle('invoiceItems') }" x-text="invoiceItems.length + ' صنف'"></span>
                        </div>
                        <div class="card-body py-2 d-flex flex-column" x-data="{ invoiceItems: @entangle('invoiceItems') }" style="overflow: hidden;">
                            <div class="flex-grow-1" style="max-height: 220px; overflow-y: auto;">
                                <template x-for="(item, index) in invoiceItems" :key="index">
                                    <div class="card mb-2">
                                        <div class="card-body py-2">
                                            <div class="d-flex align-items-center gap-2">
                                                {{-- زر الحذف --}}
                                                <button type="button" 
                                                        @click="$wire.removeRow(index)"
                                                        class="btn btn-sm btn-outline-danger flex-shrink-0">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                
                                                {{-- اسم المنتج والوحدة --}}
                                                <div class="flex-grow-1 min-width-0">
                                                    <div class="fw-bold text-truncate" x-text="item.name" style="font-size: 0.9rem;"></div>
                                                    <small class="text-muted" x-text="'الوحدة: ' + (item.available_units && item.available_units.length === 1 ? item.available_units[0].name : 'قطعه')"></small>
                                                </div>
                                                
                                                {{-- اختيار الوحدة (إذا كان هناك أكثر من وحدة) --}}
                                                <div x-show="item.available_units && item.available_units.length > 1" class="flex-shrink-0">
                                                    <select x-model="item.unit_id" 
                                                            @change="$wire.updateUnit(index, item.unit_id)"
                                                            class="form-select form-select-sm" style="width: 80px;">
                                                        <template x-for="unit in item.available_units" :key="unit.id">
                                                            <option :value="unit.id" x-text="unit.name"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                                
                                                {{-- الكمية --}}
                                                <div class="d-flex align-items-center gap-1 flex-shrink-0" x-data="{
                                                    get quantity() { return item.quantity; },
                                                    set quantity(value) { item.quantity = value; },
                                                    updateQuantity() {
                                                        $wire.updateQuantity(index, this.quantity);
                                                    }
                                                }">
                                                    <button type="button" 
                                                            @click="quantity = Math.max(1, quantity - 1); updateQuantity()"
                                                            class="btn btn-sm btn-outline-secondary" style="width: 25px; height: 25px; padding: 0;">
                                                        <i class="fas fa-minus" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    <input type="number" 
                                                           x-model="quantity"
                                                           @change="updateQuantity()"
                                                           class="form-control form-control-sm text-center"
                                                           min="1"
                                                           style="width: 45px; height: 25px; font-size: 0.8rem;">
                                                    <button type="button" 
                                                            @click="quantity++; updateQuantity()"
                                                            class="btn btn-sm btn-outline-secondary" style="width: 25px; height: 25px; padding: 0;">
                                                        <i class="fas fa-plus" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                </div>
                                                
                                                {{-- السعر والمجموع --}}
                                                <div class="text-end flex-shrink-0" style="min-width: 120px;">
                                                    <div class="small text-muted">السعر: <span x-text="item.price.toLocaleString()"></span> ريال</div>
                                                    <div class="fw-bold text-success">المجموع: <span x-text="item.sub_value.toLocaleString()"></span> ريال</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="invoiceItems.length === 0" class="text-center py-3">
                                    <i class="fas fa-shopping-cart fs-3 text-muted mb-2"></i>
                                    <p class="text-muted mb-1" style="font-size: 0.9rem;">السلة فارغة</p>
                                    <small class="text-muted" style="font-size: 0.8rem;">ابحث عن الأصناف وأضفها للسلة</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ملخص المعاملة --}}
                    <div class="card mb-2" x-data="{
                        subtotal: @entangle('subtotal'),
                        discountValue: @entangle('discount_value'),
                        additionalValue: @entangle('additional_value'),
                        totalAfterAdditional: @entangle('total_after_additional'),
                        calculateTotal() {
                            $wire.calculateTotals();
                        }
                    }">
                        <div class="card-header py-2">
                            <h6 class="mb-0" style="font-size: 0.9rem;">ملخص المعاملة</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between mb-2">
                                <span>المجموع الفرعي:</span>
                                <span x-text="subtotal.toLocaleString() + ' ريال'"></span>
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <label class="form-label small">الخصم:</label>
                                    <input type="number" 
                                           x-model="discountValue"
                                           @input="calculateTotal()"
                                           class="form-control form-control-sm"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00">
                                </div>

                                <div class="col-6">
                                    <label class="form-label small">الإضافي:</label>
                                    <input type="number" 
                                           x-model="additionalValue"
                                           @input="calculateTotal()"
                                           class="form-control form-control-sm"
                                           step="0.01"
                                           min="0"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>الإجمالي:</span>
                                <span class="text-primary" x-text="totalAfterAdditional.toLocaleString() + ' ريال'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- طرق الدفع --}}
                    <div class="card mb-2" x-data="{
                        paymentMethod: @entangle('paymentMethod'),
                        cashAmount: @entangle('cashAmount'),
                        cardAmount: @entangle('cardAmount'),
                        changeAmount: @entangle('changeAmount'),
                        totalAfterAdditional: @entangle('total_after_additional'),
                        setPaymentMethod(method) {
                            this.paymentMethod = method;
                            this.updatePaymentCalculations();
                        },
                        updatePaymentCalculations() {
                            if (this.paymentMethod === 'cash') {
                                this.cashAmount = this.totalAfterAdditional;
                                this.cardAmount = 0;
                            } else if (this.paymentMethod === 'card') {
                                this.cardAmount = this.totalAfterAdditional;
                                this.cashAmount = 0;
                            }
                            this.calculateChange();
                        },
                        calculateChange() {
                            const totalPaid = this.cashAmount + this.cardAmount;
                            this.changeAmount = Math.max(0, totalPaid - this.totalAfterAdditional);
                        }
                    }" x-init="
                        $watch('totalAfterAdditional', () => {
                            updatePaymentCalculations();
                        });
                    ">
                        <div class="card-header py-2">
                            <h6 class="mb-0" style="font-size: 0.9rem;">طريقة الدفع</h6>
                        </div>
                        <div class="card-body py-2">
                            <div class="d-flex gap-2 w-100 mb-2" role="group">
                                <button type="button" 
                                        @click="setPaymentMethod('cash')"
                                        :class="{'btn-primary': paymentMethod === 'cash', 'btn-outline-primary': paymentMethod !== 'cash'}"
                                        class="btn flex-fill">
                                    <i class="fas fa-money-bill-wave me-1"></i>
                                    نقدي
                                </button>
                                <button type="button" 
                                        @click="setPaymentMethod('card')"
                                        :class="{'btn-primary': paymentMethod === 'card', 'btn-outline-primary': paymentMethod !== 'card'}"
                                        class="btn flex-fill">
                                    <i class="fas fa-credit-card me-1"></i>
                                    بطاقة
                                </button>
                                <button type="button" 
                                        @click="setPaymentMethod('mixed')"
                                        :class="{'btn-primary': paymentMethod === 'mixed', 'btn-outline-primary': paymentMethod !== 'mixed'}"
                                        class="btn flex-fill">
                                    <i class="fas fa-coins me-1"></i>
                                    مختلط
                                </button>
                            </div>

                            <div class="row g-2">
                                <div x-show="paymentMethod === 'cash' || paymentMethod === 'mixed'" class="col-6">
                                    <label class="form-label small">المبلغ النقدي:</label>
                                    <input type="number" 
                                           x-model="cashAmount"
                                           @input="calculateChange()"
                                           class="form-control form-control-sm"
                                           step="0.01"
                                           min="0">
                                </div>

                                <div x-show="paymentMethod === 'card' || paymentMethod === 'mixed'" class="col-6">
                                    <label class="form-label small">مبلغ البطاقة:</label>
                                    <input type="number" 
                                           x-model="cardAmount"
                                           @input="calculateChange()"
                                           class="form-control form-control-sm"
                                           step="0.01"
                                           min="0">
                                </div>

                                <div x-show="changeAmount > 0" class="col-12">
                                    <div class="alert alert-success py-2 mb-0">
                                        <small class="d-flex justify-content-between">
                                            <span>المبلغ المتبقي للعميل:</span>
                                            <strong x-text="changeAmount.toLocaleString() + ' ريال'"></strong>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- أزرار العمليات --}}
                    <div class="mt-auto pt-2" x-data="{
                        invoiceItems: @entangle('invoiceItems')
                    }">
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="button" 
                                    @click="$wire.saveAndPrint()"
                                    :disabled="invoiceItems.length === 0"
                                    class="btn btn-success flex-fill" style="font-size: 0.9rem;">
                                <i class="fas fa-print me-1"></i>
                                دفع وطباعة
                            </button>

                            <button type="button" 
                                    @click="$wire.saveForm()"
                                    :disabled="invoiceItems.length === 0"
                                    class="btn btn-primary flex-fill" style="font-size: 0.9rem;">
                                <i class="fas fa-save me-1"></i>
                                حفظ فقط
                            </button>

                            <button type="button" 
                                    @click="$wire.resetForm()"
                                    class="btn btn-outline-danger flex-fill" style="font-size: 0.9rem;">
                                <i class="fas fa-times me-1"></i>
                                إلغاء
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

