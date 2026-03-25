@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.reports')
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-head">
                <h2>{{ __('reports::reports.Daily Inventory Movement Report') }}</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('reports::reports.Date') }}</th>
                                <th>{{ __('reports::reports.Item') }}</th>
                                <th>{{ __('reports::reports.inbound_quantity') }}</th>
                                <th>{{ __('reports::reports.outbound_quantity') }}</th>
                                <th>{{ __('reports::reports.Balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- سيتم عرض بيانات الحركة هنا --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

