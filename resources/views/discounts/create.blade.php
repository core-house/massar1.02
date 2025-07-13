@extends('admin.dashboard')
@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @include('components.breadcrumb', [
        'title' => __('الخصومات'),
        'items' => [
            ['label' => __('الرئيسيه'), 'url' => route('admin.dashboard')],
            ['label' => __('الخصومات'), 'url' => route('discounts.index')],
            ['label' => __('إنشاء خصم')],
        ],
    ])
    <div class="content-wrapper">
        <section class="content">
            <form action="{{ route('discounts.store') }}" method="post">
                @csrf
                <div class="card bg-white col-md-12 container">

                    @php
                        $titles = [
                            30 => 'خصم مسموح به',
                            31 => 'خصم مكتسب',
                        ];
                    @endphp

                    <div class="card-header">
                        <h3 class="card-title fw-bold fs-2">
                            {{ $titles[$type] }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="type" value="{{ $type }}">

                            @if ($type == 30)
                                {{-- acc2 ثابت --}}
                                <input type="hidden" name="acc2" value="{{ $acc2Fixed->id }}">
                                {{-- acc1 = العملاء --}}
                                <div class="col-lg-4">
                                    <label>الحساب المدين (acc1 - العملاء)</label>
                                    <select name="acc1" class="form-control" required>
                                        @foreach ($clientsAccounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->aname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @elseif ($type == 31)
                                {{-- acc1 ثابت --}}
                                <input type="hidden" name="acc1" value="{{ $acc1Fixed->id }}">

                                {{-- acc2 = الموردين --}}
                                <div class="col-lg-4">
                                    <label>الحساب الدائن (acc2 - الموردين)</label>
                                    <select name="acc2" class="form-control" required>
                                        @foreach ($suppliers as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->aname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif


                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="pro_date">{{ __('التاريخ') }}</label>
                                    <input type="date" name="pro_date"
                                        class="form-control @error('pro_date') is-invalid @enderror">
                                    @error('pro_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="pro_id">{{ __('رقم السند') }}</label>
                                    <input type="number" name="pro_id"
                                        class="form-control @error('pro_id') is-invalid @enderror"
                                        value="{{ old('pro_id', $nextProId) }}" readonly>
                                    @error('pro_id')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label for="pro_value">{{ __('قيمة الخصم ') }}</label>
                                    <input type="number" name="pro_value" id="pro_value" step="0.01" min="0.01"
                                        class="form-control @error('pro_value') is-invalid @enderror">
                                    @error('pro_value')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="form-group">
                                    <label for="info">{{ __('ملاحظات') }}</label>
                                    <textarea type="text" name="info" class="form-control  @error('info') is-invalid @enderror"></textarea>
                                    @error('info')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">تأكيد</button>
                            <a href="{{ url()->previous() }}" class="btn btn-danger">إلغاء</a>
                        </div>
                    </div>
                    <br>
                </div>
            </form>
        </section>
    </div>
@endsection
