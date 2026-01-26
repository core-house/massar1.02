@extends('pos::layouts.master')

@push('styles')
    @include('pos::partials._styles')
@endpush

@section('content')
<div class="pos-create-container">
    
    @include('pos::partials.top-nav')
    @include('pos::partials.category-bar')

    {{-- Main Content Area: Split 2/3 Products + 1/3 Cart --}}
    <div class="d-flex flex-grow-1" style="overflow: hidden;">
        @include('pos::partials.products-grid')
        @include('pos::partials.cart-sidebar')
    </div>

    @include('pos::partials.bottom-bar')

    {{-- Bootstrap Modals --}}
    @include('pos::partials.modals.payment')
    @include('pos::partials.modals.customer')
    @include('pos::partials.modals.notes')
    @include('pos::partials.modals.table')
    @include('pos::partials.modals.pending-transactions')
    @include('pos::partials.modals.recent-transactions')
    @include('pos::partials.modals.held-orders')
    @include('pos::partials.modals.pay-out')
    @include('pos::partials.modals.return-invoice')

</div>
@endsection

@push('scripts')
    @include('pos::partials._scripts')
@endpush
