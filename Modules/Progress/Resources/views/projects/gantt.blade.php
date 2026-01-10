@extends('progress::layouts.daily-progress')

@section('title', 'Gantt Chart - ' . $project->name)

@section('content')
<div class="container-lg" x-data="ganttApp()" x-init="initGantt()">
    
    <!-- 1. Header Section -->
    <div class="card mb-4 border-0 shadow-sm overflow-hidden">
        <div class="card-header border-0 p-4 text-white d-flex justify-content-between align-items-center flex-wrap gap-3" 
             style="background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="las la-chart-bar fs-2"></i> <!-- Or chart-gantt icon -->
                    <h3 class="fw-bold mb-0">Gantt Chart - {{ $project->project_code ? $project->project_code . ' - ' : '' }} {{ $project->name }}</h3>
                </div>
                <div class="text-white-50">Interactive Timeline View</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('progress.project.index') }}" class="btn btn-light text-primary fw-bold shadow-sm">
                    <i class="las la-arrow-left me-1"></i> {{ __('general.back_to_projects') }}
                </a>
                <a href="{{ route('daily_progress.create') }}" class="btn btn-success fw-bold shadow-sm text-white">
                    <i class="las la-plus me-1"></i> {{ __('general.add_progress') }}
                </a>
            </div>
        </div>
        
        <!-- Project Info Strip -->
        <div class="card-body p-4 bg-white">
            <div class="row g-4">
                <div class="col-md-4 border-end">
                    <label class="text-muted small fw-bold text-uppercase mb-1">Client</label>
                    <div class="fs-5 fw-bold text-dark">{{ $project->client->name }}</div>
                </div>
                <div class="col-md-4 border-end">
                    <label class="text-muted small fw-bold text-uppercase mb-1">Project Status</label>
                    <div>
                        <span class="badge bg-success bg-opacity-10 text-success fs-6 px-3 py-2 rounded-2">
                            {{ __('general.' . $project->status) }}
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-muted small fw-bold text-uppercase mb-1">Working Zone</label>
                    <div class="fs-5 fw-bold text-dark">{{ $project->working_zone ?? __('general.not_specified') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Tasks -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="display-4 fw-bold text-primary mb-1">{{ $project->items->count() }}</div>
                    <div class="text-muted small text-uppercase fw-bold letter-spacing-1">Total Tasks</div>
                </div>
            </div>
        </div>
        
        <!-- Completed -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="display-4 fw-bold text-success mb-1">{{ $project->items->where('remaining_quantity', 0)->count() }}</div>
                    <div class="text-muted small text-uppercase fw-bold letter-spacing-1">Completed</div>
                </div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="display-4 fw-bold text-info mb-1">{{ $project->items->filter(fn($i) => $i->completed_quantity > 0 && $i->remaining_quantity > 0)->count() }}</div>
                    <div class="text-muted small text-uppercase fw-bold letter-spacing-1">In Progress</div>
                </div>
            </div>
        </div>

        <!-- Pending -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4 text-center">
                    <div class="display-4 fw-bold text-warning mb-1">{{ $project->items->where('completed_quantity', 0)->count() }}</div>
                    <div class="text-muted small text-uppercase fw-bold letter-spacing-1">Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Controls & Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                
                <!-- View & Zoom -->
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-bold me-1">View:</span>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm" :class="viewMode === 'day' ? 'btn-primary' : 'btn-outline-secondary'" @click="changeViewMode('day')">Daily</button>
                            <button type="button" class="btn btn-sm" :class="viewMode === 'week' ? 'btn-primary' : 'btn-outline-secondary'" @click="changeViewMode('week')">Weekly</button>
                            <button type="button" class="btn btn-sm" :class="viewMode === 'month' ? 'btn-primary' : 'btn-outline-secondary'" @click="changeViewMode('month')">Monthly</button>
                        </div>
                    </div>
                    
                    <div class="vr"></div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-bold me-1">Zoom:</span>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" @click="zoomIn()"><i class="las la-search-plus"></i></button>
                            <button class="btn btn-primary" @click="resetZoom()">Reset</button>
                            <button class="btn btn-outline-secondary" @click="zoomOut()"><i class="las la-search-minus"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="d-flex align-items-center gap-2">
                    <div class="position-relative">
                        <i class="las la-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" class="form-control form-control-sm ps-5 rounded-pill" placeholder="Search by name, category..." x-model="searchQuery" @input="filterTasks()" style="width: 220px;">
                    </div>
                    
                    <select class="form-select form-select-sm rounded-pill" x-model="statusFilter" @change="filterTasks()" style="width: 140px;">
                        <option value="all">All Statuses</option>
                        <option value="completed">Completed</option>
                        <option value="in_progress">In Progress</option>
                        <option value="pending">Pending</option>
                    </select>
                    
                    <div class="d-flex gap-1">
                        <input type="date" class="form-control form-control-sm rounded-pill" placeholder="From" style="width: 130px;">
                        <input type="date" class="form-control form-control-sm rounded-pill" placeholder="To" style="width: 130px;">
                    </div>
                    
                    <button class="btn btn-primary btn-sm rounded-pill px-3"><i class="las la-filter me-1"></i> Filter</button>
                    <button class="btn btn-secondary btn-sm rounded-pill px-3"><i class="las la-undo me-1"></i> Reset</button>
                </div>
            </div>
            
            <!-- Legend & Refresh -->
            <div class="mt-4 d-flex flex-column align-items-center justify-content-center border-top pt-3">
                <div class="d-flex flex-wrap justify-content-center gap-4 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-1" style="width: 12px; height: 12px; background: #fbbf24;"></span>
                        <span class="small fw-bold text-muted">Pending</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-1" style="width: 12px; height: 12px; background: #3b82f6;"></span>
                        <span class="small fw-bold text-muted">In Progress</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-1" style="width: 12px; height: 12px; background: #10b981;"></span>
                        <span class="small fw-bold text-muted">Completed</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-1" style="width: 12px; height: 12px; background: #ef4444;"></span>
                        <span class="small fw-bold text-muted">Critical</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-1" style="width: 12px; height: 12px; background: #f97316;"></span>
                        <span class="small fw-bold text-muted">Delayed</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-block rounded-1" style="width: 12px; height: 12px; background: #b91c1c;"></span>
                        <span class="small fw-bold text-muted">Today Line</span>
                    </div>
                </div>
                
                <button class="btn btn-primary btn-sm shadow-sm px-4" @click="initGantt()">
                    <i class="las la-sync me-2"></i> Refresh Chart
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Gantt Chart Area -->
    <div class="gantt-container-wrapper card border-0 shadow-sm overflow-hidden" style="height: 600px; display: flex; flex-direction: column;">
        <!-- Timeline Header -->
        <div class="gantt-header-wrapper" style="overflow: hidden; flex: 0 0 auto; border-bottom: 1px solid #eee;">
            <div class="gantt-header" :style="'transform: translateX(-' + scrollX + 'px); min-width: ' + totalWidth + 'px;'">
                <!-- Top scale (Months/Years) -->
                <div class="d-flex date-row-top">
                    <!-- Js will render this -->
                    <template x-for="col in headerGroups" :key="col.id">
                        <div class="header-group" :style="'width: ' + col.width + 'px;'" x-text="col.label"></div>
                    </template>
                </div>
                <!-- Bottom scale (Days/Weeks) -->
                <div class="d-flex date-row-bottom">
                    <template x-for="(col, index) in dateColumns" :key="col.dateStr">
                        <div class="date-cell" 
                            :class="{'weekend': col.isWeekend}"
                            :style="'width: ' + columnWidth + 'px;'" 
                            x-text="col.label"></div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Task Rows and Grid -->
        <div class="gantt-body-wrapper" x-ref="ganttBody" @scroll="handleScroll()" style="overflow: auto; flex: 1 1 auto; position: relative;">
            
            <!-- Grid Lines Layer -->
            <div class="gantt-grid" :style="'min-width: ' + totalWidth + 'px;'">
                <template x-for="col in dateColumns" :key="col.dateStr">
                   <div class="grid-column" :class="{'weekend': col.isWeekend, 'today': col.isToday}" :style="'width: ' + columnWidth + 'px;'"></div>
                </template>
            </div>

            <!-- Dependencies Layer -->
            <svg class="dependency-layer" :style="'width: ' + totalWidth + 'px; height: ' + totalHeight + 'px;'">
                <template x-for="line in dependencyLines" :key="line.id">
                   <path :d="line.path" fill="none" stroke="#aaa" stroke-width="2" marker-end="url(#arrowhead)" />
                </template>
                <defs>
                    <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                        <polygon points="0 0, 10 3.5, 0 7" fill="#888" />
                    </marker>
                </defs>
            </svg>

            <!-- Tasks Layer -->
            <div class="gantt-tasks" :style="'min-width: ' + totalWidth + 'px; padding-bottom: 20px;'">
                <template x-for="(task, index) in filteredTasks" :key="task.id">
                    <div class="gantt-row" @mouseenter="hoveredTask = task.id" @mouseleave="hoveredTask = null">
                        <!-- Task Info Column (Sticky) -->
                        <div class="task-label-col sticky-left bg-white border-end p-2" style="position: absolute; left: 0; width: 180px; z-index: 10;">
                             <div class="d-flex flex-column justify-content-center h-100 w-100" :style="'transform: translateX(' + scrollX + 'px);'">
                                <div class="fw-bold text-dark text-truncate mb-1" :title="task.name" x-text="task.name" style="font-size: 0.9rem;"></div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted small" style="font-size: 0.75rem;">Qty: <span x-text="task.total_quantity"></span>/Unit: <span x-text="'Unit'"></span></span> <!-- TODO: Add unit to item -->
                                </div>
                                <div class="mt-1 d-flex gap-1" v-if="task.name.toLowerCase().includes('rig')"> <!-- Example conditional badge -->
                                    <span class="badge bg-danger rounded-1" style="font-size: 0.65rem;">Critical</span>
                                    <span class="badge bg-info rounded-1" style="font-size: 0.65rem;">Machines</span>
                                </div>
                             </div>
                        </div>

                        <!-- Bar -->
                        <div class="task-bar-wrapper" :style="'margin-left: 180px; height: 48px; position: relative;'">
                             <div class="task-bar rounded shadow-sm d-flex align-items-center justify-content-center" 
                                  :class="getBarClass(task)"
                                  :style="'left: ' + task.left + 'px; width: ' + task.width + 'px;'"
                                  @click="openTaskModal(task)">
                                  
                                  <!-- Progress Fill -->
                                  <div class="bar-progress" :style="'width: ' + task.progress + '%'"></div>
                                  
                                  <!-- Label (Percentage Center) -->
                                  <div class="bar-label text-white fw-bold small" style="z-index: 5; font-size: 0.75rem;" x-text="task.progress + '%'"></div>

                                  <!-- Tooltip -->
                                  <div class="gantt-tooltip shadow" x-show="hoveredTask === task.id" x-transition>
                                      <div class="fw-bold mb-1" x-text="task.name"></div>
                                      <div class="small mb-1"><span class="text-muted">Start:</span> <span x-text="task.startDateDisplay"></span></div>
                                      <div class="small mb-1"><span class="text-muted">End:</span> <span x-text="task.endDateDisplay"></span></div>
                                      <div class="small"><span class="text-muted">Progress:</span> <span x-text="task.progress + '%'"></span></div>
                                  </div>
                             </div>
                        </div>
                    </div>
                </template>
            </div>

        </div>
    </div>

    <!-- Task Details Modal -->
    <div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" x-text="selectedTask?.name"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-3 mb-4">
                        <div class="text-center p-3 bg-light rounded flex-fill">
                            <small class="text-muted d-block">Start Date</small>
                            <div class="fw-bold" x-text="selectedTask?.startDateDisplay"></div>
                        </div>
                        <div class="text-center p-3 bg-light rounded flex-fill">
                            <small class="text-muted d-block">End Date</small>
                            <div class="fw-bold" x-text="selectedTask?.endDateDisplay"></div>
                        </div>
                         <div class="text-center p-3 bg-light rounded flex-fill">
                            <small class="text-muted d-block">Duration</small>
                            <div class="fw-bold" x-text="selectedTask?.duration + ' Days'"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                         <div class="d-flex justify-content-between mb-1">
                             <span class="fw-bold">Progress</span>
                             <span class="fw-bold" x-text="selectedTask?.progress + '%'"></span>
                         </div>
                         <div class="progress" style="height: 10px;">
                             <div class="progress-bar" role="progressbar" :style="'width: ' + selectedTask?.progress + '%'" :class="getBarClass(selectedTask)"></div>
                         </div>
                    </div>

                    <div class="mb-3" x-show="selectedTask?.notes">
                        <h6 class="fw-bold">Notes</h6>
                        <p class="text-muted small bg-light p-2 rounded" x-text="selectedTask?.notes"></p>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <!-- Link to add progress -->
                    <!-- In a real app, this would open the generic Daily Progress Modal, pre-filled -->
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Styles -->
<style>
    /* Gantt Core Styles */
    .gantt-header-wrapper {
        background: #f8f9fa;
        color: #495057;
        font-size: 0.85rem;
    }
    .date-row-top {
        border-bottom: 1px solid #dee2e6;
    }
    .header-group {
        padding: 5px;
        text-align: center;
        border-right: 1px solid #dee2e6;
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .date-row-bottom {
        display: flex;
    }
    .date-cell {
        height: 30px;
        line-height: 30px;
        text-align: center;
        border-right: 1px solid #eee;
        font-size: 0.75rem;
        flex-shrink: 0;
    }
    .date-cell.weekend {
        background-color: #f1f3f5;
        color: #adb5bd;
    }

    .gantt-body-wrapper {
        background-color: #fff;
    }
    
    .gantt-grid {
        position: absolute;
        top: 0;
        left: 180px; /* Offset for Task Label Column */
        height: 100%;
        display: flex;
        pointer-events: none;
        z-index: 0;
    }
    .grid-column {
        border-right: 1px solid #f8f9fa;
        flex-shrink: 0;
        height: 100%;
    }
    .grid-column.weekend {
        background-color: #f8f9fa;
    }
    .grid-column.today {
        border-left: 2px solid #e03131; /* Red line for today */
    }

    .dependency-layer {
        position: absolute;
        top: 0;
        left: 180px; /* Offset */
        z-index: 1;
        pointer-events: none;
    }

    .gantt-tasks {
        position: relative;
        z-index: 2;
        padding-top: 10px;
    }
    
    .gantt-row {
        height: 48px; /* Row height */
        display: flex;
        align-items: center;
        border-bottom: 1px solid #f1f3f5;
        position: relative;
    }
    .task-label-col {
        height: 100%;
        display: flex;
        align-items: center;
        border-right: 1px solid #eee;
        background: white;
    }

    .task-bar {
        position: absolute;
        top: 8px; /* Vertically centered in row roughly */
        height: 24px !important; /* Force height */
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.75rem;
        line-height: 24px;
        white-space: nowrap;
        overflow: visible;
        transition: transform 0.1s;
    }
    .task-bar:hover {
        transform: translateY(-2px);
        filter: brightness(0.95);
        z-index: 100; /* Bring to front on hover */
    }
    
    .bar-progress {
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        border-radius: 4px 0 0 4px;
    }
    .bar-label {
        position: relative;
        z-index: 2;
    }

    /* Status Colors (Gradients) */
    .bar-completed { background: linear-gradient(90deg, #10b981 0%, #34d399 100%); }
    .bar-completed .bar-progress { background: rgba(255,255,255,0.3); }

    .bar-in-progress { background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%); }
    .bar-delayed { background: linear-gradient(90deg, #ef4444 0%, #f87171 100%); }
    .bar-pending { background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%); }

    /* Tooltip */
    .gantt-tooltip {
        position: absolute;
        bottom: 110%;
        left: 50%;
        transform: translateX(-50%);
        background: #1f2937;
        color: white;
        padding: 10px;
        border-radius: 6px;
        font-size: 0.8rem;
        white-space: nowrap;
        pointer-events: none;
        z-index: 1000;
        min-width: 150px;
    }
    .gantt-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1f2937 transparent transparent transparent;
    }
    
    /* Scrollbar Styling */
    .gantt-body-wrapper::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .gantt-body-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }
    .gantt-body-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1; 
        border-radius: 4px;
    }
    .gantt-body-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8; 
    }
</style>

@php
    $tasksData = $project->items->map(function($item) {
        return [
            'id' => $item->id,
            'name' => $item->workItem ? $item->workItem->name : 'N/A',
            'startDate' => $item->start_date,
            'endDate' => $item->end_date,
            'notes' => $item->notes,
            'progress' => $item->calc_comp_percent ?? round(($item->completed_quantity / max($item->total_quantity, 1)) * 100),
            'predecessor' => $item->predecessor, 
            'dependency_type' => $item->dependency_type,
            'completed_quantity' => $item->completed_quantity,
            'total_quantity' => $item->total_quantity,
            'remaining_quantity' => $item->remaining_quantity
        ];
    });
@endphp

<!-- Scripts -->
<script>
    function ganttApp() {
        return {
            // Data
            project: @json($project),
            rawTasks: @json($tasksData),
            
            // State
            tasks: [],
            filteredTasks: [],
            dependencyLines: [],
            dateColumns: [],
            headerGroups: [],
            
            // Settings
            viewMode: 'day', // day, week, month
            columnWidth: 22,
            zoomLevel: 1,
            
            // Filters
            searchQuery: '',
            statusFilter: 'all',
            
            // UI
            scrollX: 0,
            totalWidth: 0,
            totalHeight: 0,
            hoveredTask: null,
            selectedTask: null,
            modalInstance: null,
            
            // Helpers
            minDate: null,
            maxDate: null,

            initGantt() {
                // Initialize Data
                this.tasks = this.rawTasks.map(t => ({
                    ...t,
                    startObj: new Date(t.startDate),
                    endObj: new Date(t.endDate),
                    startDateDisplay: t.startDate,
                    endDateDisplay: t.endDate
                }));

                // Determine Timeline Range
                if (this.tasks.length > 0) {
                    const startDates = this.tasks.map(t => t.startObj.getTime());
                    const endDates = this.tasks.map(t => t.endObj.getTime());
                    this.minDate = new Date(Math.min(...startDates));
                    this.maxDate = new Date(Math.max(...endDates));
                    
                    // Buffer dates
                    this.minDate.setDate(this.minDate.getDate() - 7);
                    this.maxDate.setDate(this.maxDate.getDate() + 14);
                } else {
                    this.minDate = new Date();
                    this.maxDate = new Date();
                    this.maxDate.setDate(this.maxDate.getDate() + 30);
                }

                this.render();
                
                // Initialize Modal
                this.modalInstance = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
            },

            render() {
                 this.generateDateColumns();
                 this.calculateTaskPositions();
                 this.filterTasks(); // Updates filteredTasks which drives the view
                 // Dependencies are calculated after filtering/rendering, but filteredTasks change triggers re-render.
                 // We need to wait for DOM updates for accurate dependency lines if we rely on DOM calc, 
                 // but here we calculate based on logical positions.
                 this.calculateDependencies();
            },

            generateDateColumns() {
                this.dateColumns = [];
                this.headerGroups = [];
                
                let current = new Date(this.minDate);
                const end = new Date(this.maxDate);
                let dayCount = 0;

                // Adjust column width based on view mode
                if (this.viewMode === 'day') this.columnWidth = 22 * this.zoomLevel;
                if (this.viewMode === 'week') this.columnWidth = 11 * this.zoomLevel; // Compressed days
                if (this.viewMode === 'month') this.columnWidth = 5 * this.zoomLevel;

                let currentMonthGroup = null;

                while (current <= end) {
                    const isWeekend = current.getDay() === 5 || current.getDay() === 6; // Fri/Sat
                    const isToday = current.toDateString() === new Date().toDateString();
                    
                    this.dateColumns.push({
                        dateObj: new Date(current),
                        dateStr: current.toISOString().split('T')[0],
                        label: current.getDate(),
                        dayName: current.toLocaleDateString('en-US', { weekday: 'narrow' }),
                        isWeekend: isWeekend,
                        isToday: isToday,
                        index: dayCount
                    });

                    // Header Groups (Months)
                    const monthKey = current.toLocaleString('default', { month: 'short', year: 'numeric' });
                    if (!currentMonthGroup || currentMonthGroup.label !== monthKey) {
                        currentMonthGroup = { label: monthKey, width: 0, id: monthKey };
                        this.headerGroups.push(currentMonthGroup);
                    }
                    currentMonthGroup.width += this.columnWidth;

                    current.setDate(current.getDate() + 1);
                    dayCount++;
                }
                
                this.totalWidth = dayCount * this.columnWidth;
            },

            calculateTaskPositions() {
                const oneDay = 24 * 60 * 60 * 1000;
                
                this.tasks.forEach(task => {
                    // Start Index from MinDate
                    const diffTime = task.startObj - this.minDate;
                    const startIndex = Math.floor(diffTime / oneDay);
                    
                    // Duration
                    const durationTime = task.endObj - task.startObj;
                    const durationDays = Math.floor(durationTime / oneDay) + 1;
                    task.duration = durationDays;

                    // Position
                    task.left = startIndex * this.columnWidth;
                    task.width = durationDays * this.columnWidth;
                });
            },

            calculateDependencies() {
                this.dependencyLines = [];
                // Simple logical mapping: id -> task
                const taskMap = {};
                this.tasks.forEach(t => taskMap[t.id] = t);

                this.filteredTasks.forEach((task, index) => {
                    if (task.predecessor && taskMap[task.predecessor]) {
                        const parent = taskMap[task.predecessor];
                        
                        // Check if parent is visible in filtered view (optional, but good for cleanliness)
                        if (!this.filteredTasks.find(t => t.id === parent.id)) return;
                        
                        // Calculate coordinates relative to the Gantt Container (offset 250px for sidebar)
                        // Parent (From): Right Edge, Vertical Center
                        // Task (To): Left Edge, Vertical Center
                        // Vertical Position: Row Index * 48px (row height) + 24px (center)
                        
                        const parentIndex = this.filteredTasks.findIndex(t => t.id === parent.id);
                        const taskIndex = index; // current loop index in filtered array
                        
                        const x1 = parent.left + parent.width;
                        const y1 = (parentIndex * 48) + 24; // row height 48, center 24
                        
                        const x2 = task.left;
                        const y2 = (taskIndex * 48) + 24;
                        
                        // Draw Path: Bezier Curve or L-Shape
                        // Simple 3-point connector: x1,y1 -> x1+10,y1 -> x2-10,y2 -> x2,y2
                        const midX = (x1 + x2) / 2;
                        
                        // Just an L shape for simplicity and robustness
                        // Move rigth 10px, go down/up, move right to target
                        const path = `M ${x1} ${y1} L ${x1 + 10} ${y1} L ${x1 + 10} ${y2} L ${x2} ${y2}`;
                        
                        this.dependencyLines.push({
                            id: `dep-${parent.id}-${task.id}`,
                            path: path
                        });
                    }
                });
                
                this.totalHeight = Math.max(600, this.filteredTasks.length * 48); // Ensure SVG covers all rows
            },

            filterTasks() {
                this.filteredTasks = this.tasks.filter(t => {
                    const matchesSearch = t.name.toLowerCase().includes(this.searchQuery.toLowerCase());
                    let matchesStatus = true;
                    if (this.statusFilter === 'completed') matchesStatus = t.progress >= 100;
                    if (this.statusFilter === 'in_progress') matchesStatus = t.progress > 0 && t.progress < 100;
                    if (this.statusFilter === 'pending') matchesStatus = t.progress === 0;
                    
                    return matchesSearch && matchesStatus;
                });
                
                // Recalculate dependencies based on new filtered list vertical positions
                this.calculateDependencies();
            },

            // Interactivity
            changeViewMode(mode) {
                this.viewMode = mode;
                this.render();
            },
            
            zoomIn() {
                if (this.zoomLevel < 2) {
                    this.zoomLevel += 0.2;
                    this.render();
                }
            },
            
            zoomOut() {
                if (this.zoomLevel > 0.5) {
                    this.zoomLevel -= 0.2;
                    this.render();
                }
            },
            
            resetZoom() {
                this.zoomLevel = 1;
                this.render();
            },

            handleScroll() {
                const el = this.$refs.ganttBody;
                this.scrollX = el.scrollLeft;
            },

            openTaskModal(task) {
                this.selectedTask = task;
                this.modalInstance.show();
            },

            // Helpers
            getBarClass(task) {
                if (task.progress >= 100) return 'bar-completed';
                if (task.progress > 0) return 'bar-in-progress';
                
                // Check delay: If today > endDate and progress < 100
                const today = new Date();
                if (today > task.endObj && task.progress < 100) return 'bar-delayed';
                
                return 'bar-pending';
            },

            getStatusBadgeClass(task) {
                if (task.progress >= 100) return 'bg-success';
                if (task.progress > 0) return 'bg-primary';
                return 'bg-secondary';
            }
        }
    }
</script>
@endsection
