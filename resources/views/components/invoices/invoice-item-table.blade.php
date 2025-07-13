<table class="table table-striped mb-0" style="min-width: 1200px;">
    <thead class="table-light text-center align-middle">

        <tr>
            <th class="font-family-cairo fw-bold font-14 text-center">الصنف</th>
            <th class="font-family-cairo fw-bold font-14 text-center">الوحدة</th>
            <th class="font-family-cairo fw-bold font-14 text-center">الكمية</th>
            <th class="font-family-cairo fw-bold font-14 text-center">السعر</th>
            <th class="font-family-cairo fw-bold font-14 text-center">الخصم</th>
            <th class="font-family-cairo fw-bold font-14 text-center">القيمة</th>
            <th class="font-family-cairo fw-bold font-14 text-center">إجراء</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="7" style="padding:0; border:none;">
                <div style="max-height: 320px; overflow-y: auto; overflow-x: hidden;">
                    <table class="table mb-0" style="background: transparent;">
                        <tbody>
                            @forelse ($invoiceItems as $index => $row)
                                <tr wire:key="invoice-row-{{ $index }}">

                                    {{-- اختيار الصنف --}}
                                    <td style="width: 18%">
                                        <select wire:model.live="invoiceItems.{{ $index }}.item_id"
                                            wire:change="updateUnits({{ $index }})"
                                            class="form-control @error('invoiceItems.' . $index . '.item_id') is-invalid @enderror"
                                            disabled>
                                            <option value="">{{ __('اختر الصنف') }}</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('invoiceItems.' . $index . '.item_id')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </td>

                                    {{-- اختيار الوحدة --}}
                                    <td style="width: 15%">
                                        <select wire:model.live="invoiceItems.{{ $index }}.unit_id"
                                            wire:key="unit-select-{{ $index }}-{{ $row['item_id'] ?? 'default' }}"
                                            wire:change="updatePriceForUnit({{ $index }})"
                                            class="form-control @error('invoiceItems.' . $index . '.unit_id') is-invalid @enderror">
                                            <option value="">{{ __('اختر الوحدة') }}
                                            </option>
                                            @if (isset($row['available_units']) && $row['available_units']->count() > 0)
                                                @foreach ($row['available_units'] as $unit)
                                                    <option value="{{ $unit->id }}">
                                                        {{ $unit->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('invoiceItems.' . $index . '.unit_id')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </td>

                                    {{-- حقل الكمية مع التنقل التلقائي --}}
                                    <td style="width: 10%">
                                        <input type="number" step="0.01" min="0"
                                            wire:model.blur="invoiceItems.{{ $index }}.quantity"
                                            id="quantity_{{ $index }}" placeholder="الكمية"
                                            onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('price_{{ $index }}')?.focus();document.getElementById('price_{{ $index }}')?.select();}"
                                            class="form-control @error('invoiceItems.' . $index . '.quantity') is-invalid @enderror">
                                        @error('invoiceItems.' . $index . '.quantity')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </td>

                                    {{-- حقل السعر مع التنقل التلقائي --}}
                                    <td style="width: 15%">
                                        <input type="number" step="0.01" min="0"
                                            wire:model.blur="invoiceItems.{{ $index }}.price"
                                            id="price_{{ $index }}" placeholder="السعر"
                                            onkeydown="if(event.key==='Enter'){event.preventDefault();document.getElementById('discount_{{ $index }}')?.focus();document.getElementById('discount_{{ $index }}')?.select();}"
                                            class="form-control @error('invoiceItems.' . $index . '.price') is-invalid @enderror">
                                        @error('invoiceItems.' . $index . '.price')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </td>

                                    {{-- حقل الخصم مع التنقل للصف التالي أو البحث --}}
                                    <td style="width: 15%">
                                        <input type="number" step="0.01" min="0"
                                            wire:model.blur="invoiceItems.{{ $index }}.discount"
                                            id="discount_{{ $index }}" placeholder="الخصم"
                                            onkeydown="if(event.key==='Enter'){
                                                                event.preventDefault();
                                                                const subValueField = document.getElementById('sub_value_{{ $index }}');
                                                                if(subValueField) {
                                                                    subValueField.focus();
                                                                    subValueField.select();
                                                                }
                                                            }"
                                            class="form-control">
                                    </td>

                                    {{-- حقل القيمة الفرعية --}}
                                    <td style="width: 15%">
                                        <input type="number" step="0.01" min="0"
                                            wire:model.blur="invoiceItems.{{ $index }}.sub_value"
                                            id="sub_value_{{ $index }}" placeholder="القيمة"
                                            onkeydown="if(event.key==='Enter'){
                                                                event.preventDefault();
                                                                const nextQuantity = document.getElementById('quantity_{{ $index + 1 }}');
                                                                if(nextQuantity) {
                                                                    nextQuantity.focus();
                                                                    nextQuantity.select();
                                                                } else {
                                                                    const searchField = document.querySelector('input[wire\\:model\\.live=&quot;searchTerm&quot;]');
                                                                    if(searchField) searchField.focus();
                                                                }
                                                            }"
                                            class="form-control">
                                    </td>

                                    {{-- زرّ الحذف --}}
                                    <td class="text-center" style="width: 10%">
                                        <button type="button" wire:click="removeRow({{ $index }})"
                                            class="btn btn-danger btn-icon-square-sm"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا الصف؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="text-center">
                                        <div class="alert alert-info py-3 mb-0"
                                            style="font-size: 1.2rem; font-weight: 500;">
                                            <i class="las la-info-circle me-2"></i>
                                            لا توجد بيانات
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </tbody>
</table>
