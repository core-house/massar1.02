
        @extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.departments')
    @include('components.sidebar.permissions')
@endsection
        @section('content')
            @include('components.breadcrumb', [
                'title' => __('CVs'),
                'items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('CVs')]],
            ])

            <livewire:hr-management.cvs.manage-cvs />
        @endsection
