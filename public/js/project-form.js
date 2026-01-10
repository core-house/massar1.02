document.addEventListener('alpine:init', () => {
    Alpine.data('projectForm', () => ({
        form: {
            name: '',
            client_id: '',
            project_type_id: '',
            start_date: new Date().toISOString().split('T')[0],
            description: '',
            daily_work_hours: 8,
            working_days: 5,
            holidays: ['5', '6'],
            working_zone: '',
            working_zone: '',
            status: 'pending',
            save_as_template: false,
            template_name: ''
        },
        showSummaryModal: false,
        showMoveModal: false,
        targetSubproject: '',
        bulkAction: '',

        get completionPercentage() {
            let fields = [
                this.form.name,
                this.form.client_id,
                this.form.project_type_id,
                this.form.start_date,
                this.form.working_zone,
                this.items.length > 0 ? 'items' : ''
            ];
            
            let filled = fields.filter(f => f && f !== '').length;
            return Math.round((filled / fields.length) * 100);
        },

        get isComplete() {
            return this.completionPercentage === 100;
        },

        get subprojectSummary() {
            const groups = {};
            
            this.items.forEach(item => {
                const sub = item.subproject_name ? item.subproject_name.trim() : 'بدون مشروع فرعي';
                if (!groups[sub]) {
                    groups[sub] = {
                        name: sub,
                        count: 0,
                        total_quantity: 0,
                        total_estimated_daily_qty: 0,
                        avg_duration: 0,
                        items: []
                    };
                }
                groups[sub].count++;
                groups[sub].total_quantity += parseFloat(item.total_quantity) || 0;
                groups[sub].total_estimated_daily_qty += parseFloat(item.estimated_daily_qty) || 0;
                groups[sub].items.push(item);
            });

            return Object.values(groups);
        },
        currentStep: 1,
        steps: [
            { id: 1, title: 'البيانات الأساسية', icon: 'las la-info-circle' },
            { id: 2, title: 'الجدول الزمني', icon: 'las la-calendar-alt' },
            { id: 3, title: 'بنود المشروع', icon: 'las la-tasks' },
            { id: 4, title: 'فريق العمل', icon: 'las la-users' }
        ],
        searchQuery: '',
        searchResults: [],
        items: [],
        selectedItems: [],
        allTemplates: [], 
        allWorkItems: [],
        daysOfWeek: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        calculatedEndDate: '',
        
        // Counter for unique IDs
        itemCounter: 0,

        init() {
            this.updateWorkingDays();
            
            this.$nextTick(() => {
                if(this.$refs.sortableList) {
                    new Sortable(this.$refs.sortableList, {
                        animation: 150,
                        handle: '.drag-handle',
                        onEnd: (evt) => {
                            // Reorder items array based on DOM change
                            const itemEl = this.items[evt.oldIndex];
                            this.items.splice(evt.oldIndex, 1);
                            this.items.splice(evt.newIndex, 0, itemEl);
                            
                            // 1. Update Orders
                            this.updateItemOrders();
                            
                            // 2. Update Predecessors logic (IDs don't change, but list order does)
                            // Actually, visual order matters for user understanding options
                            this.updatePredecessors(); 

                            // 3. Recalculate Dates
                            this.calculateAllDates();
                        }
                    });
                }
            });
        },

        // Helper to generate unique row ID
        generateRowId() {
            this.itemCounter++;
            return `item_${this.itemCounter}_${Date.now()}`;
        },

        updateItemOrders() {
            this.items.forEach((item, index) => {
                item.item_order = index; // 0-indexed order
            });
        },

        updatePredecessors() {
            // Function to get available predecessors for a specific row
            // We expose this helper or just let the view Loop iterate over `items`
            // But to avoid circular reference in dropdown (an item depends on itself), 
            // we handle that in the view: <option x-show="p.id !== row.id">
            
            // This function is mainly a hook if we need to rebuild explicit lists, 
            // but in Alpine/Vue we compute it in the view or getter.
            // However, we trigger a refresh of UI if needed.
        },

        detectCircularDependency(targetItemIndex, predecessorIndex) {
            if (predecessorIndex === '' || predecessorIndex === null) return false;

            // Build adjacency list using INDICES
            // Array index -> Predecessor index
            const adj = {};
            this.items.forEach((item, idx) => {
                if (item.predecessor !== '' && item.predecessor !== null) {
                    adj[idx] = item.predecessor;
                }
            });

            // Check if adding target -> pred creates a cycle
            let current = predecessorIndex;
            const visited = new Set();
            
            while (current !== undefined && current !== null && current !== '') {
                if (current == targetItemIndex) return true; // Cycle found
                if (visited.has(current)) break; 
                visited.add(current);
                current = adj[current];
            }
            return false;
        },

        duplicateItem(item) {
            const newItem = JSON.parse(JSON.stringify(item));
            newItem.id = this.generateRowId();
            newItem.predecessor = ''; // Reset predecessor to avoid immediate logic issues
            
            this.items.push(newItem);
            this.updateItemOrders();
            this.updatePredecessors();
            this.calculateAllDates();
        },

        setData(templates, workItems) {
            this.allTemplates = templates;
            this.allWorkItems = workItems;
        },

        loadProjectData(project, projectItems) {
            // Basic Info
            this.form.name = project.name || '';
            this.form.client_id = project.client_id || '';
            this.form.project_type_id = project.project_type_id || '';
            this.form.start_date = project.start_date ? new Date(project.start_date).toISOString().split('T')[0] : '';
            this.form.description = project.description || '';
            this.form.daily_work_hours = project.daily_work_hours || 8;
            this.form.working_days = project.working_days || 5;
            this.form.holidays = project.holidays ? String(project.holidays).split(',') : ['5', '6'];
            this.form.working_zone = project.working_zone || '';
            this.form.status = project.status || 'pending';

            // Items Mapping
            if (Array.isArray(projectItems)) {
                this.items = projectItems.map(item => ({
                     id: String(item.id || 'item_' + this.itemCounter++ + '_' + Date.now()),
                     work_item_id: item.work_item_id,
                     name: item.name || '',
                     unit: item.unit || '',
                     total_quantity: parseFloat(item.total_quantity || 0),
                     estimated_daily_qty: parseFloat(item.estimated_daily_qty || 0),
                     duration: parseInt(item.duration || 0),
                     start_date: item.start_date || '',
                     end_date: item.end_date || '',
                     // Normalize predecessor to string if it exists, otherwise empty string
                     predecessor: (item.predecessor !== null && item.predecessor !== undefined && item.predecessor !== '') ? String(item.predecessor) : '', 
                     dependency_type: item.dependency_type || 'end_to_start',
                     lag: parseInt(item.lag || 0),
                     is_measurable: item.is_measurable == 1 || item.is_measurable === true,
                     subproject_name: item.subproject_name || '',
                     notes: item.notes || '',
                     item_order: item.item_order || 0,
                     _calculated: false
                }));
            }

            this.updatePredecessors();
            // Recalculate dates
            this.$nextTick(() => {
                this.calculateAllDates();
            });
        },

        searchItems() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }
            const query = this.searchQuery.toLowerCase();
            this.searchResults = this.allWorkItems.filter(item => 
                item.name.toLowerCase().includes(query)
            ).slice(0, 50);
        },

        addItem(item) {
            const newItem = {
                id: this.generateRowId(),
                work_item_id: item.id,
                name: item.name,
                unit: item.unit,
                subproject_name: '',
                total_quantity: 1,
                estimated_daily_qty: 1,
                duration: 1,
                predecessor: '', // Stores ID of predecessor row
                dependency_type: 'end_to_start',
                lag: 0,
                start_date: this.form.start_date,
                end_date: this.form.start_date,
                is_measurable: true,
                notes: '',
                item_order: this.items.length
            };
            this.items.push(newItem);
            this.searchResults = [];
            this.searchQuery = '';
            
            this.calculateDuration(newItem); // Initial duration calc
            this.updateItemOrders();
            this.updatePredecessors(); 
            this.calculateAllDates();
        },

        removeItem(index) {
            // Remove item
            const removedId = this.items[index].id;
            this.items.splice(index, 1);
            
            // Clear any relationships pointing to this item
            this.items.forEach(item => {
                if (item.predecessor === removedId) {
                    item.predecessor = ''; // or handle gracefully
                }
            });

            this.updateItemOrders();
            this.updatePredecessors();
            this.calculateAllDates();
        },

        removeSelected() {
            const selectedIds = this.selectedItems.map(idx => this.items[idx].id);
            this.items = this.items.filter((item, index) => !this.selectedItems.map(Number).includes(index));
            
            // Clean up dependencies
            this.items.forEach(item => {
                if (selectedIds.includes(item.predecessor)) {
                    item.predecessor = '';
                }
            });

            this.selectedItems = [];
            this.updateItemOrders();
            this.updatePredecessors();
            this.calculateAllDates();
        },

        duplicateSelected() {
            if (this.selectedItems.length === 0) return;
            
            const newItems = this.selectedItems.map(idx => {
                const item = this.items[idx];
                const newItem = JSON.parse(JSON.stringify(item));
                newItem.id = this.generateRowId();
                newItem.name = newItem.name + ' (نسخة)';
                newItem.predecessor = ''; // Reset predecessor
                newItem.item_order = this.items.length; // Will be updated
                return newItem;
            });

            this.items.push(...newItems);
            
            this.updateItemOrders();
            this.updatePredecessors(); 
            this.calculateAllDates();
            
            // Optional: select the new items?
            this.selectedItems = [];
        },

        prepareMoveToSubproject() {
            if (this.selectedItems.length === 0) return;
            this.targetSubproject = '';
            this.showMoveModal = true;
        },

        applyMoveToSubproject() {
            if (!this.targetSubproject.trim()) {
                alert('يرجى إدخال اسم المشروع الفرعي');
                return;
            }
            
            this.selectedItems.forEach(idx => {
                if (this.items[idx]) {
                    this.items[idx].subproject_name = this.targetSubproject;
                }
            });
            
            this.showMoveModal = false;
            this.targetSubproject = '';
            // If in grouped mode, this wil trigger reactivity updates
        },

        exportSelectedToCSV() {
            if (this.selectedItems.length === 0) {
                alert('يرجى تحديد بنود للتصدير');
                return;
            }

            const headers = ['المسلسل', 'البند', 'الوحدة', 'المشروع الفرعي', 'الكمية', 'اليومية', 'المدة', 'ت. البداية', 'ت. النهاية', 'ملاحظات'];
            
            const rows = this.selectedItems.map(idx => {
                const item = this.items[idx];
                return [
                    idx + 1,
                    `"${(item.name || '').replace(/"/g, '""')}"`,
                    item.unit,
                    `"${(item.subproject_name || '').replace(/"/g, '""')}"`,
                    item.total_quantity,
                    item.estimated_daily_qty,
                    item.duration,
                    item.start_date,
                    item.end_date,
                    `"${(item.notes || '').replace(/"/g, '""')}"`
                ];
            });

            const csvContent = "\uFEFF" + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "project_items_export.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        executeBulkAction() {
            if (this.selectedItems.length === 0) {
                alert('يرجى تحديد بنود أولاً');
                return;
            }
            
            switch(this.bulkAction) {
                case 'delete':
                    if(confirm('هل أنت متأكد من حذف العناصر المحددة؟')) {
                        this.removeSelected();
                    }
                    break;
                case 'duplicate':
                    this.duplicateSelected();
                    break;
                case 'move':
                    this.prepareMoveToSubproject();
                    break;
                case 'export_csv':
                    this.exportSelectedToCSV();
                    break;
                default:
                    alert('يرجى اختيار إجراء');
            }
            // Reset action? Maybe not, keep it selected.
        },
        
        // Helper to find index for backend submission
        getPredecessorIndex(predId) {
            if (!predId) return '';
            return this.items.findIndex(item => item.id == predId);
        },

        get subprojectSummary() {
            const groups = {};
            
            this.items.forEach((item, index) => {
                const sub = item.subproject_name ? item.subproject_name.trim() : 'بدون مشروع فرعي';
                if (!groups[sub]) {
                    groups[sub] = {
                        name: sub,
                        count: 0,
                        total_quantity: 0,
                        total_estimated_daily_qty: 0,
                        items: [],
                        start_dates: [],
                        end_dates: []
                    };
                }
                groups[sub].count++;
                groups[sub].total_quantity += parseFloat(item.total_quantity) || 0;
                groups[sub].total_estimated_daily_qty += parseFloat(item.estimated_daily_qty) || 0;
                // We store the item AND its original index so the form inputs work in the grouped view
                groups[sub].items.push({ data: item, originalIndex: index });
                
                if (item.start_date) groups[sub].start_dates.push(new Date(item.start_date));
                if (item.end_date) groups[sub].end_dates.push(new Date(item.end_date));
            });

            return Object.values(groups).map(g => {
                 // Calculate Min Start, Max End
                 let minStart = g.start_dates.length ? new Date(Math.min(...g.start_dates)) : null;
                 let maxEnd = g.end_dates.length ? new Date(Math.max(...g.end_dates)) : null;
                 
                 g.formattedStart = minStart ? minStart.toISOString().split('T')[0] : '-';
                 g.formattedEnd = maxEnd ? maxEnd.toISOString().split('T')[0] : '-';
                 
                 // Duration in days (inclusive)
                 if (minStart && maxEnd) {
                     const diffTime = Math.abs(maxEnd - minStart);
                     const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; 
                     g.duration = diffDays;
                 } else {
                     g.duration = 0;
                 }
                 
                 return g;
            });
        },

        isGrouped: false,

        toggleGroupedMode(enable) {
            this.isGrouped = enable;
            // logic is handled by subprojectSummary computed property and x-if in view
        },

        toggleAll(checked) {
            this.selectedItems = checked ? this.items.map((_, i) => i) : [];
        },

        toggleHoliday(dayIndex) {
            if (this.form.holidays.includes(dayIndex)) {
                this.form.holidays = this.form.holidays.filter(d => d !== dayIndex);
            } else {
                this.form.holidays.push(dayIndex);
            }
            this.updateWorkingDays();
            this.calculateAllDates();
        },

        updateWorkingDays() {
            this.form.working_days = 7 - this.form.holidays.length;
        },

        toggleTemplate(templateId, checked) {
            if (checked) {
                const template = this.allTemplates.find(t => t.id === templateId);
                if (template && template.items) {
                    const idMap = {}; // Map Old Template Item ID -> New Generated Row ID
                    const newItems = [];

                    // Pass 1: Create Items & Build Map
                    template.items.forEach(tItem => {
                        const newRowId = this.generateRowId();
                        
                        // IMPORTANT: tItem.id is the Template Item ID from DB. 
                        // We map it to the new dynamic UI ID.
                        if(tItem.id) {
                            idMap[tItem.id] = newRowId;
                        }

                        const newItem = {
                            id: newRowId,
                            work_item_id: tItem.work_item_id,
                            name: tItem.name,
                            unit: tItem.unit,
                            subproject_name: tItem.subproject_name || '',
                            total_quantity: parseFloat(tItem.default_quantity) || 1, 
                            estimated_daily_qty: parseFloat(tItem.estimated_daily_qty) || 1,
                            duration: parseInt(tItem.duration) || 1,
                            
                            // Store original predecessor ID temporarily to resolve in Pass 2
                            _original_pred_id: tItem.predecessor, 
                            
                            predecessor: '', // Will be set in Pass 2
                            dependency_type: tItem.dependency_type || 'end_to_start',
                            lag: parseInt(tItem.lag) || 0,
                            start_date: this.form.start_date,
                            end_date: this.form.start_date,
                            is_measurable: (tItem.is_measurable == 1 || tItem.is_measurable === true),
                            notes: tItem.notes || '',
                            item_order: this.items.length + newItems.length // temporary sort order
                        };
                        
                        newItems.push(newItem);
                    });
                    
                    // Pass 2: Resolve Predecessors
                    newItems.forEach(item => {
                        const oldPredId = item._original_pred_id;
                        if (oldPredId && idMap[oldPredId]) {
                            // Link to the NEW ID of the referenced item
                            item.predecessor = idMap[oldPredId];
                            console.log(`[Template] Mapped Item ${item.id}: OldPred ${oldPredId} -> NewPred ${item.predecessor}`);
                        } else {
                            if(oldPredId) console.warn(`[Template] Failed to map OldPred ${oldPredId} for Item ${item.id}. Map keys:`, Object.keys(idMap));
                        }
                        // Cleanup temp prop
                        delete item._original_pred_id;
                    });

                    // Add to main list
                    this.items.push(...newItems);
                    
                    // Post-processing
                    this.updateItemOrders();
                    this.updatePredecessors();
                    this.calculateAllDates();
                }
            }
        },

        calculateDuration(row) {
            if (row.estimated_daily_qty > 0 && row.total_quantity > 0) {
                row.duration = Math.ceil(row.total_quantity / row.estimated_daily_qty);
            } else {
                // If not calculated by quantity (e.g., lump sum or manual), ensure at least 1 day or user input?
                // For now keep as is, but if measurable boolean is off, maybe allow manual duration
                if (row.is_measurable) {
                     row.duration = 1;
                }
                // If not measurable, user edits duration directly? 
                // The input is readonly in blade currently. Assuming duration is always calculated or 1.
            }
            this.calculateAllDates();
        },

        isHoliday(date) {
            const dayOfWeek = date.getDay(); // 0 (Sun) to 6 (Sat)
            return this.form.holidays.includes(dayOfWeek.toString());
        },

        addWorkingDays(startDateStr, days, isDuration = false) {
             let date = new Date(startDateStr);
             // Normalize to noon to avoid DST issues
             date.setHours(12, 0, 0, 0);

             let count = 0;
             // If we are adding duration (calculating end date), we count the start date itself as Day 1
             // UNLESS start date is a holiday? Ideally start date shouldn't be a holiday.
             // But if we are adding Lag, we are shifting.
             
             const target = Math.abs(days);
             const direction = days >= 0 ? 1 : -1;
             
             // Base Case: 0 days
             if (days === 0 && !isDuration) return date.toISOString().split('T')[0];

             // If duration, we want to encompass 'days' working days.
             // Day 1 is already 'date'.
             let daysToProcess = isDuration ? (target - 1) : target;
             
             // Safety brake
             let loops = 0;
             
             // If we are SHIFTING (Lag), we move first, then check.
             // If isDuration, we check current first.
             
             if (!isDuration) {
                 // Lag Logic
                 while (count < target && loops < 1000) {
                     date.setDate(date.getDate() + direction);
                     // If it's a holiday, we don't count it towards the "Working Days Lag"
                     // BUT: Does negative lag (Lead) count working days? Usually yes.
                     if (!this.isHoliday(date)) {
                         count++;
                     }
                     loops++;
                 }
             } else {
                 // Duration Logic (always positive)
                 if (daysToProcess < 0) daysToProcess = 0; // Duration 1 = 0 adds
                 
                 while (count < daysToProcess && loops < 1000) {
                     date.setDate(date.getDate() + 1);
                     if (!this.isHoliday(date)) {
                         count++;
                     }
                      loops++;
                 }
             }
             
             return date.toISOString().split('T')[0];
        },

        calculateWorkingEndDate(startDateStr, duration) {
            return this.addWorkingDays(startDateStr, duration, true);
        },

        calculateAllDates() {
            if (!this.form.start_date) return;
            
            // Reset visited flags for recursion
            this.items.forEach(i => i._calculated = false);
            
            // Calculate Project Start Date
            const projectStart = new Date(this.form.start_date);

            // Iterate all items
            // We can't just loop linearly because dependency might be later in the list (though UI suggests top-down)
            // But strict graph theory says topological sort or recursion with memoization.
            
            this.items.forEach(item => {
                this.calculateItemDates(item, projectStart);
            });
            
            // Update Global Max Date
            let maxDate = projectStart;
            this.items.forEach(item => {
                const end = new Date(item.end_date);
                if (end > maxDate) maxDate = end;
            });
            this.calculatedEndDate = maxDate.toISOString().split('T')[0];
        },

        calculateItemDates(item, projectStart) {
            if (item._calculated) return;
            
            // Checks for circular dependency
            // Use current item's index in the list
            const currentIdx = this.items.indexOf(item);
            // Predecessor is ID now. detection logic loops indices?
            // We should update detectCircularDependency too or just check simple direct recursion here?
            // "detectCircularDependency" was index based. Let's update it separately later, 
            // but for now, let's just do a simple check:
            if (item.predecessor === item.id) {
                 item.predecessor = ''; // Self dependency
            }

            let startBaseDateStr = this.form.start_date;
            
            // 1. Resolve Predecessor
            // Predecessor is now an ID (string/int)
            if (item.predecessor && item.predecessor !== '') {
                // Find predecessor object by ID
                const pred = this.items.find(p => p.id == item.predecessor);
                
                // Safety check: Don't depend on self or invalid item
                if (pred && pred.id !== item.id) {
                    
                    // Recursive call to ensure predecessor is ready
                    if (!pred._calculated) {
                        this.calculateItemDates(pred, projectStart);
                    }
                    
                    // Determine reference date based on type
                    let refDate;
                    if (item.dependency_type === 'start_to_start') {
                        refDate = pred.start_date;
                    } else {
                        // end_to_start (default)
                        refDate = pred.end_date;
                        
                        // We assume strict logic: Start = Finish + 1 working day (handled by lag logic or implicitly?)
                        // Previous logic: refDate = end_date.
                        // Let's stick to adding 1 day to finish date to start next day.
                        const d = new Date(pred.end_date);
                        d.setDate(d.getDate() + 1);
                        refDate = d.toISOString().split('T')[0];
                    }
                    
                    startBaseDateStr = refDate;
                    
                    // Apply Lag (can be negative)
                    item.start_date = this.addWorkingDays(startBaseDateStr, parseInt(item.lag) || 0);

                } else {
                    // Predecessor ID invalid/deleted?
                     item.start_date = startBaseDateStr;
                }
            } else {
                // No predecessor must use project start
                item.start_date = startBaseDateStr;
            }

            // Ensure Start Date is a Working Day (if it landed on holiday due to naive math)
            // The addWorkingDays generally handles shifts, but the Base might be holiday?
            // Let's sanitize start_date
            let sDate = new Date(item.start_date);
            while (this.isHoliday(sDate)) {
                sDate.setDate(sDate.getDate() + 1);
            }
            item.start_date = sDate.toISOString().split('T')[0];

            // 2. Calculate End Date
            item.end_date = this.calculateWorkingEndDate(item.start_date, parseInt(item.duration) || 1);
            
            item._calculated = true;
        },

        formatDate(dateStr) {
            return dateStr || '-';
        },

        submitForm(e) {
            // Check if saving as draft
            const isDraft = e.submitter && e.submitter.name === 'save_action' && e.submitter.value === 'draft';
            
            if (!isDraft && this.items.length === 0) {
                alert('يجب إضافة بند واحد على الأقل للمشروع');
                e.preventDefault(); // Stop submission
                return;
            }
            // If draft, or if items exist, allow submit
            // Alpine x-on:submit handles the submit unless we prevent it? 
            // Wait, the blade has <form x-on:submit.prevent="submitForm"> ? 
            // Let's check how it's called. usually calls submitForm($event). 
            // If we didn't prevent default in blade, we don't need to prevent here for alert.
            // But usually we prevent default to validate, then submit manual.
            
            // Looking at line 719: e.target.submit() suggests we prevented default.
            // So if validation passes, we call submit().
            
            if (isDraft) {
                // Manually append hidden input because submit() bypasses button value
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'save_action';
                hiddenInput.value = 'draft';
                e.target.appendChild(hiddenInput);
            }

            e.target.submit();
        }
    }));
});
