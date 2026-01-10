<div class="card stat-card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold text-primary">
            <i class="fas fa-sitemap me-2"></i>{{ __('general.hierarchical_view') }}
        </h5>
        
        <!-- Search & Expand Buttons (Optional future enhancement) -->
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search..." style="max-width: 200px;">
            <button class="btn btn-sm btn-primary" @click="expandAll = !expandAll">{{ __('general.toggle_all') }}</button>
        </div>
    </div>
    <div class="card-body p-0" x-data="{ activeItem: null }">
        <div class="accordion accordion-flush" id="hierarchicalAccordion">
            
            @foreach($hierarchicalData as $subprojectName => $subData)
            <div class="accordion-item mb-2 border rounded overflow-hidden">
                <h2 class="accordion-header">
                    <button class="accordion-button" 
                            type="button" 
                            :class="{ 'collapsed': activeItem !== {{ $loop->index }} }"
                            @click="activeItem === {{ $loop->index }} ? activeItem = null : activeItem = {{ $loop->index }}">
                        <div class="d-flex justify-content-between w-100 align-items-center pe-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-folder-open text-primary"></i>
                                <span class="fw-bold">{{ $subprojectName }}</span>
                                <span class="badge bg-info text-dark">{{ $subData['progress'] }}%</span>
                            </div>
                            <div class="progress" style="width: 150px; height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                     style="width: {{ $subData['progress'] }}%"></div>
                            </div>
                        </div>
                    </button>
                </h2>
                <div x-show="activeItem === {{ $loop->index }}" 
                     class="accordion-collapse border-0"
                     style="display: none;">
                    <div class="accordion-body bg-white">
                        
                        @foreach($subData['categories'] as $categoryName => $catData)
                        <div class="mb-4">
                            <h6 class="text-muted fw-bold border-bottom pb-2 mb-3">
                                <i class="fas fa-tags me-1"></i> {{ $categoryName }}
                                <span class="badge bg-secondary rounded-pill ms-2">{{ count($catData['items']) }} Items</span>
                            </h6>
                            
                            <div class="row g-3">
                                @foreach($catData['items'] as $item)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title text-dark fw-bold mb-0 text-truncate" title="{{ $item->workItem->name ?? 'Unknown' }}">
                                                    {{ $item->workItem->name ?? 'Unknown' }}
                                                </h6>
                                                <span class="badge bg-white text-muted border">{{ $item->workItem->unit ?? '-' }}</span>
                                            </div>
                                            
                                            <div class="small text-muted mb-2">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') : '-' }} 
                                                <i class="fas fa-arrow-right mx-1"></i>
                                                {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') : '-' }}
                                            </div>

                                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                                <span><i class="fas fa-check-circle text-success me-1"></i>{{ round($item->completed_quantity, 1) }}</span>
                                                <span class="text-muted">/ {{ round($item->total_quantity, 1) }}</span>
                                            </div>
                                            
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar {{ $item->completion_percentage >= 100 ? 'bg-success' : 'bg-warning' }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $item->completion_percentage }}%"></div>
                                            </div>
                                            <div class="text-end mt-1" style="font-size: 0.75rem;">
                                                @if($item->status)
                                                    <span class="badge" 
                                                          style="background-color: {{ $item->status->color }}; color: #fff;">
                                                        <i class="{{ $item->status->icon }} me-1"></i>
                                                        {{ $item->status->name }}
                                                    </span>
                                                @endif
                                                <span class="fw-bold">{{ round($item->completion_percentage, 1) }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</div>
