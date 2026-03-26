@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.service')
@endsection

@section('title', 'إضافة فاتورة خدمة جديدة')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @livewire('services.service-invoice-form', ['type' => request('type', 'sell')])
        </div>
    </div>
</div>
@endsection
