@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('reports::reports.account_movement_report'),
            'breadcrumb_items' => [['label' => __('reports::reports.home'), 'url' => route('admin.dashboard')], ['label' => __('reports::reports.accounts_reports'), 'url' => route('accounts.index')], ['label' => __('reports::reports.account_movement_report')]],
    ])

    <livewire:accounts.reports.account-movement :accountId="$accountId" />
@endsection
