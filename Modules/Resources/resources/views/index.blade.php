@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">إدارة الموارد</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="header-title">قائمة الموارد</h4>
                        <div>
                            @can('create Resources')
                            <a href="{{ route('resources.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> إضافة مورد جديد
                            </a>
                            @endcan
                            @can('view Resources Dashboard')
                            <a href="{{ route('resources.dashboard') }}" class="btn btn-info">
                                <i class="fas fa-chart-bar"></i> لوحة المعلومات
                            </a>
                            @endcan
                        </div>
                    </div>

                    @livewire('resources-index')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

