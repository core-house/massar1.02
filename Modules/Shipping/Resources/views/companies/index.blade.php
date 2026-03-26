@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.shipping')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.shipping_companies'),
        'breadcrumb_items' => [
            ['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('shipping::shipping.shipping_companies')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Shipping Companies')
                <a href="{{ route('companies.create') }}" type="button" class="btn btn-main font-hold fw-bold">
                    {{ __('shipping::shipping.add_new') }}
                    <i class="las la-plus me-2"></i>
                </a>
            @endcan
            <br><br>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="companies-table" filename="shipping-companies"
                            excel-label="{{ __('shipping::shipping.export_excel') }}" pdf-label="{{ __('shipping::shipping.export_pdf') }}"
                            print-label="{{ __('shipping::shipping.print') }}" />

                        <table id="companies-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('shipping::shipping.name') }}</th>
                                    <th>{{ __('shipping::shipping.email') }}</th>
                                    <th>{{ __('shipping::shipping.phone') }}</th>
                                    <th>{{ __('shipping::shipping.address') }}</th>
                                    <th>{{ __('shipping::shipping.base_rate') }}</th>
                                    <th>{{ __('shipping::shipping.status') }}</th>
                                    @canany(['edit Shipping Companies', 'delete Shipping Companies'])
                                        <th>{{ __('shipping::shipping.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($companies as $company)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $company->name }}</td>
                                        <td>{{ $company->email }}</td>
                                        <td>{{ $company->phone }}</td>
                                        <td>{{ $company->address }}</td>
                                        <td>{{ $company->base_rate }}</td>
                                        <td>
                                            @if ($company->is_active)
                                                <span class="badge bg-success">{{ __('shipping::shipping.active') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('shipping::shipping.inactive') }}</span>
                                            @endif
                                        </td>
                                        @canany(['edit Shipping Companies', 'delete Shipping Companies'])
                                            <td>
                                                @can('edit Shipping Companies')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('companies.edit', $company) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete Shipping Companies')
                                                    <form action="{{ route('companies.destroy', $company) }}" method="POST"
                                                        style="display:inline-block;"
                                                        onsubmit="return confirm('{{ __('shipping::shipping.confirm_delete_company') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('shipping::shipping.no_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{ $companies->links() }}
@endsection
