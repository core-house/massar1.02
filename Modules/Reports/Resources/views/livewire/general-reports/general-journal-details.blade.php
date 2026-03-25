<?php

use Livewire\Volt\Component;
use Modules\Accounts\Models\AccHead;
use App\Models\JournalDetail;
use App\Models\JournalHead;
use App\Models\OperHead;
use App\Models\ProType;

use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $fromDate = '';
    public $toDate = '';
    public $accountId = '';
    public $operationType = '';

    public $search = '';
    public $perPage = 50;

    public function mount()
    {
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }

    public function with(): array
    {
        $query = JournalDetail::with(['head', 'accHead', 'costCenter', 'operHead'])
            ->when($this->fromDate, function ($q) {
                $q->whereDate('crtime', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('crtime', '<=', $this->toDate);
            })
            ->when($this->accountId, function ($q) {
                $q->where('account_id', $this->accountId);
            })
            ->when($this->operationType, function ($q) {
                $q->whereHas('head', function ($subQ) {
                    $subQ->where('pro_type', $this->operationType);
                });
            })
            // Cost center filtering removed as cost_center_id column doesn't exist in journal_details table
            ->when($this->search, function ($q) {
                $q->where(function ($subQ) {
                    $subQ
                        ->whereHas('accountHead', function ($accQ) {
                            $accQ->where('aname', 'like', '%' . $this->search . '%')->orWhere('code', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('head', function ($headQ) {
                            $headQ->where('info', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('crtime', 'asc');

        $journalDetails = $query->paginate($this->perPage);

        // Calculate totals
        $totalDebit = $query->sum('debit');
        $totalCredit = $query->sum('credit');
        $totalBalance = $totalDebit - $totalCredit;

        return [
            'journalDetails' => $journalDetails,
            'accounts' => AccHead::where('isdeleted', 0)->where('is_basic', 0)->orderBy('code')->get(),

            'operationTypes' => ProType::select('id', 'ptext')
                ->whereIn('id', OperHead::select('pro_type')->whereNotNull('pro_type')->distinct()->pluck('pro_type'))
                ->where('isdeleted', 0)
                ->orderBy('ptext', 'asc')
                ->get(),
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'totalBalance' => $totalBalance,
        ];
    }

    public function resetFilters()
    {
        $this->reset(['fromDate', 'toDate', 'accountId', 'operationType', 'search']);
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFromDate()
    {
        $this->resetPage();
    }

    public function updatedToDate()
    {
        $this->resetPage();
    }

    public function updatedAccountId()
    {
        $this->resetPage();
    }

    public function updatedOperationType()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }
}; ?>

<div style="font-family: 'Cairo', sans-serif; direction: rtl;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title font-bold fw-bold">{{ __('reports::reports.general_account_statement_details') }}</h3>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="from_date"
                                    class="form-label font-bold fw-bold">{{ __('reports::reports.from_date') }}:</label>
                                <input type="date" id="from_date" class="form-control" wire:model.live="fromDate">
                            </div>
                            <div class="col-md-2">
                                <label for="to_date" class="form-label font-bold fw-bold">{{ __('reports::reports.to_date') }}:</label>
                                <input type="date" id="to_date" class="form-control" wire:model.live="toDate">
                            </div>
                            <div class="col-md-2">
                                <label for="account_id"
                                    class="form-label font-bold fw-bold">{{ __('reports::reports.account') }}:</label>
                                <select id="account_id" class="form-control" wire:model.live="accountId">
                                    <option value="">{{ __('reports::reports.all_accounts') }}</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->aname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="operation_type"
                                    class="form-label font-bold fw-bold">{{ __('reports::reports.operation_type') }}:</label>
                                <select id="operation_type" class="form-control" wire:model.live="operationType">
                                    <option value="">{{ __('reports::reports.all_operations') }}</option>
                                    @foreach ($operationTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->ptext }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="search"
                                    class="form-label font-bold fw-bold">{{ __('reports::reports.searching') }}:</label>
                                <input type="text" id="search" class="form-control"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="{{ __('reports::reports.search_in_account_or_description') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label for="per_page"
                                    class="form-label font-bold fw-bold">{{ __('reports::reports.results_per_page') }}:</label>
                                <select id="per_page" class="form-control" wire:model.live="perPage">
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="200">200</option>
                                </select>
                            </div>
                            <div class="col-md-10 d-flex align-items-end">
                                <button class="btn btn-secondary me-2" wire:click="resetFilters">
                                    <i class="fas fa-refresh me-1"></i>{{ __('reports::reports.reset') }}
                                </button>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="card bg-danger text-white shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-arrow-down fa-2x mb-2 opacity-75"></i>
                                        <h5 class="card-title font-bold fw-bold">{{ __('reports::reports.total_debit') }}</h5>
                                        <h3 class="font-bold fw-bold mb-0">{{ number_format($totalDebit, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-arrow-up fa-2x mb-2 opacity-75"></i>
                                        <h5 class="card-title font-bold fw-bold">{{ __('reports::reports.total_credit') }}</h5>
                                        <h3 class="font-bold fw-bold mb-0">{{ number_format($totalCredit, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div
                                    class="card {{ $totalBalance >= 0 ? 'bg-info' : 'bg-warning' }} text-white shadow-sm">
                                    <div class="card-body text-center">
                                        <i class="fas fa-balance-scale fa-2x mb-2 opacity-75"></i>
                                        <h5 class="card-title font-bold fw-bold">{{ __('reports::reports.balance') }}</h5>
                                        <h3 class="font-bold fw-bold mb-0">{{ number_format($totalBalance, 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="table-light">
                                    <tr class="text-center">
                                        <th class="font-bold fw-bold" style="min-width: 100px;">{{ __('reports::reports.date') }}</th>
                                        <th class="font-bold fw-bold" style="min-width: 80px;">{{ __('reports::reports.operation_number') }}</th>
                                        <th class="font-bold fw-bold" style="min-width: 150px;">{{ __('reports::reports.operation_name') }}</th>
                                        <th class="font-bold fw-bold" style="min-width: 80px;">{{ __('reports::reports.journal_number') }}</th>
                                        <th class="font-bold fw-bold" style="min-width: 200px;">{{ __('reports::reports.account') }}</th>
                                        <th class="font-bold fw-bold" style="min-width: 250px;">{{ __('reports::reports.description') }}</th>
                                        <th class="font-bold fw-bold text-danger" style="min-width: 120px;">{{ __('reports::reports.debit') }}</th>
                                        <th class="font-bold fw-bold text-success" style="min-width: 120px;">{{ __('reports::reports.credit') }}</th>
                                        <th class="font-bold fw-bold text-info" style="min-width: 120px;">{{ __('reports::reports.balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($journalDetails as $detail)
                                        <tr>
                                            <td class="text-center font-bold">
                                                {{ $detail->crtime ? \Carbon\Carbon::parse($detail->crtime)->format('Y-m-d') : '---' }}
                                            </td>
                                            <td class="text-center font-bold">
                                                {{ $detail->op_id ?? '---' }}
                                            </td>
                                            <td class="text-center font-bold">
                                                @if ($detail->operHead && $detail->op_id)
                                                    @php
                                                        $operationType = $detail->operHead->type->ptext ?? '---';
                                                        $editRoute = $detail->operHead->getEditRoute();
                                                    @endphp
                                                    @if (\Illuminate\Support\Facades\Route::has($editRoute))
                                                        <a href="{{ route($editRoute, $detail->op_id) }}"
                                                            class="text-decoration-none text-primary fw-bold hover-shadow"
                                                            title="{{ __('reports::reports.edit_operation') }}" target="_blank">
                                                            <i class="fas fa-edit me-1"></i>{{ $operationType }}
                                                        </a>
                                                    @else
                                                        <span class="text-muted"
                                                            title="{{ __('reports::reports.cannot_edit_operation_type') }}">
                                                            {{ $operationType }}
                                                        </span>
                                                    @endif
                                                @else
                                                    {{ $detail->operHead->pname ?? '---' }}
                                                @endif
                                            </td>
                                            <td class="text-center font-bold">
                                                {{ $detail->journal_id ?? '---' }}
                                            </td>
                                            <td class="font-bold">
                                                <strong
                                                    class="text-primary">{{ $detail->accHead->code ?? '---' }}</strong>
                                                <br><small
                                                    class="text-muted">{{ $detail->accHead->aname ?? '---' }}</small>
                                            </td>
                                            <td class="font-bold">{{ $detail->info ?? '---' }}</td>
                                            <td class="text-end font-bold text-danger fw-bolder fs-6">
                                                {{ $detail->debit > 0 ? number_format($detail->debit, 2) : '0.00' }}
                                            </td>
                                            <td class="text-end font-bold text-success fw-bolder fs-6">
                                                {{ $detail->credit > 0 ? number_format($detail->credit, 2) : '0.00' }}
                                            </td>
                                            <td
                                                class="text-end font-bold {{ $detail->debit - $detail->credit > 0 ? 'text-success' : 'text-danger' }} fs-5">
                                                <strong>{{ number_format($detail->debit - $detail->credit, 2) }}</strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center font-bold py-4">
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                    {{ __('reports::reports.no_data_to_display') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if ($journalDetails->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $journalDetails->links() }}
                            </div>
                        @endif

                        <!-- Summary Footer -->
                        @if ($journalDetails->count() > 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-success shadow-sm">
                                        <i class="fas fa-chart-bar fa-2x float-start me-3 mb-2"></i>
                                        <strong
                                            class="font-bold fw-bold fs-5 d-block">{{ __('reports::reports.report_summary') }}:</strong>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <span class="font-bold">{{ __('reports::reports.operations_count') }}:</span>
                                                <strong class="text-primary">{{ $journalDetails->total() }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="font-bold">{{ __('reports::reports.total_debit') }}:</span>
                                                <strong
                                                    class="text-danger">{{ number_format($totalDebit, 2) }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="font-bold">{{ __('reports::reports.total_credit') }}:</span>
                                                <strong
                                                    class="text-success">{{ number_format($totalCredit, 2) }}</strong>
                                            </div>
                                            <div class="col-md-3">
                                                <span class="font-bold">{{ __('reports::reports.balance') }}:</span>
                                                <strong
                                                    class="{{ $totalBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($totalBalance, 2) }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

