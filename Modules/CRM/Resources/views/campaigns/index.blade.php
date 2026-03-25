@extends('admin.dashboard')

@section('sidebar')
    @include('components.sidebar.crm')
@endsection

@section('content')
    @include('components.breadcrumb', [
        'title' => __('crm::crm.marketing_campaigns'),
        'breadcrumb_items' => [
            ['label' => __('crm::crm.dashboard'), 'url' => route('admin.dashboard')],
            ['label' => __('crm::crm.marketing_campaigns')]
        ],
    ])

    <div class="row">
        <div class="col-lg-12">
            @can('create Campaigns')
                <a href="{{ route('campaigns.create') }}" class="btn btn-main font-hold fw-bold">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('crm::crm.new_campaign') }}
                </a>
            @endcan
            <br><br>

            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('crm::crm.campaign_title') }}</th>
                                    <th>{{ __('crm::crm.status') }}</th>
                                    <th>{{ __('crm::crm.total_recipients') }}</th>
                                    <th>{{ __('crm::crm.sent') }}</th>
                                    <th>{{ __('crm::crm.opened') }}</th>
                                    <th>{{ __('crm::crm.open_rate') }}</th>
                                    <th>{{ __('crm::crm.created_by') }}</th>
                                    <th>{{ __('crm::crm.created_at') }}</th>
                                    <th>{{ __('crm::crm.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($campaigns as $campaign)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $campaign->title }}</td>
                                        <td>
                                            @if ($campaign->status === 'draft')
                                                <span class="badge bg-secondary">{{ __('crm::crm.draft') }}</span>
                                            @elseif ($campaign->status === 'sent')
                                                <span class="badge bg-success">{{ __('crm::crm.sent') }}</span>
                                            @else
                                                <span class="badge bg-info">{{ __('crm::crm.scheduled') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $campaign->total_recipients }}</td>
                                        <td>{{ $campaign->total_sent }}</td>
                                        <td>{{ $campaign->total_opened }}</td>
                                        <td>
                                            @if ($campaign->total_sent > 0)
                                                <span class="badge bg-primary">{{ $campaign->open_rate }}%</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $campaign->creator->name }}</td>
                                        <td>{{ $campaign->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <a class="btn btn-info btn-icon-square-sm" 
                                               href="{{ route('campaigns.show', $campaign) }}">
                                                <i class="las la-eye"></i>
                                            </a>
                                            
                                            @if ($campaign->isDraft())
                                                @can('edit Campaigns')
                                                    <a class="btn btn-success btn-icon-square-sm"
                                                       href="{{ route('campaigns.edit', $campaign) }}">
                                                        <i class="las la-edit"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('delete Campaigns')
                                                    <form action="{{ route('campaigns.destroy', $campaign) }}" 
                                                          method="POST" style="display:inline-block;"
                                                          onsubmit="return confirm('{{ __('crm::crm.confirm_delete_campaign') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-icon-square-sm">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info py-3 mb-0">
                                                <i class="las la-info-circle me-2"></i>
                                                {{ __('crm::crm.no_campaigns_added_yet') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
