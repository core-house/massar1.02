    @extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.manufacturing')
@endsection

    @section('content')
        <livewire:manufacturing-invoice />
    @endsection
