@extends('admin.dashboard')

@section('content')
<div class="row">
    @include('components.breadcrumb', [
        'title' => __('shipping::shipping.shipping_zones'),
        'breadcrumb_items' => [['label' => __('shipping::shipping.dashboard'), 'url' => route('admin.dashboard')], ['label' => __('shipping::shipping.shipping_zones')]],
    ])
    <div class="col-lg-12">
        <a href="{{ route('shipping.zones.create') }}" class="btn btn-primary mb-3">
            <i class="las la-plus"></i> {{ __('shipping::shipping.add_new') }}
        </a>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('shipping::shipping.name') }}</th>
                            <th>{{ __('shipping::shipping.code') }}</th>
                            <th>{{ __('shipping::shipping.base_rate') }}</th>
                            <th>{{ __('shipping::shipping.rate_per_kg') }}</th>
                            <th>{{ __('shipping::shipping.estimated_days') }}</th>
                            <th>{{ __('shipping::shipping.status') }}</th>
                            <th>{{ __('shipping::shipping.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($zones as $zone)
                        <tr>
                            <td>{{ $zone->id }}</td>
                            <td>{{ $zone->name }}</td>
                            <td><span class="badge bg-info">{{ $zone->code }}</span></td>
                            <td>{{ number_format($zone->base_rate, 2) }}</td>
                            <td>{{ number_format($zone->rate_per_kg, 2) }}</td>
                            <td>{{ $zone->estimated_days }} {{ __('shipping::shipping.days') }}</td>
                            <td>
                                @if ($zone->is_active)
                                    <span class="badge bg-success">{{ __('shipping::shipping.active') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('shipping::shipping.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('shipping.zones.edit', $zone->id) }}" class="btn btn-sm btn-warning">
                                    <i class="las la-edit"></i>
                                </a>
                                <form action="{{ route('shipping.zones.destroy', $zone) }}" method="POST" class="d-inline" style="display:inline-block;" onsubmit="return confirm('{{ __('shipping::shipping.confirm_delete_zone') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="las la-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $zones->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
