@extends('pos::layouts.master')

@push('styles')
@endpush

@section('content')
<div class="pos-create-page">
    <div class="pos-header-navigation">
        <a href="{{ route('pos.index') }}" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لنظام نقاط البيع</span>
        </a>
    </div>
    
    @livewire('pos::create-pos-transaction-form')
</div>
@endsection
