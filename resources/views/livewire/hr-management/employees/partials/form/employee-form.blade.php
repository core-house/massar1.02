{{-- Employee Form with Alpine.js Tabs --}}
@php
    // Get isEdit from Livewire component or default to false
    $isEdit = $isEdit ?? $this->isEdit ?? false;
    $tabs = [
            'personal' => [
                'icon' => 'fa-user',
                'label' => __('البيانات الشخصية'),
                'errors' => ['name', 'email', 'phone', 'status', 'image', 'gender', 'date_of_birth', 'nationalId', 'marital_status', 'education', 'information']
            ],
            'location' => [
                'icon' => 'fa-map-marker-alt',
                'label' => __('الموقع'),
                'errors' => ['country_id', 'city_id', 'state_id', 'town_id']
            ],
            'job' => [
                'icon' => 'fa-briefcase',
                'label' => __('الوظيفة'),
                'errors' => ['job_id', 'department_id', 'date_of_hire', 'date_of_fire', 'job_level']
            ],
            'salary' => [
                'icon' => 'fa-money-bill-wave',
                'label' => __('المرتبات'),
                'errors' => ['salary', 'salary_type']
            ],
            'attendance' => [
                'icon' => 'fa-clock',
                'label' => __('الحضور'),
                'errors' => ['finger_print_id', 'finger_print_name', 'shift_id', 'additional_hour_calculation', 'additional_day_calculation', 'late_hour_calculation', 'late_day_calculation']
            ],
            'kpi' => [
                'icon' => 'fa-chart-line',
                'label' => __('معدلات الأداء'),
                'errors' => ['kpi_ids', 'kpi_weights', 'selected_kpi_id']
            ],
            'Accounting' => [
                'icon' => 'fa-chart-line',
                'label' => __('الحسابات'),
                'errors' => ['salary_basic_account_id', 'opening_balance']
            ],
            'leaveBalances' => [
                'icon' => 'fa-calendar-check',
                'label' => __('رصيد الإجازات'),
                'errors' => ['leave_balances', 'selected_leave_type_id']
            ],
        ];
    @endphp

