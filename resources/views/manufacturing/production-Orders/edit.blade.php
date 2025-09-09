@extends('admin.dashboard')

@section('content')
<livewire:production-orders-management.edit :productionOrder="$productionOrder" />
@endsection
