<!-- Custom Alpine Overlay Modal (Clean Design) -->
<div x-show="isCustomizationModalOpen" 
     style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background: rgba(0,0,0,0.5) !important; z-index: 2147483647 !important; display: block !important;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Modal Content Card -->
    <div class="bg-white shadow-lg overflow-hidden d-flex flex-column" 
         @click.away="isCustomizationModalOpen = false"
         x-show="isCustomizationModalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         style="position: fixed !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; width: 800px; max-width: 90vw; max-height: 90vh; border-radius: 8px; margin: 0 !important; direction: rtl !important; z-index: 2147483648 !important;">

        <!-- Header -->
        <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-white">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fas fa-cog me-2"></i>{{ __('general.customize_view') }}
            </h5>
            <button type="button" class="btn-close" @click="isCustomizationModalOpen = false"></button>
        </div>

        <!-- Body (Scrollable) -->
        <div class="p-4 overflow-auto" style="flex-grow: 1;">
            <div class="row g-3">
                @php
                    $toggles = [
                        'showStats' => ['icon' => 'fas fa-chart-line', 'label' => __('general.statistics_cards')],
                        'showTeamMembers' => ['icon' => 'fas fa-users', 'label' => __('general.team_members')],
                        'showAdvancedChart' => ['icon' => 'fas fa-chart-area', 'label' => __('general.planned_vs_actual_chart')],
                        'showCategoriesChart' => ['icon' => 'fas fa-tags', 'label' => __('general.categories_chart')],
                        'showItemsByCategory' => ['icon' => 'fas fa-folder', 'label' => __('general.items_by_category')],
                        'showWorkItems' => ['icon' => 'fas fa-tasks', 'label' => __('general.work_items_progress')],
                        'showTimeline' => ['icon' => 'fas fa-stream', 'label' => __('general.project_timeline')],
                        'showClientInfo' => ['icon' => 'fas fa-user-tie', 'label' => __('general.client_info')],
                        'showCharts' => ['icon' => 'fas fa-chart-pie', 'label' => __('general.progress_charts')],
                        'showSubprojectsChart' => ['icon' => 'fas fa-project-diagram', 'label' => __('general.subprojects_chart')],
                        'showItemsBySubproject' => ['icon' => 'fas fa-list', 'label' => __('general.items_by_subproject')],
                        'showHierarchicalView' => ['icon' => 'fas fa-sitemap', 'label' => __('general.hierarchical_view')],
                        'showRecentActivity' => ['icon' => 'fas fa-history', 'label' => __('general.recent_activity')],
                    ];
                @endphp

                @foreach($toggles as $key => $data)
                    <div class="col-md-6">
                        <div class="d-flex align-items-center mb-2">
                            <input class="form-check-input me-2" type="checkbox" id="{{ $key }}Check" 
                                   x-model="viewSettings.{{ $key }}" 
                                   style="width: 1.2em; height: 1.2em; cursor: pointer;">
                            <label class="form-check-label d-flex align-items-center cursor-pointer text-dark" for="{{ $key }}Check" style="cursor: pointer;">
                                <i class="{{ $data['icon'] }} me-2 text-secondary" style="width: 20px;"></i>
                                {{ $data['label'] }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 pt-3 border-top d-flex justify-content-between">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                            @click="Object.keys(viewSettings).forEach(k => viewSettings[k] = true)">
                        <i class="fas fa-check-double me-1"></i> {{ __('general.select_all') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                            @click="Object.keys(viewSettings).forEach(k => viewSettings[k] = false)">
                        <i class="fas fa-times me-1"></i> {{ __('general.deselect_all') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 bg-light border-top d-flex justify-content-end gap-2">
             <button type="button" class="btn btn-secondary" @click="isCustomizationModalOpen = false">
                {{ __('general.close') }}
            </button>
             <button type="button" class="btn btn-primary px-4" @click="isCustomizationModalOpen = false">
                <i class="fas fa-save me-2"></i>{{ __('general.save_view') }}
            </button>
        </div>
    </div>
</div>
