@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.service')
    @include('components.sidebar.accounts')
@endsection

@section('title', 'تفاصيل فاتورة الخدمة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @livewire('services.service-invoice-show', ['invoiceId' => $invoice->id])
        </div>
    </div>
</div>
@endsection
