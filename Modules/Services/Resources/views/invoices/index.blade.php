@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.service')
    @include('components.sidebar.accounts')
@endsection

@section('title', 'فواتير الخدمات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @livewire('services.service-invoice', ['type' => request('type', 'sell')])
        </div>
    </div>
</div>
@endsection
