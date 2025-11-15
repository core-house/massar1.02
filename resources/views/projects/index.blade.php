@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.projects')
@endsection
@section('content')
   

    <livewire:projects.index />
@endsection