<div x-data="{
    activeTab: 'personal',
    isEdit: @js($isEdit ?? false),
    isRedirecting: false,
    init() {
        // Listen for employee saved event to disable all buttons and dispatch browser event
        if (window.Livewire) {
            this.$wire.on('employee-saved', () => {
                this.isRedirecting = true;
                
                // Dispatch custom browser event for other Alpine components
                window.dispatchEvent(new CustomEvent('employee-redirect-started'));
                
                // Disable all buttons immediately
                this.$nextTick(() => {
                    document.querySelectorAll('button, a.btn').forEach(btn => {
                        if (!btn.hasAttribute('data-keep-enabled')) {
                            btn.disabled = true;
                            btn.classList.add('opacity-50', 'cursor-not-allowed', 'pointer-events-none');
                        }
                    });
                });
            });
        }
        
        // For new employee (create mode), always start with first tab
        // For edit mode, restore active tab from localStorage
        if (!this.isEdit) {
            // Clear saved tab for new employee
            localStorage.removeItem('employeeFormActiveTab');
            this.activeTab = 'personal';
            this.$nextTick(() => {
                this.switchTab('personal', false);
            });
        } else {
            // Restore active tab from localStorage for edit mode
            const savedTab = localStorage.getItem('employeeFormActiveTab');
            if (savedTab) {
                this.activeTab = savedTab;
                this.$nextTick(() => {
                    this.switchTab(savedTab, false);
                });
            } else {
                this.$nextTick(() => {
                    this.switchTab('personal', false);
                });
            }
        }
        
        // Watch for tab changes and save to localStorage
        this.$watch('activeTab', (value) => {
            if (value && !this.isRedirecting) {
                localStorage.setItem('employeeFormActiveTab', value);
            }
        });
    }
        
        // Listen for Livewire updates to preserve active tab
        if (window.Livewire) {
            // Preserve tab after DOM updates
            Livewire.hook('morph.updated', ({ el, component }) => {
                const currentTab = this.activeTab || localStorage.getItem('employeeFormActiveTab') || 'personal';
                // Only restore if we're not already on the correct tab
                if (this.activeTab !== currentTab) {
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.switchTab(currentTab, false);
                        }, 100);
                    });
                } else {
                    // Just ensure the tab is properly displayed
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.switchTab(this.activeTab, false);
                        }, 50);
                    });
                }
            });
            
            // Also listen for component updates
            Livewire.hook('message.processed', (message, component) => {
                const currentTab = this.activeTab || localStorage.getItem('employeeFormActiveTab') || 'personal';
                if (this.activeTab !== currentTab) {
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.switchTab(currentTab, false);
                        }, 100);
                    });
                }
            });
        }
    },
    switchTab(tabName, saveToStorage = true) {
        if (!tabName || this.isRedirecting) return;
        
        this.activeTab = tabName;
        if (saveToStorage && !this.isRedirecting) {
            localStorage.setItem('employeeFormActiveTab', tabName);
        }
        
        // Update Bootstrap tabs
        this.$nextTick(() => {
            // Remove active class from all tabs
            const allLinks = document.querySelectorAll('#employeeFormTabs .nav-link');
            const allPanes = document.querySelectorAll('#employeeFormTabsContent .tab-pane');
            
            allLinks.forEach(link => {
                link.classList.remove('active');
                link.setAttribute('aria-selected', 'false');
            });
            
            allPanes.forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Add active class to selected tab
            const tabButton = document.querySelector(`#${tabName}-tab`);
            const tabPane = document.querySelector(`#${tabName}-content`);
            
            if (tabButton) {
                tabButton.classList.add('active');
                tabButton.setAttribute('aria-selected', 'true');
            }
            if (tabPane) {
                tabPane.classList.add('show', 'active');
            }
        });
    },
    isActiveTab(tabName) {
        return this.activeTab === tabName;
    }
}" x-init="init()">
    <!-- Navigation Tabs - Bootstrap -->
    <ul class="nav nav-tabs mb-3" role="tablist" id="employeeFormTabs">
        @foreach($tabs as $tabKey => $tab)
            @php
                // Calculate error count for this tab using Livewire error bag
                $tabErrorCount = 0;
                $processedFields = [];
                
                // Get Livewire errors only
                $livewireErrors = $this->getErrorBag();
                
                if ($livewireErrors->any()) {
                    foreach ($tab['errors'] as $errorKey) {
                        // Check for direct errors
                        if ($livewireErrors->has($errorKey) && !in_array($errorKey, $processedFields)) {
                            $errorMessages = $livewireErrors->get($errorKey);
                            $tabErrorCount += is_array($errorMessages) ? count($errorMessages) : 1;
                            $processedFields[] = $errorKey;
                        }
                        
                        // Check for nested errors (e.g., leave_balances.0.max_monthly_days)
                        foreach ($livewireErrors->keys() as $errorField) {
                            if (str_starts_with($errorField, $errorKey . '.') && !in_array($errorField, $processedFields)) {
                                $errorMessages = $livewireErrors->get($errorField);
                                $tabErrorCount += is_array($errorMessages) ? count($errorMessages) : 1;
                                $processedFields[] = $errorField;
                            }
                        }
                    }
                }
            @endphp
            <li class="nav-item" role="presentation">
                <button class="nav-link font-hold fw-bold @if($tabErrorCount > 0) text-danger @endif"
                        id="{{ $tabKey }}-tab"
                        type="button"
                        role="tab"
                        aria-controls="{{ $tabKey }}-content"
                        :aria-selected="isActiveTab('{{ $tabKey }}')"
                        @click.prevent="switchTab('{{ $tabKey }}')"
                        :class="{ 'active': isActiveTab('{{ $tabKey }}') }"
                        style="@if($tabErrorCount > 0) border-bottom: 2px solid #dc3545 !important; @endif">
                    <i class="fas {{ $tab['icon'] }} me-2"></i>
                    <span>{{ $tab['label'] }}</span>
                    @if($tabErrorCount > 0)
                        <span class="badge bg-danger ms-2 rounded-pill" 
                              wire:key="error-count-{{ $tabKey }}-{{ $tabErrorCount }}"
                              style="font-size: 0.7rem; font-weight: bold; padding: 0.2em 0.6em; min-width: 1.5em; display: inline-flex; align-items: center; justify-content: center;"
                              title="{{ __('عدد الأخطاء في هذا التاب: ') . $tabErrorCount }}">
                            {{ $tabErrorCount }}
                        </span>
                    @endif
                </button>
            </li>
        @endforeach
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="employeeFormTabsContent">
        @foreach($tabs as $tabKey => $tab)
            @php
                // Convert tab key to file name
                $tabFileName = match($tabKey) {
                    'Accounting' => 'accounting',
                    'leaveBalances' => 'leave-balances',
                    default => strtolower($tabKey)
                };
            @endphp
            <div class="tab-pane fade"
                 :class="{ 'show active': isActiveTab('{{ $tabKey }}') }"
                 id="{{ $tabKey }}-content"
                 role="tabpanel"
                 aria-labelledby="{{ $tabKey }}-tab"
                 tabindex="0"
                 x-show="isActiveTab('{{ $tabKey }}')"
                 x-transition>
                @include("livewire.hr-management.employees.partials.form.tabs.{$tabFileName}-tab")
            </div>
        @endforeach
    </div>
</div>
