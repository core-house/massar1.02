<div>
    <div class="container-fluid">
        {{-- Alert Messages --}}
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="las la-check-circle me-2"></i>
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="las la-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="las la-exclamation-triangle me-2"></i>
                <strong>{{ __('manufacturing::manufacturing.form has errors') }}:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stage Management View --}}
        @if ($view_mode === 'stages' && isset($viewing_order))
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header text-white d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 fw-bold">
                                    <i class="las la-tasks me-2"></i>
                                    {{ __('manufacturing::manufacturing.manufacturing order stages') }}:
                                    {{ $viewing_order->order_number }}
                                </h4>
                                <small class="opacity-75">{{ __('manufacturing::manufacturing.branch') }}:
                                    {{ $viewing_order->branch->name ?? '-' }}</small>
                            </div>
                            <button wire:click="backToList" class="btn btn-light btn-sm">
                                <i class="las la-arrow-right me-1"></i> {{ __('manufacturing::manufacturing.back') }}
                            </button>
                        </div>

                        <div class="card-body p-0">
                            {{-- Order Summary --}}
                            <div class="row g-0 border-bottom">
                                <div class="col-md-3 border-end p-3 text-center">
                                    <div class="text-muted small mb-1">
                                        {{ __('manufacturing::manufacturing.total stages') }}</div>
                                    <div class="fs-3 fw-bold text-primary">{{ $viewing_order->stages->count() }}
                                    </div>
                                </div>
                                <div class="col-md-3 border-end p-3 text-center">
                                    <div class="text-muted small mb-1">
                                        {{ __('manufacturing::manufacturing.total quantity') }}</div>
                                    <div class="fs-3 fw-bold text-success">
                                        {{ number_format($viewing_order->stages->sum('pivot.quantity')) }}
                                    </div>
                                </div>
                                <div class="col-md-3 border-end p-3 text-center">
                                    <div class="text-muted small mb-1">
                                        {{ __('manufacturing::manufacturing.estimated duration') }}</div>
                                    <div class="fs-3 fw-bold text-info">
                                        {{ number_format($viewing_order->estimated_duration, 1) }}</div>
                                    <small class="text-muted">{{ __('manufacturing::manufacturing.hours') }}</small>
                                </div>
                                <div class="col-md-3 p-3 text-center">
                                    <div class="text-muted small mb-1">
                                        {{ __('manufacturing::manufacturing.order status') }}</div>
                                    @php
                                        $orderStatusBadge = [
                                            'stopped' => 'danger',
                                            'in_progress' => 'primary',
                                            'completed' => 'success',
                                        ];
                                        $orderStatusText = [
                                            'stopped' => __('manufacturing::manufacturing.stopped'),
                                            'in_progress' => __('manufacturing::manufacturing.in_progress'),
                                            'completed' => __('manufacturing::manufacturing.completed'),
                                        ];
                                    @endphp
                                    <span
                                        class="badge bg-{{ $orderStatusBadge[$viewing_order->status] ?? 'secondary' }} fs-6 px-3 py-2">
                                        {{ $orderStatusText[$viewing_order->status] ?? $viewing_order->status }}
                                    </span>
                                </div>
                            </div>

                            {{-- Stages Timeline --}}
                            <div class="p-4">
                                <div class="timeline">
                                    @foreach ($viewing_order->stages as $index => $stage)
                                        @php
                                            $statusEnum = \Modules\Manufacturing\Enums\ManufacturingStageStatus::from(
                                                $stage->pivot->status,
                                            );
                                            $isCompleted = $stage->pivot->status === 'completed';
                                            $isInProgress = $stage->pivot->status === 'in_progress';
                                        @endphp

                                        <div class="timeline-item mb-4 position-relative">
                                            <div class="row g-0 align-items-center">
                                                {{-- Timeline Icon --}}
                                                <div class="col-auto">
                                                    <div class="timeline-badge bg-{{ $statusEnum->color() }} text-white d-flex align-items-center justify-content-center rounded-circle"
                                                        style="width: 50px; height: 50px;">
                                                        <i class="las {{ $statusEnum->icon() }} fs-3"></i>
                                                    </div>
                                                    @if (!$loop->last)
                                                        <div class="timeline-line bg-{{ $isCompleted ? 'success' : 'secondary' }}"
                                                            style="width: 3px; height: 80px; margin: 5px auto;">
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Stage Card --}}
                                                <div class="col">
                                                    <div
                                                        class="card border-{{ $statusEnum->color() }} ms-3 {{ $isInProgress ? 'shadow' : '' }}">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-6">
                                                                    <h5 class="mb-2 fw-bold">
                                                                        <span
                                                                            class="badge bg-light text-dark me-2">{{ $index + 1 }}</span>
                                                                        {{ $stage->name }}
                                                                    </h5>
                                                                    @if ($stage->pivot->notes)
                                                                        <p class="text-muted small mb-2">
                                                                            <i class="las la-comment me-1"></i>
                                                                            {{ $stage->pivot->notes }}
                                                                        </p>
                                                                    @endif
                                                                    <div class="d-flex gap-3 flex-wrap">
                                                                        <span class="badge bg-light text-dark">
                                                                            <i class="las la-clock me-1"></i>
                                                                            {{ number_format($stage->pivot->estimated_duration, 1) }}
                                                                            {{ __('manufacturing::manufacturing.hours') }}
                                                                        </span>

                                                                        <span class="badge bg-light text-dark">
                                                                            <i class="las la-clock me-1"></i>
                                                                            {{ number_format($stage->pivot->estimated_duration, 1) }}
                                                                            {{ __('manufacturing::manufacturing.hours') }}
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6 text-md-end mt-3 mt-md-0">

                                                                    <a href="{{ route('manufacturing.create', ['order_id' => $viewing_order->id, 'stage_id' => $stage->id]) }}"
                                                                        class="btn btn-sm btn-success"
                                                                        title="{{ __('manufacturing::manufacturing.create manufacturing invoice') }}">
                                                                        <i class="las la-file-invoice"></i>
                                                                        <span
                                                                            class="d-none d-lg-inline ms-1">{{ __('manufacturing::manufacturing.create invoice') }}</span>
                                                                    </a>

                                                                    <div class="btn-group" role="group">
                                                                        @foreach (\Modules\Manufacturing\Enums\ManufacturingStageStatus::cases() as $status)
                                                                            <button type="button"
                                                                                wire:click="updateStageStatus({{ $viewing_order->id }}, {{ $stage->id }}, '{{ $status->value }}')"
                                                                                class="btn btn-sm btn-{{ $stage->pivot->status === $status->value ? $status->color() : 'outline-' . $status->color() }}"
                                                                                title="{{ $status->label() }}">
                                                                                <i
                                                                                    class="las {{ $status->icon() }}"></i>
                                                                                <span
                                                                                    class="d-none d-lg-inline ms-1">{{ $status->label() }}</span>
                                                                            </button>
                                                                        @endforeach

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Form View --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header">
                            <h1 class="mb-0 fw-bold">
                                <i class="las la-{{ $order_id ? 'edit' : 'plus-circle' }} me-2"></i>
                                {{ $order_id ? __('manufacturing::manufacturing.edit manufacturing order') : __('manufacturing::manufacturing.create new manufacturing order') }}
                            </h1>
                        </div>

                        <div class="card-body p-4">
                            <form wire:submit.prevent="{{ $order_id ? 'updateOrder' : 'createOrder' }}">
                                {{-- Basic Info --}}
                                <div class="row g-3 mb-4">
                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i
                                                class="las la-hashtag me-1"></i>{{ __('manufacturing::manufacturing.order number') }}
                                        </label>
                                        <input wire:model="order_number" type="text"
                                            class="form-control @error('order_number') is-invalid @enderror"
                                            {{ $order_id ? '' : 'readonly' }}>
                                        @error('order_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <livewire:app::searchable-select :model="App\Models\Item::class" label-field="name"
                                            :selected-id="$item_id" wire-model="item_id"
                                            label="{{ __('manufacturing::manufacturing.item/product') }}"
                                            placeholder="{{ __('manufacturing::manufacturing.search for item or add new...') }}"
                                            :key="'product-select-' . ($order_id ?? 'new') . '-' . $item_id" :additional-data="['code' => $this->generateItemCode()]" />
                                        @error('item_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label fw-semibold">
                                            <i
                                                class="las la-building me-1"></i>{{ __('manufacturing::manufacturing.branch') }}
                                        </label>
                                        <select wire:model="branch_id"
                                            class="form-select @error('branch_id') is-invalid @enderror">
                                            <option value="">
                                                {{ __('manufacturing::manufacturing.select branch') }}</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-2 mb-3">
                                        <label class="form-label fw-semibold">
                                            <i
                                                class="las la-info-circle me-1"></i>{{ __('manufacturing::manufacturing.status') }}
                                        </label>
                                        <select wire:model="status"
                                            class="form-select @error('status') is-invalid @enderror">
                                            <option value="stopped">متوقف/تم التعطيل</option>
                                            <option value="in_progress">قيد التنفيذ</option>
                                            <option value="completed">مكتمل</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">
                                            <i
                                                class="las la-align-left me-1"></i>{{ __('manufacturing::manufacturing.description') }}
                                        </label>
                                        <textarea wire:model="description" class="form-control @error('description') is-invalid @enderror" rows="1"></textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    </div>

                                    {{-- Template Option --}}
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" wire:model.live="is_template"
                                                            class="form-check-input" id="saveTemplate">
                                                        <label for="saveTemplate"
                                                            class="form-check-label fw-semibold">
                                                            <i
                                                                class="las la-save me-1"></i>{{ __('manufacturing::manufacturing.save as template') }}
                                                        </label>
                                                    </div>
                                                    @if ($is_template)
                                                        <div class="mt-3">
                                                            <input wire:model="template_name" type="text"
                                                                placeholder="{{ __('manufacturing::manufacturing.template name') }}"
                                                                class="form-control @error('template_name') is-invalid @enderror">
                                                            @error('template_name')
                                                                <div class="invalid-feedback">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if (count($templates) > 0)
                                            <div class="col-md-6">
                                                <div class="card bg-light border-0">
                                                    <div class="card-body">
                                                        <label class="form-label fw-semibold">
                                                            <i
                                                                class="las la-file-import me-1"></i>{{ __('manufacturing::manufacturing.load ready template') }}
                                                        </label>
                                                        <select wire:change="loadTemplate($event.target.value)"
                                                            class="form-select">
                                                            <option value="">
                                                                {{ __('manufacturing::manufacturing.select template') }}
                                                            </option>
                                                            @foreach ($templates as $template)
                                                                <option value="{{ $template->id }}">
                                                                    {{ $template->template_name }}
                                                                    ({{ $template->stages->count() }}
                                                                    {{ __('manufacturing::manufacturing.stages') }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Stages Section --}}
                                    <div class="card border-primary mb-4">
                                        <div class="card-header bg-opacity-10 border-primary">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0 fw-bold text-primary">
                                                    <i
                                                        class="las la-layer-group me-2"></i>{{ __('manufacturing::manufacturing.manufacturing stages') }}
                                                </h5>
                                                @if (!empty($selected_stages))
                                                    <span class="badge bg-light">{{ count($selected_stages) }}
                                                        {{ __('manufacturing::manufacturing.stages') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <select wire:change="addStage($event.target.value)"
                                                        class="form-select form-select-lg" id="stageSelect">
                                                        <option value="">
                                                            <i class="las la-plus-circle"></i>
                                                            {{ __('manufacturing::manufacturing.select stage to add') }}
                                                        </option>
                                                        @foreach ($available_stages as $stage)
                                                            <option value="{{ $stage->id }}">
                                                                {{ $stage->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            @if (empty($selected_stages))
                                                <div class="alert alert-info d-flex align-items-center mb-0"
                                                    role="alert">
                                                    <i class="las la-info-circle fs-3 me-3"></i>
                                                    <div>
                                                        <strong>{{ __('manufacturing::manufacturing.no stages added yet') }}</strong>
                                                        <p class="mb-0 small">
                                                            {{ __('manufacturing::manufacturing.please select at least one stage from the list above') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="table-responsive">
                                                    <table class="table table-hover align-middle mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="5%" class="text-center">#</th>
                                                                <th width="30%">
                                                                    {{ __('manufacturing::manufacturing.stage') }}</th>
                                                                <th width="15%">
                                                                    {{ __('manufacturing::manufacturing.duration (hours)') }}
                                                                </th>
                                                                <th width="20%">
                                                                    {{ __('manufacturing::manufacturing.status') }}
                                                                </th>
                                                                <th width="15%">
                                                                    {{ __('manufacturing::manufacturing.notes') }}</th>
                                                                <th width="5%" class="text-center">
                                                                    {{ __('manufacturing::manufacturing.action') }}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($selected_stages as $index => $stage)
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <span
                                                                            class="badge bg-light">{{ $index + 1 }}</span>
                                                                    </td>
                                                                    <td>
                                                                        <strong
                                                                            class="text-dark">{{ $stage['name'] ?? __('manufacturing::manufacturing.not specified') }}</strong>
                                                                    </td>
                                                                    <td>
                                                                        <input
                                                                            wire:model="selected_stages.{{ $index }}.estimated_duration"
                                                                            type="number" step="0.01"
                                                                            min="0"
                                                                            class="form-control form-control-sm @error('selected_stages.' . $index . '.estimated_duration') is-invalid @enderror">
                                                                        @error('selected_stages.' . $index .
                                                                            '.estimated_duration')
                                                                            <div class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </td>
                                                                    <td>
                                                                        <select
                                                                            wire:model="selected_stages.{{ $index }}.status"
                                                                            class="form-select form-select-sm @error('selected_stages.' . $index . '.status') is-invalid @enderror">
                                                                            @foreach ($stage_statuses as $status)
                                                                                <option value="{{ $status->value }}">
                                                                                    {{ $status->label() }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        @error('selected_stages.' . $index . '.status')
                                                                            <div class="invalid-feedback">
                                                                                {{ $message }}
                                                                            </div>
                                                                        @enderror
                                                                    </td>
                                                                    <td>
                                                                        <input
                                                                            wire:model="selected_stages.{{ $index }}.notes"
                                                                            type="text"
                                                                            placeholder="{{ __('manufacturing::manufacturing.notes') }}"
                                                                            class="form-control form-control-sm">
                                                                    </td>
                                                                    <td class="text-center">
                                                                        <button type="button"
                                                                            wire:click="removeStage({{ $index }})"
                                                                            class="btn btn-sm btn-danger"
                                                                            onclick="return confirm('{{ __('manufacturing::manufacturing.are you sure you want to delete this stage?') }}')">
                                                                            <i class="las la-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                        <tfoot class="table-light">
                                                            <tr class="fw-bold">
                                                                <td colspan="2" class="text-end">
                                                                    {{ __('manufacturing::manufacturing.total') }}:
                                                                </td>
                                                                <td class="text-info">
                                                                    {{ number_format(collect($selected_stages)->sum('estimated_duration'), 2) }}
                                                                    {{ __('manufacturing::manufacturing.hours') }}
                                                                </td>
                                                                <td colspan="3"></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-lg px-4">
                                            <i class="las la-{{ $order_id ? 'save' : 'plus-circle' }} me-2"></i>
                                            {{ $order_id ? __('manufacturing::manufacturing.update order') : __('manufacturing::manufacturing.create order') }}
                                        </button>
                                        <button type="button" wire:click="resetForm"
                                            class="btn btn-secondary btn-lg px-4">
                                            <i
                                                class="las la-redo me-2"></i>{{ __('manufacturing::manufacturing.reset') }}
                                        </button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Orders List --}}
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0 fw-bold">
                                <i
                                    class="las la-list me-2"></i>{{ __('manufacturing::manufacturing.manufacturing orders list') }}
                            </h4>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('manufacturing::manufacturing.order number') }}</th>
                                            <th>{{ __('manufacturing::manufacturing.branch') }}</th>
                                            <th class="text-center">{{ __('manufacturing::manufacturing.status') }}
                                            </th>
                                            <th class="text-center">
                                                {{ __('manufacturing::manufacturing.number of stages') }}</th>
                                            </th>
                                            <th class="text-end">
                                                {{ __('manufacturing::manufacturing.estimated duration') }}</th>
                                            <th class="text-center">{{ __('manufacturing::manufacturing.actions') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($orders as $order)
                                            <tr>
                                                <td>
                                                    <strong class="text-primary">{{ $order->order_number }}</strong>
                                                    @if ($order->is_template)
                                                        <span class="badge bg-info ms-2">
                                                            <i class="las la-file-alt"></i>
                                                            {{ __('manufacturing::manufacturing.template') }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="las la-building text-muted me-1"></i>
                                                    {{ $order->branch->name ?? '-' }}
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $statusClass = [
                                                            'stopped' => 'danger',
                                                            'in_progress' => 'primary',
                                                            'completed' => 'success',
                                                        ];
                                                        $statusText = [
                                                            'stopped' => 'متوقف/تم التعطيل',
                                                            'in_progress' => 'قيد التنفيذ',
                                                            'completed' => 'مكتمل',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $statusClass[$order->status] ?? 'secondary' }} px-3 py-2">
                                                        {{ $statusText[$order->status] ?? $order->status }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light">{{ $order->stages->count() }}</span>
                                                </td>

                                                <td class="text-end">
                                                    <strong
                                                        class="text-info">{{ number_format($order->estimated_duration, 1) }}</strong>
                                                    <small
                                                        class="text-muted">{{ __('manufacturing::manufacturing.hours') }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <button wire:click="viewOrderStages({{ $order->id }})"
                                                            class="btn btn-sm btn-info"
                                                            title="{{ __('manufacturing::manufacturing.view stages') }}">
                                                            <i class="las la-tasks"></i>
                                                        </button>
                                                        <button wire:click="editOrder({{ $order->id }})"
                                                            class="btn btn-sm btn-primary"
                                                            title="{{ __('manufacturing::manufacturing.edit') }}">
                                                            <i class="las la-edit"></i>
                                                        </button>
                                                        <button wire:click="deleteOrder({{ $order->id }})"
                                                            class="btn btn-sm btn-danger">
                                                            <i class="las la-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-5">
                                                    <i class="las la-inbox fs-1 text-muted d-block mb-3"></i>
                                                    <p class="text-muted mb-0">
                                                        {{ __('manufacturing::manufacturing.no manufacturing orders') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if ($orders->hasPages())
                            <div class="card-footer bg-light">
                                {{ $orders->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        @endif
    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', function() {
                Livewire.on('stageAdded', () => {
                    const select = document.getElementById('stageSelect');
                    if (select) select.value = '';
                });

                Livewire.on('templateLoaded', () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('manufacturing::manufacturing.loaded') }}',
                            text: '{{ __('manufacturing::manufacturing.template loaded successfully!') }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert('{{ __('manufacturing::manufacturing.template loaded successfully!') }}');
                    }
                });

                Livewire.on('orderCreated', () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('manufacturing::manufacturing.created') }}',
                            text: '{{ __('manufacturing::manufacturing.manufacturing order created successfully!') }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(
                            '{{ __('manufacturing::manufacturing.manufacturing order created successfully!') }}');
                    }
                });

                Livewire.on('orderUpdated', () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('manufacturing::manufacturing.updated') }}',
                            text: '{{ __('manufacturing::manufacturing.manufacturing order updated successfully!') }}',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(
                            '{{ __('manufacturing::manufacturing.manufacturing order updated successfully!') }}');
                    }
                });

                Livewire.on('stageStatusUpdated', () => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '{{ __('manufacturing::manufacturing.updated') }}',
                            text: '{{ __('manufacturing::manufacturing.stage status updated successfully!') }}',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        alert('{{ __('manufacturing::manufacturing.stage status updated successfully!') }}');
                    }
                });
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .bg-gradient-dark {
                background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            }

            .timeline-badge {
                box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.9);
                position: relative;
                z-index: 2;
            }

            .timeline-line {
                opacity: 0.3;
                transition: all 0.3s ease;
            }

            .card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .card:hover {
                transform: translateY(-2px);
            }

            .table tbody tr {
                transition: background-color 0.2s ease;
            }

            .table tbody tr:hover {
                background-color: rgba(0, 123, 255, 0.05);
            }

            .btn-group .btn {
                margin: 0 2px;
            }

            .timeline-item {
                animation: fadeInUp 0.5s ease;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }

            .badge {
                font-weight: 500;
                letter-spacing: 0.3px;
            }
        </style>
    @endpush
</div>
