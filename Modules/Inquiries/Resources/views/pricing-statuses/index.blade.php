@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.inquiries')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('inquiries::inquiries.pricing_status'),
        'breadcrumb_items' => [
            ['label' => __('inquiries::inquiries.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('inquiries::inquiries.pricing_status')],
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Pricing Statuses')
                <a href="{{ route('pricing-statuses.create') }}" type="button" class="btn btn-main font-hold fw-bold">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('inquiries::inquiries.add_new') }}
                </a>
            @endcan
            <br>
            <br>
            <div class="card">
                <div class="card-body">
                    <x-inquiries::bulk-actions model="Modules\Inquiries\Models\PricingStatus" permission="delete Pricing Statuses">
                        <div class="table-responsive" style="overflow-x: auto;">

                        <x-table-export-actions table-id="pricing-statuses-table" filename="pricing-statuses-table"
                            excel-label="{{ __('inquiries::inquiries.export_excel') }}" pdf-label="{{ __('inquiries::inquiries.export_pdf') }}" print-label="{{ __('inquiries::inquiries.print') }}" />

                        <table id="pricing-statuses-table" class="table table-striped mb-0" style="min-width: 1200px;">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" x-model="selectAll" @change="toggleAll">
                                    </th>
                                    <th>#</th>
                                    <th>{{ __('inquiries::inquiries.name') }}</th>
                                    <th>{{ __('inquiries::inquiries.description') }}</th>
                                    <th>{{ __('inquiries::inquiries.color') }}</th>
                                    <th>{{ __('inquiries::inquiries.status') }}</th>
                                    @canany(['edit Pricing Statuses', 'delete Pricing Statuses'])
                                        <th>{{ __('inquiries::inquiries.actions') }}</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pricingStatuses as $status)
                                    <tr class="text-center">
                                        <td>
                                            <input type="checkbox" class="form-check-input bulk-checkbox" 
                                                   value="{{ $status->id }}" x-model="selectedIds">
                                        </td>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ __($status->name) }}</td>
                                        <td>{{ $status->description ?: '-' }}</td>
                                        <td>
                                            <span class="badge"
                                                style="background-color: {{ $status->color }}; color: #fff;">
                                                {{ $status->color }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($status->is_active)
                                                <span class="badge bg-success">
                                                    <i class="las la-check-circle"></i> {{ __('inquiries::inquiries.active') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="las la-times-circle"></i> {{ __('inquiries::inquiries.inactive') }}
                                                </span>
                                            @endif
                                        </td>
                                        @canany(['edit Pricing Statuses', 'delete Pricing Statuses'])
                                            <td>
                                                @can('edit Pricing Statuses')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                        href="{{ route('pricing-statuses.edit', $status->id) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('delete Pricing Statuses')
                                                    <form action="{{ route('pricing-statuses.destroy', $status->id) }}"
                                                        method="POST" style="display:inline-block;"
                                        onsubmit="return confirm('{{ __('inquiries::inquiries.confirm_delete') }}');">
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
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info py-3 mb-0"
                                                style="font-size: 1.2rem; font-weight: 500;">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('inquiries::inquiries.no_data_available') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </x-inquiries::bulk-actions>
                </div>
            </div>
        </div>
    </div>
@endsection
