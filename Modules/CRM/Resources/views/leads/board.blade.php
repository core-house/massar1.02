<div>
    @extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.crm')
    @include('components.sidebar.accounts')
@endsection

    @section('content')
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>إدارة الفرص</h2>
                {{-- <a href="{{ route('leads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة فرصة جديدة
            </a> --}}
            </div>

            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @livewire('leads-board')
        </div>
    @endsection
</div>
