@extends('admin.dashboard')
@section('content')
<div class="container mt-4">
    <h2 class="mb-4">بيانات المشروع: {{ $project->name }}</h2>

    {{-- بيانات المشروع --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">تفاصيل المشروع</div>
        <div class="card-body">
            <p><strong>الاسم:</strong> {{ $project->name }}</p>
            <p><strong>التاريخ:</strong> {{ $project->start_date }}</p>
            <p><strong>الوصف:</strong> {{ $project->description }}</p>
        </div>
    </div>

    {{-- العمليات --}}
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">العمليات</div>
        <div class="card-body">
            @if($project->operations->count())
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>نوع العملية</th>
                            <th>القيمة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->operations as $op)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $op->type }}</td>
                                <td>{{ number_format($op->amount, 2) }}</td>
                                <td>{{ $op->date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>لا توجد عمليات.</p>
            @endif
        </div>
    </div>

    {{-- المعدات --}}
    <div class="card mb-4">
        <div class="card-header bg-success text-white">المعدات</div>
        <div class="card-body">
            @if($project->equipment->count())
                <ul class="list-group">
                    @foreach($project->equipment as $equip)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $equip->name }}
                            <span class="badge bg-primary rounded-pill">{{ $equip->quantity }}</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>لا توجد معدات.</p>
            @endif
        </div>
    </div>

    {{-- السندات --}}
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">السندات</div>
        <div class="card-body">
            @if($project->vouchers->count())
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نوع السند</th>
                            <th>القيمة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->vouchers as $voucher)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $voucher->type }}</td>
                                <td>{{ number_format($voucher->amount, 2) }}</td>
                                <td>{{ $voucher->date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>لا توجد سندات.</p>
            @endif
        </div>
    </div>
</div>
@endsection
