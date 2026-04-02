@extends('admin.dashboard')

{{-- Dynamic Sidebar --}}
@section('sidebar')
    @include('components.sidebar.journals')
@endsection
@section('content')
    @include('components.breadcrumb', [
        'title' => __('common.journals'),
        'breadcrumb_items' => [['label' => __('Home'), 'url' => route('admin.dashboard')], ['label' => __('common.journals')]],
    ])

    <div class="card">
        @if (session('success'))
            <div class="alert alert-success cake cake-pulse">
                {{ __('common.' . session('success')) }}
            </div>
        @endif

        <div class="card-header">
            @can('create journals')
                <a href="{{ route('journals.create') }}" type="button" class="btn btn-main">
                    <i class="fas fa-plus me-2"></i>
                    {{ __('common.add_new') }}
                </a>
            @endcan

        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: auto;">
                <table class="table table-striped mb-0" style="min-width: 1200px;">
                    <thead class="table-light text-center align-middle">

                        <tr>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('#') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.date') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.operation_number') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.operation_type') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.description') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.amount') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.from_account') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.to_account') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.employee') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.employee_2') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.user') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.created_at') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.notes') }}</th>
                            <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.review') }}</th>
                            @canany(['edit journals', 'delete journals'])
                                <th class="font-family-cairo fw-bold font-14 text-center">{{ __('common.actions') }}</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($journals as $journal)
                            <tr>
                                <td class="font-hold fw-bold font-14 text-center">{{ $loop->iteration }}</td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->pro_date }}</td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->pro_id }}</td>
                                <td class="font-hold fw-bold font-14 text-center">
                                    {{ $journal->type->ptext ?? '—' }}
                                </td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->details }}</td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->pro_value }}</td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->account1->aname }}
                                </td>
                                <td class="font-hold fw-bold font-14 text-center">
                                    {{ $journal->account2->aname ?? '' }}</td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->emp1->aname ?? '' }}
                                </td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->emp2->aname ?? '' }}
                                </td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->user }}</td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->created_at }}</td>
                                <td class="font-hold fw-bold font-14 text-center">{{ $journal->info }}</td>
                                <td class="font-hold fw-bold font-14 text-center">
                                    {{ $journal->confirmed ? __('Yes') : __('No') }}</td>
                                @canany(['edit journals', 'delete journals'])
                                    <td class="font-family-cairo fw-bold font-14 text-center" x-show="columns[16]">
                                        @can('edit journals')
                                     
                                                <a href="{{ route('journals.edit', $journal) }}" class="btn btn-sm btn-success"><i
                                                        class="las la-edit"></i></a>
                                          
                                        @endcan
                                        @can('delete journals')
                                            <form action="{{ route('journals.destroy', $journal->id) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('{{ __('common.confirm_delete_journal') }}')">
                                                    <i class="las la-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="15" class="text-center">
                                    <div class="alert alert-info py-3 mb-0" style="font-size: 1.2rem; font-weight: 500;">
                                        <i class="las la-info-circle me-2"></i>
                                        {{ __('common.no_data_available') }}
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
@endsection
