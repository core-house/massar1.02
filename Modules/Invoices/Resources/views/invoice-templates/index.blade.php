@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.sales-invoices')
    @include('components.sidebar.purchases-invoices')
    @include('components.sidebar.inventory-invoices')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('invoices::templates.invoice_templates'),
        'items' => [
            ['label' => __('invoices::invoices.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('invoices::templates.invoice_templates')],
        ],
    ])

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">{{ __('invoices::templates.invoice_templates') }}</h3>
            @can('create Invoice Templates')
                <a href="{{ route('invoice-templates.create') }}" class="btn btn-main btn-sm">
                    <i class="las la-plus"></i> {{ __('invoices::templates.add_new_template') }}
                </a>
            @endcan
        </div>

        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('invoices::templates.template_name') }}</th>
                        <th>{{ __('invoices::templates.template_code') }}</th>
                        <th>{{ __('invoices::invoices.notes') }}</th>
                        <th>{{ __('invoices::invoices.invoice_types') }}</th>
                        <th>{{ __('invoices::templates.visible_columns_and_order') }}</th>
                        <th>{{ __('invoices::invoices.invoice_status') }}</th>
                        <th>{{ __('invoices::templates.display_order') }}</th>
                        @canany(['edit Invoice Templates', 'delete Invoice Templates'])
                            <th>{{ __('invoices::invoices.actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse ($templates as $template)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $template->name }}</td>
                            <td><code>{{ $template->code }}</code></td>
                            <td>{{ Str::limit($template->description, 60) }}</td>
                            <td>
                                @foreach ($template->invoiceTypes as $type)
                                    <span class="badge bg-info">
                                        {{ Modules\Invoices\Models\InvoiceTemplate::getInvoiceTypeName($type->invoice_type) }}
                                        @if ($type->is_default)
                                                <i class="las la-star text-warning"></i>
                                        @endif
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                {{ count($template->visible_columns) }} {{ __('invoices::templates.number_of_columns') }}
                                @if($template->printable_sections)
                                    <br>
                                    <small class="text-muted">
                                        {{ count(array_filter($template->printable_sections ?? [])) }} {{ __('invoices::templates.printable_sections_in_invoice') }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @can('edit Invoice Templates')
                                    <form action="{{ route('invoice-templates.toggle-active', $template) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-sm btn-{{ $template->is_active ? 'success' : 'secondary' }}">
                                            {{ $template->is_active ? __('invoices::invoices.active') : __('invoices::invoices.inactive') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-{{ $template->is_active ? 'success' : 'secondary' }}">
                                        {{ $template->is_active ? __('invoices::invoices.active') : __('invoices::invoices.inactive') }}
                                    </span>
                                @endcan
                            </td>
                            <td>{{ $template->sort_order }}</td>
                            @canany(['edit Invoice Templates', 'delete Invoice Templates'])
                                <td>
                                    @can('edit Invoice Templates')
                                        <a href="{{ route('invoice-templates.edit', $template) }}" class="btn btn-sm btn-warning">
                                            <i class="las la-edit"></i>
                                        </a>
                                    @endcan

                                    @can('delete Invoice Templates')
                                        <form action="{{ route('invoice-templates.destroy', $template) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('{{ __('invoices::invoices.confirm_delete') }}')">
                                                <i class="las la-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="las la-info-circle mb-2 d-block" style="font-size: 2rem"></i>
                                {{ __('invoices::templates.no_templates_available') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $templates->links() }}
        </div>
    </div>
@endsection
