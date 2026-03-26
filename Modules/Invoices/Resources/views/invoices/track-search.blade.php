@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.purchases-invoices')
@endsection

@section('content')
    <div class="container-fluid px-4 py-5">
        <h1 class="mb-4">{{ __('invoices::invoices.invoice_track_search') }}</h1>

        <form action="" method="get"
            onsubmit="event.preventDefault(); var id = document.getElementById('operation_id').value; if(id) window.location.href = '/invoices/track/' + encodeURIComponent(id);">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="operation_id">{{ __('invoices::invoices.operation_number_pro_id') }}</label>
                        <input id="operation_id" name="operation_id" class="form-control"
                            placeholder="{{ __('invoices::invoices.enter_operation_number') }}">
                    </div>
                    <div class="mt-3">
                        <button class="btn btn-primary" type="submit">
                            <i class="las la-search me-2"></i>
                            {{ __('invoices::invoices.view_track') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
