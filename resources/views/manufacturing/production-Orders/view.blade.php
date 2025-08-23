@extends('admin.dashboard')

@section('content')
<livewire:production-orders-management.view :productionOrder="$productionOrder" />
@endsection
