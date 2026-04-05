<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('portal.account_statement') }} - {{ $account->aname }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @if(app()->getLocale() === 'ar')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    @endif
</head>
<body class="bg-light">
<nav class="navbar navbar-light bg-white border-bottom mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold">{{ __('portal.client_portal') }}</span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted">{{ $account->aname }}</span>
            <form method="POST" action="{{ route('portal.logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">{{ __('portal.logout') }}</button>
            </form>
        </div>
    </div>
</nav>

<div class="container">

    {{-- Balance summary --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <div class="text-muted small">{{ __('portal.current_balance') }}</div>
                    <div class="fs-4 fw-bold {{ $account->balance < 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($account->balance, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Date filter --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('portal.statement') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">{{ __('portal.from_date') }}</label>
                    <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('portal.to_date') }}</label>
                    <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">{{ __('portal.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Movements table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>{{ __('portal.date') }}</th>
                            <th>{{ __('portal.description') }}</th>
                            <th class="text-end">{{ __('portal.debit') }}</th>
                            <th class="text-end">{{ __('portal.credit') }}</th>
                            <th class="text-end">{{ __('portal.balance') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Opening balance row --}}
                        <tr class="table-secondary">
                            <td colspan="4" class="fw-bold">{{ __('portal.opening_balance') }}</td>
                            <td class="text-end fw-bold {{ $balanceBefore < 0 ? 'text-danger' : '' }}">
                                {{ number_format($balanceBefore, 2) }}
                            </td>
                        </tr>

                        @php $running = $balanceBefore; @endphp

                        @forelse($movements as $movement)
                            @php
                                $running += $movement->debit - $movement->credit;
                            @endphp
                            <tr>
                                <td>{{ $movement->crtime?->format('Y-m-d') }}</td>
                                <td>{{ $movement->notes ?? '#' . $movement->op_id }}</td>
                                <td class="text-end">
                                    @if($movement->debit > 0)
                                        {{ number_format($movement->debit, 2) }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($movement->credit > 0)
                                        {{ number_format($movement->credit, 2) }}
                                    @endif
                                </td>
                                <td class="text-end fw-bold {{ $running < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($running, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    {{ __('portal.no_movements') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($movements->hasPages())
        <div class="card-footer">
            {{ $movements->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>
</body>
</html>
