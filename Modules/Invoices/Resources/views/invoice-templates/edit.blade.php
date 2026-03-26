@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.sales-invoices')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('invoices::templates.edit_template'),
        'breadcrumb_items' => [
            ['label' => __('invoices::invoices.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('invoices::templates.invoice_templates'), 'url' => route('invoice-templates.index')],
            ['label' => __('invoices::templates.edit_template')],
        ],
    ])


    <div class="row">
        <div class="col-lg-12">
            {{-- عرض رسائل النجاح --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="las la-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- عرض أخطاء الـ Validation --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading">
                        <i class="las la-exclamation-triangle me-2"></i>
                        {{ __('invoices::invoices.validation_errors') }}
                    </h5>
                    <hr>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <form action="{{ route('invoice-templates.update', $template) }}" method="POST">
                    @csrf
                    @method('PUT')


                    <div class="card-body">
                        @include('invoices::invoice-templates.partials.form-fields', [
                            'template' => $template,
                        ])
                    </div>


                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary font-hold fw-bold">
                            <i class="las la-save me-1"></i> {{ __('invoices::invoices.save_changes') }}
                        </button>
                        <a href="{{ route('invoice-templates.index') }}"
                            class="btn btn-secondary font-hold fw-bold">
                            <i class="las la-times me-1"></i> {{ __('invoices::invoices.cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('invoices::invoice-templates.partials.scripts')
@endsection
