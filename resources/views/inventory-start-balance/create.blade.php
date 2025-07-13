@extends('admin.dashboard')

@section('content')
    <div class="content-wrapper">
        <section class="content">
            <form action="{{ route('inventory-balance.store') }}" method="POST" class="card bg-white mt-3">
                <div class="card-header">
                    <div class="col-12">
                        <h3 class="card-title ">تعديل الرصيد الافتتاحي للأصناف</h3>
                    </div>
                </div>
                <div class="card-body">
                    @csrf



                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label">المخزن</label>
                            <select id="store_select" name="store_id"
                                class="form-control form-control-sm @error('store_id') is-invalid @enderror">
                                @foreach ($stors as $store)
                                    <option value="{{ $store->id }}">{{ $store->aname }}</option>
                                @endforeach
                            </select>
                            @error('store_id')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">الشريك</label>
                            <select id="partner_select" name="partner_id"
                                class="form-control form-control-sm @error('partner_id') is-invalid @enderror">
                                @foreach ($partners as $partner)
                                    <option value="{{ $partner->id }}">{{ $partner->aname }}</option>
                                @endforeach
                            </select>
                            @error('partner_id')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">تاريخ بداية المدة</label>
                            <input type="date" name="periodStart" id="periodStart"
                                value="{{ old('periodStart', $periodStart) }}"
                                class="form-control form-control-sm @error('periodStart') is-invalid @enderror">
                            @error('periodStart')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped mb-0" style="min-width: 1200px;">
                                    <thead class="table-light text-center align-middle">
                                        <tr>
                                            <th style="width: 10%">الكود</th>
                                            <th style="width: 20%">الاسم</th>
                                            <th style="width: 15%">الوحدة</th>
                                            <th style="width: 15%">التكلفة</th>
                                            <th style="width: 10%">الرصيد الحالي</th>
                                            <th style="width: 15%">رصيد أول المدة الجديد</th>
                                            <th style="width: 15%">كمية التسوية</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items_table_body">
                                        @foreach ($itemList as $item)
                                            <tr data-item-id="{{ $item->id }}">
                                                <td><input type="text" value="{{ $item->code }}"
                                                        class="form-control form-control-sm" readonly></td>
                                                <td><input type="text" value="{{ $item->name }}"
                                                        class="form-control form-control-sm" readonly></td>
                                                <td>
                                                    <select name="unit_ids[{{ $item->id }}]"
                                                        class="form-control form-control-sm unit-select"
                                                        data-item-id="{{ $item->id }}">
                                                        @foreach ($item->units as $unit)
                                                            <option value="{{ $unit->id }}"
                                                                data-cost="{{ $unit->pivot->cost ?? 0 }}">
                                                                {{ $unit->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="text"
                                                        value="{{ $item->units->first()?->pivot->cost ?? 0 }}"
                                                        class="form-control form-control-sm cost-input"
                                                        data-item-id="{{ $item->id }}"></td>
                                                <td><input type="text" value="{{ $item->opening_balance ?? 0 }}"
                                                        class="form-control form-control-sm current-balance" readonly></td>
                                                <td><input type="number" name="new_opening_balance[{{ $item->id }}]"
                                                        class="form-control form-control-sm new-balance-input"
                                                        data-item-id="{{ $item->id }}"></td>
                                                <td><input type="number" name="adjustment_qty[{{ $item->id }}]"
                                                        class="form-control form-control-sm adjustment-qty" readonly></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12 text-left">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </section>
    </div>
@endsection
