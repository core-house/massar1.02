/**
 * Employee Form Alpine.js Component - Page-based Scripts
 * 
 * Scripts for employee create/edit pages (non-modal)
 */

(function () {
    'use strict';

    /**
     * Constants for Employee Form Component
     */
    const CONSTANTS = {
        MAX_IMAGE_SIZE: 2 * 1024 * 1024, // 2MB in bytes
        VALID_IMAGE_TYPES: ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'],
        DEBOUNCE_DELAY: 300, // milliseconds
        NOTIFICATION_TIMEOUT: 3000 // milliseconds
    };

    /**
     * Employee Form Manager Component
     * @param {Object} config - Component configuration
     * @returns {Object} Alpine component
     */
    function employeeFormManager(config = {}) {
        return {
            // ==========================================
            // State - synced with Livewire
            // ==========================================
            kpiIds: config.kpiIds || [],
            kpiWeights: config.kpiWeights || {},
            selectedKpiId: config.selectedKpiId || '',
            currentImageUrl: config.currentImageUrl || null,
            isEdit: config.isEdit || false,

            // ==========================================
            // Local State
            // ==========================================
            kpis: config.kpis || [],
            leaveTypes: config.leaveTypes || [],
            leaveBalances: config.leaveBalances || {},
            selectedLeaveTypeId: config.selectedLeaveTypeId || '',
            activeTab: 'personal',
            notifications: [],
            imagePreview: null,
            showPassword: false,
            selectedFileName: '',
            selectedFileSize: '',
            isDragging: false,
            imageLoading: false,

            // KPI Search state
            kpiSearch: '',
            kpiSearchOpen: false,
            kpiSearchIndex: -1,

            // Leave Type Search state
            leaveTypeSearch: '',
            leaveTypeSearchOpen: false,
            leaveTypeSearchIndex: -1,

            // ==========================================
            // Computed Properties
            // ==========================================
            get totalKpiWeight() {
                let total = 0;
                this.kpiIds.forEach(kpiId => {
                    total += parseInt(this.kpiWeights[kpiId]) || 0;
                });
                return total;
            },

            get weightStatus() {
                if (this.totalKpiWeight === 100) return 'success';
                if (this.totalKpiWeight > 100) return 'danger';
                return 'warning';
            },

            get weightMessage() {
                if (this.totalKpiWeight === 100) {
                    return 'ممتاز! تم اكتمال النسبة بنجاح. يمكنك الآن حفظ البيانات.';
                } else if (this.totalKpiWeight > 100) {
                    return `المجموع الحالي ${this.totalKpiWeight}% أكبر من 100%. يرجى تقليل الأوزان.`;
                } else {
                    return `المجموع الحالي ${this.totalKpiWeight}% أقل من 100%. يرجى إكمال الأوزان.`;
                }
            },

            get availableKpis() {
                return this.kpis.filter(kpi => !this.kpiIds.includes(kpi.id));
            },

            get filteredKpis() {
                if (!this.kpiSearch) return this.availableKpis;
                const search = this.kpiSearch.toLowerCase();
                return this.availableKpis.filter(kpi =>
                    kpi.name.toLowerCase().includes(search) ||
                    (kpi.description && kpi.description.toLowerCase().includes(search))
                );
            },

            get leaveBalanceIds() {
                return Object.keys(this.leaveBalances || {});
            },

            get availableLeaveTypes() {
                const addedLeaveTypeIds = Object.values(this.leaveBalances || {})
                    .map(b => b.leave_type_id);
                return this.leaveTypes.filter(lt => !addedLeaveTypeIds.includes(lt.id));
            },

            get filteredLeaveTypes() {
                if (!this.leaveTypeSearch) return this.availableLeaveTypes;
                const search = this.leaveTypeSearch.toLowerCase();
                return this.availableLeaveTypes.filter(lt =>
                    lt.name.toLowerCase().includes(search) ||
                    (lt.code && lt.code.toLowerCase().includes(search))
                );
            },


            // ==========================================
            // Methods
            // ==========================================

            /**
             * Safely execute a function with error handling
             */
            safeExecute(fn, errorMessage = 'حدث خطأ غير متوقع', defaultValue = null) {
                try {
                    return fn();
                } catch (error) {
                    console.error('Error in employeeFormManager:', error);
                    
                    let message = errorMessage;
                    if (error instanceof Error) {
                        if (error.message.includes('rate_limit') || error.message.includes('Rate limit')) {
                            message = 'تم تجاوز الحد المسموح. يرجى المحاولة مرة أخرى بعد قليل.';
                        } else if (error.message.includes('unauthorized') || error.message.includes('403')) {
                            message = 'ليس لديك صلاحية لتنفيذ هذا الإجراء.';
                        } else if (error.message.includes('network') || error.message.includes('fetch')) {
                            message = 'فشل الاتصال بالخادم. يرجى التحقق من الاتصال بالإنترنت.';
                        } else if (error.message) {
                            message = error.message;
                        }
                    }
                    
                    this.addNotification('error', message);
                    return defaultValue;
                }
            },

            init() {
                // Restore active tab from localStorage if available
                const savedTab = localStorage.getItem('employeeFormActiveTab');
                if (savedTab) {
                    this.activeTab = savedTab;
                }

                // Listen for Livewire notifications
                this.$wire.on('notify', (data) => {
                    if (data && data.type && data.message) {
                        this.addNotification(data.type, data.message);
                    }
                });

                // Listen for Livewire errors
                this.$wire.on('$error', (errors) => {
                    if (errors && Object.keys(errors).length > 0) {
                        const firstError = Object.values(errors)[0];
                        if (Array.isArray(firstError)) {
                            this.addNotification('error', firstError[0]);
                        } else {
                            this.addNotification('error', firstError);
                        }
                    }
                });

                // Listen for Livewire validation errors
                this.$wire.on('$validation', (errors) => {
                    if (errors && Object.keys(errors).length > 0) {
                        const errorMessages = Object.values(errors).flat();
                        errorMessages.forEach(message => {
                            this.addNotification('error', message);
                        });
                    }
                });

                // Preserve active tab across Livewire updates using Alpine's $watch
                this.$watch('activeTab', (value) => {
                    if (value) {
                        localStorage.setItem('employeeFormActiveTab', value);
                    }
                });

                // Listen for Livewire updates to preserve active tab
                // Use Livewire's hook system for Livewire 3
                if (window.Livewire) {
                    Livewire.hook('morph.updated', ({ el, component }) => {
                        // Preserve active tab after Livewire DOM updates
                        const savedTab = localStorage.getItem('employeeFormActiveTab');
                        if (savedTab && this.activeTab !== savedTab) {
                            this.$nextTick(() => {
                                this.activeTab = savedTab;
                            });
                        }
                    });

                    // Listen for validation errors after commit
                    Livewire.hook('message.processed', (message, component) => {
                        // After Livewire processes a message, check for validation errors
                        this.$nextTick(() => {
                            this.handleValidationErrors();
                        });
                    });
                }

                // Listen for employee saved event
                this.$wire.on('employee-saved', () => {
                    this.resetImagePreview();
                    // Clear saved tab on successful save
                    localStorage.removeItem('employeeFormActiveTab');
                });

                // Listen for KPI added event
                this.$wire.on('kpiAdded', () => {
                    this.clearKpiSelection();
                });

                // Listen for leave balance added event
                this.$wire.on('leaveBalanceAdded', () => {
                    this.clearLeaveTypeSelection();
                });

                // Setup keyboard shortcuts
                this.setupKeyboardShortcuts();
            },

            /**
             * Handle validation errors by switching to the first tab with errors
             */
            handleValidationErrors() {
                try {
                    // Tab error mapping
                    const tabErrors = {
                        'personal': ['name', 'email', 'phone', 'status', 'image', 'gender', 'date_of_birth', 'nationalId', 'marital_status', 'education', 'information'],
                        'location': ['country_id', 'city_id', 'state_id', 'town_id'],
                        'job': ['job_id', 'department_id', 'date_of_hire', 'date_of_fire', 'job_level'],
                        'salary': ['salary', 'salary_type'],
                        'attendance': ['finger_print_id', 'finger_print_name', 'shift_id', 'additional_hour_calculation', 'additional_day_calculation', 'late_hour_calculation', 'late_day_calculation'],
                        'kpi': ['kpi_ids', 'kpi_weights', 'selected_kpi_id'],
                        'Accounting': ['salary_basic_account_id', 'opening_balance'],
                        'leaveBalances': ['leave_balances', 'selected_leave_type_id']
                    };

                    // Get errors from Livewire
                    let livewireErrors = null;
                    try {
                        livewireErrors = this.$wire.get('errors');
                    } catch (e) {
                        // If errors are not available, try alternative method
                        livewireErrors = this.$wire.__instance?.serverMemo?.errors || {};
                    }

                    if (!livewireErrors || Object.keys(livewireErrors).length === 0) {
                        return;
                    }

                    // Helper function to check if error exists
                    const hasError = (errorKey) => {
                        if (!livewireErrors) return false;
                        // Check direct error
                        if (livewireErrors.has && typeof livewireErrors.has === 'function') {
                            return livewireErrors.has(errorKey);
                        }
                        // Check if error exists in object
                        if (livewireErrors[errorKey]) {
                            return true;
                        }
                        // Check nested errors (e.g., leave_balances.0.max_monthly_days)
                        for (const key in livewireErrors) {
                            if (key.startsWith(errorKey + '.') || key.includes(errorKey)) {
                                return true;
                            }
                        }
                        return false;
                    };

                    // Check if current tab has errors, if not, switch to first tab with errors
                    const currentTabErrors = tabErrors[this.activeTab] || [];
                    const currentTabHasErrors = currentTabErrors.some(error => hasError(error));

                    if (!currentTabHasErrors) {
                        // Find first tab with errors
                        for (const [tabKey, errors] of Object.entries(tabErrors)) {
                            const hasErrors = errors.some(error => hasError(error));
                            if (hasErrors) {
                                this.switchTab(tabKey);
                                // Scroll to top of form to show errors
                                setTimeout(() => {
                                    const formElement = document.querySelector('.card-body');
                                    if (formElement) {
                                        formElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                    }
                                }, 100);
                                break;
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error handling validation errors:', error);
                }
            },


            /**
             * Add a notification
             */
            addNotification(type, message) {
                const id = Date.now();
                this.notifications.push({
                    id,
                    type,
                    message
                });
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, CONSTANTS.NOTIFICATION_TIMEOUT);
            },

            /**
             * Switch to a different tab
             */
            switchTab(tab) {
                this.activeTab = tab;
                // Save active tab to localStorage to preserve it across Livewire updates
                localStorage.setItem('employeeFormActiveTab', tab);
            },

            // Image handling
            /**
             * Handle image file change event
             */
            handleImageChange(event) {
                this.safeExecute(() => {
                    const file = event.target.files[0];
                    if (file) {
                        // Validate file type
                        if (!CONSTANTS.VALID_IMAGE_TYPES.includes(file.type)) {
                            this.addNotification('error', 'يرجى اختيار صورة صالحة');
                            event.target.value = '';
                            return;
                        }

                        // Validate file size
                        if (file.size > CONSTANTS.MAX_IMAGE_SIZE) {
                            this.addNotification('error', 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت');
                            event.target.value = '';
                            return;
                        }

                        // Set file info
                        this.selectedFileName = file.name;
                        this.selectedFileSize = this.formatFileSize(file.size);

                        // Create preview
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.imagePreview = e.target.result;
                        };
                        reader.onerror = () => {
                            this.addNotification('error', 'فشل في قراءة الصورة');
                        };
                        reader.readAsDataURL(file);
                    }
                }, 'حدث خطأ أثناء معالجة الصورة');
            },

            /**
             * Handle image drop event
             */
            handleImageDrop(event) {
                this.safeExecute(() => {
                    const file = event.dataTransfer.files[0];
                    if (file) {
                        // Validate file type
                        if (!CONSTANTS.VALID_IMAGE_TYPES.includes(file.type)) {
                            this.addNotification('error', 'يرجى اختيار صورة صالحة');
                            return;
                        }

                        // Validate file size
                        if (file.size > CONSTANTS.MAX_IMAGE_SIZE) {
                            this.addNotification('error', 'حجم الصورة يجب أن يكون أقل من 2 ميجابايت');
                            return;
                        }

                        // Set file to input and trigger Livewire upload
                        const input = document.getElementById('employee-image-input');
                        if (input) {
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(file);
                            input.files = dataTransfer.files;

                            // Trigger change event for Livewire
                            input.dispatchEvent(new Event('change', {
                                bubbles: true
                            }));

                            // Set file info
                            this.selectedFileName = file.name;
                            this.selectedFileSize = this.formatFileSize(file.size);

                            // Create preview
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.imagePreview = e.target.result;
                            };
                            reader.onerror = () => {
                                this.addNotification('error', 'فشل في قراءة الصورة');
                            };
                            reader.readAsDataURL(file);

                            this.addNotification('success', 'تم اختيار الصورة بنجاح');
                        }
                    }
                }, 'حدث خطأ أثناء معالجة الصورة');
            },

            /**
             * Remove the selected image
             */
            removeImage() {
                this.imagePreview = null;
                this.selectedFileName = '';
                this.selectedFileSize = '';
                this.currentImageUrl = null;

                const input = document.getElementById('employee-image-input');
                if (input) {
                    input.value = '';
                }

                this.$wire.set('image', null);
                this.addNotification('info', 'تم إزالة الصورة');
            },

            /**
             * Format file size
             */
            formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
            },

            /**
             * Reset image preview
             */
            resetImagePreview() {
                this.imagePreview = null;
                this.selectedFileName = '';
                this.selectedFileSize = '';
                this.isDragging = false;
                this.imageLoading = false;
            },

            /**
             * Toggle password visibility
             */
            togglePassword() {
                this.showPassword = !this.showPassword;
            },

            // KPI Management
            /**
             * Get KPI name by ID
             */
            getKpiName(kpiId) {
                const kpi = this.kpis.find(k => k.id == kpiId);
                return kpi ? kpi.name : '';
            },

            /**
             * Get KPI description by ID
             */
            getKpiDescription(kpiId) {
                const kpi = this.kpis.find(k => k.id == kpiId);
                return kpi && kpi.description ? kpi.description.substring(0, 50) + '...' : '';
            },

            /**
             * Select a KPI
             */
            selectKpi(kpi) {
                this.selectedKpiId = kpi.id;
                this.kpiSearchOpen = false;
                this.kpiSearch = '';
                this.kpiSearchIndex = -1;
            },

            /**
             * Clear KPI selection
             */
            clearKpiSelection() {
                this.selectedKpiId = '';
                this.kpiSearch = '';
            },

            /**
             * Navigate down in KPI dropdown
             */
            navigateKpiDown() {
                if (this.kpiSearchIndex < this.filteredKpis.length - 1) {
                    this.kpiSearchIndex++;
                }
            },

            /**
             * Navigate up in KPI dropdown
             */
            navigateKpiUp() {
                if (this.kpiSearchIndex > 0) {
                    this.kpiSearchIndex--;
                }
            },

            /**
             * Select the currently highlighted KPI
             */
            selectCurrentKpi() {
                if (this.kpiSearchIndex >= 0 && this.kpiSearchIndex < this.filteredKpis.length) {
                    this.selectKpi(this.filteredKpis[this.kpiSearchIndex]);
                }
            },

            // Leave Type Management
            /**
             * Get Leave Type name by ID
             */
            getLeaveTypeName(leaveTypeId) {
                const leaveType = this.leaveTypes.find(lt => lt.id == leaveTypeId);
                return leaveType ? leaveType.name : '';
            },

            /**
             * Select a Leave Type
             */
            selectLeaveType(leaveType) {
                this.selectedLeaveTypeId = leaveType.id;
                this.leaveTypeSearchOpen = false;
                this.leaveTypeSearch = '';
                this.leaveTypeSearchIndex = -1;
            },

            /**
             * Clear Leave Type selection
             */
            clearLeaveTypeSelection() {
                this.selectedLeaveTypeId = '';
                this.leaveTypeSearch = '';
            },

            /**
             * Navigate down in Leave Type dropdown
             */
            navigateLeaveTypeDown() {
                if (this.leaveTypeSearchIndex < this.filteredLeaveTypes.length - 1) {
                    this.leaveTypeSearchIndex++;
                }
            },

            /**
             * Navigate up in Leave Type dropdown
             */
            navigateLeaveTypeUp() {
                if (this.leaveTypeSearchIndex > 0) {
                    this.leaveTypeSearchIndex--;
                }
            },

            /**
             * Select the currently highlighted Leave Type
             */
            selectCurrentLeaveType() {
                if (this.leaveTypeSearchIndex >= 0 && this.leaveTypeSearchIndex < this.filteredLeaveTypes.length) {
                    this.selectLeaveType(this.filteredLeaveTypes[this.leaveTypeSearchIndex]);
                }
            },

            /**
             * Calculate remaining days for a leave balance
             */
            calculateRemainingDays(balance) {
                const opening = parseFloat(balance.opening_balance_days) || 0;
                const used = parseFloat(balance.used_days) || 0;
                const pending = parseFloat(balance.pending_days) || 0;
                const remaining = opening - used - pending;
                return remaining.toFixed(1);
            },

            /**
             * Check if max monthly days exceeds opening balance
             */
            exceedsMonthlyLimit(balance) {
                if (!balance) {
                    return false;
                }
                
                // Get raw values
                const maxMonthlyRaw = balance.max_monthly_days;
                const openingRaw = balance.opening_balance_days;
                
                // Convert to numbers, handling null, undefined, empty strings
                let maxMonthly = 0;
                let opening = 0;
                
                if (maxMonthlyRaw !== null && maxMonthlyRaw !== undefined && maxMonthlyRaw !== '') {
                    maxMonthly = parseFloat(maxMonthlyRaw);
                    if (isNaN(maxMonthly)) {
                        maxMonthly = 0;
                    }
                }
                
                if (openingRaw !== null && openingRaw !== undefined && openingRaw !== '') {
                    opening = parseFloat(openingRaw);
                    if (isNaN(opening)) {
                        opening = 0;
                    }
                }
                
                // Only show error if:
                // 1. opening balance is greater than 0 (has a valid limit)
                // 2. max monthly is greater than opening balance
                // 3. Both values are valid numbers
                if (opening > 0 && maxMonthly > opening) {
                    return true;
                }
                
                return false;
            },

            /**
             * Setup keyboard shortcuts
             */
            setupKeyboardShortcuts() {
                this._keyboardHandler = (event) => {
                    // Don't trigger shortcuts when typing
                    if (event.target.tagName === 'INPUT' ||
                        event.target.tagName === 'TEXTAREA' ||
                        event.target.tagName === 'SELECT') {
                        return;
                    }

                    const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
                    const ctrlKey = isMac ? event.metaKey : event.ctrlKey;

                    // Esc: Close dropdowns
                    if (event.key === 'Escape') {
                        this.kpiSearchOpen = false;
                        this.leaveTypeSearchOpen = false;
                    }

                    // Ctrl+1-8: Navigate between tabs
                    if (ctrlKey && event.key >= '1' && event.key <= '8') {
                        event.preventDefault();
                        const tabIndex = parseInt(event.key) - 1;
                        const tabs = ['personal', 'location', 'job', 'salary', 'attendance', 'kpi', 'Accounting', 'leaveBalances'];
                        if (tabs[tabIndex]) {
                            this.switchTab(tabs[tabIndex]);
                        }
                    }
                };

                document.addEventListener('keydown', this._keyboardHandler);
            },

            /**
             * Cleanup
             */
            destroy() {
                if (this._keyboardHandler) {
                    document.removeEventListener('keydown', this._keyboardHandler);
                    this._keyboardHandler = null;
                }
            }
        };
    }

    // Expose function to window
    window.employeeFormManager = employeeFormManager;
})();

