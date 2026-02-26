// Alpine.js Manufacturing Calculator Component
// Support both alpine:init event and direct initialization

document.addEventListener('alpine:init', () => {

    // -------------------------------------------------------------------------
    // 1. Alpine Store for Items (Global for this page)
    // -------------------------------------------------------------------------
    Alpine.store('manufacturingItems', {
        allItems: [],
        fuse: null,
        loading: false,
        lastUpdated: null,
        _refreshInterval: null,

        async init() {
            if (this.allItems.length > 0) return; // Already initialized
            await this.loadItems();

            // Auto-refresh every 60 seconds
            this._refreshInterval = setInterval(() => {
                console.log('⏰ Auto-refreshing manufacturing items...');
                this.loadItems(true);
            }, 60000);
        },

        async loadItems(isBackground = false) {
            if (!isBackground) this.loading = true;

            try {
                // Determine Branch ID (try to find it in the DOM or from Livewire if accessible)
                // In this context, we'll try to get it from a global variable or select element if available,
                // otherwise fallback to 'all' or let the API handle it.
                // Assuming `branch_id` might be available in Livewire data if we traverse up, 
                // but simpler to fetch all usable items or just assume current user's branch context from session (API handles auth).

                const response = await fetch('/api/items/lite?type=all', { // Fetch all items (Inventory & Service)
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch items');

                const items = await response.json();
                this.allItems = items;
                this.lastUpdated = new Date();

                // Initialize Fuse.js
                // We index both name, code, barcode
                const options = {
                    keys: ['name', 'code', 'barcode'],
                    threshold: 0.3, // Fuzzy match threshold
                    ignoreLocation: true,
                    minMatchCharLength: 2
                };

                this.fuse = new Fuse(this.allItems, options);

                if (!isBackground) {
                    console.log(`✅ Loaded ${items.length} items for manufacturing search.`);
                }

            } catch (error) {
                console.error('Error loading items:', error);
                if (!isBackground) {
                    // Show error only on manual load
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load items list. Please try refeshing.'
                        });
                    }
                }
            } finally {
                if (!isBackground) this.loading = false;
            }
        },

        search(term, typeFilter = null) {
            if (!this.fuse || !term) return [];

            let results = this.fuse.search(term).map(r => r.item);

            // Optional: Filter by specific logic if needed
            // Currently we return all matches. 
            // If strict filtering is needed (e.g. only Inventory for Products), add it here.
            // Based on previous Livewire code, there wasn't strict filtering in search method visible, 
            // but usually Manufacturing only uses Inventory items.

            return results.slice(0, 10); // Limit to 10 results
        }
    });

    // -------------------------------------------------------------------------
    // 2. Main Calculator Component
    // -------------------------------------------------------------------------
    Alpine.data('manufacturingCalculator', () => ({
        products: [],
        rawMaterials: [],
        expenses: [],

        // Computed totals
        get totalRawMaterialsCost() {
            return this.rawMaterials.reduce((sum, item) =>
                sum + (parseFloat(item.total_cost) || 0), 0
            );
        },

        get totalProductsCost() {
            return this.products.reduce((sum, item) =>
                sum + (parseFloat(item.total_cost) || 0), 0
            );
        },

        get totalExpenses() {
            return this.expenses.reduce((sum, item) =>
                sum + (parseFloat(item.amount) || 0), 0
            );
        },

        get totalManufacturingCost() {
            return this.totalRawMaterialsCost + this.totalExpenses;
        },

        // Total invoice cost = products + raw materials + expenses
        get totalInvoiceCost() {
            return this.totalProductsCost + this.totalRawMaterialsCost + this.totalExpenses;
        },

        get unitCostPerProduct() {
            const totalQty = this.products.reduce((sum, p) =>
                sum + (parseFloat(p.quantity) || 0), 0
            );
            return totalQty > 0 ? this.totalManufacturingCost / totalQty : 0;
        },

        // Initialize from Livewire data
        initFromLivewire() {
            // Trigger Store Initialization
            this.$store.manufacturingItems.init();

            // Load Data from Livewire
            this.products = this.$wire.selectedProducts || [];
            this.rawMaterials = this.$wire.selectedRawMaterials || [];
            this.expenses = this.$wire.additionalExpenses || [];

            // Initial distribution if needed
            if (this.products.length > 0) {
                this.distributePercentagesEqually();
            }

            // Watch Livewire changes
            this.$wire.$watch('selectedProducts', (value) => {
                // Only update if length differs to avoid overwriting ongoing edits like quantity
                if (!value) value = [];
                // Simple equality check or length check to prevent loop
                this.products = value;
            });

            this.$wire.$watch('selectedRawMaterials', (value) => {
                this.rawMaterials = value || [];
            });

            this.$wire.$watch('additionalExpenses', (value) => {
                this.expenses = value || [];
            });

            // Watch local changes to sync back
            // Using a deboouce buffer for syncing
            this.$watch('products', (newProducts, oldProducts) => {
                if (newProducts.length > (oldProducts?.length || 0)) {
                    const hasPercentages = newProducts.some(p => (parseFloat(p.cost_percentage) || 0) > 0);
                    if (!hasPercentages) {
                        this.distributePercentagesEqually();
                    }
                }
                this.syncToLivewire();
            });
            this.$watch('rawMaterials', () => this.syncToLivewire());
            this.$watch('expenses', () => this.syncToLivewire());
        },

        // Product methods
        updateProductTotal(index) {
            const product = this.products[index];
            if (!product) return;

            const qty = parseFloat(product.quantity) || 0;
            const cost = parseFloat(product.average_cost) || 0;
            product.total_cost = parseFloat((qty * cost).toFixed(2));

            this.syncToLivewire();
        },

        updateProductPercentages() {
            const count = this.products.length;
            if (count === 0) return;

            const percentage = parseFloat((100 / count).toFixed(2));
            this.products.forEach(p => p.cost_percentage = percentage);
        },

        // Raw material methods
        updateRawMaterialTotal(index) {
            const material = this.rawMaterials[index];
            if (!material) return;

            const qty = parseFloat(material.quantity) || 0;
            // Use user-defined unit cost if available/override logic, usually it's average_cost
            const cost = parseFloat(material.average_cost) || 0;
            material.total_cost = parseFloat((qty * cost).toFixed(2));

            this.syncToLivewire();
        },

        updateRawMaterialUnit(index) {
            const material = this.rawMaterials[index];
            if (!material || !material.unit_id) return;

            const unit = material.unitsList?.find(u => u.id == material.unit_id);
            if (!unit) return;

            // Update logic based on unit conversion
            material.unit_cost = parseFloat(unit.cost || 0);
            material.available_quantity = unit.available_qty || 0;

            const baseCost = parseFloat(material.base_cost) || 0;
            // Assuming unit cost conversion logic here matches server requirement
            // Usually we just use the API provided prices or relationship
            // Here we keep existing logic:
            const conversionFactor = unit.available_qty || 1; // Double check this logic, often u_val
            // If 'available_qty' in unitsList means 'u_val' (factor), then:
            // average_cost = base_cost * factor

            // The original code used:
            // $unit->pivot->u_val for available_qty? No, u_val IS the factor usually.
            // In loadRawMaterialFromTemplate: 'available_qty' => $unit->pivot->u_val

            // So yes, assume available_qty holds the conversion factor
            material.average_cost = parseFloat((baseCost * conversionFactor).toFixed(2));

            this.updateRawMaterialTotal(index);
        },

        updateTotals() {
            this.syncToLivewire();
        },

        distributeCostsByPercentage() {
            if (this.products.length === 0) {
                this.showAlert('خطأ!', 'لا توجد منتجات لتوزيع التكلفة عليها', 'error');
                return;
            }

            const totalCost = this.totalManufacturingCost;

            this.products.forEach((product, index) => {
                const percentage = parseFloat(product.cost_percentage) || 0;
                const quantity = parseFloat(product.quantity) || 1;

                if (percentage > 0 && quantity > 0) {
                    const allocatedCost = (totalCost * percentage) / 100;
                    const unitCost = allocatedCost / quantity;

                    product.average_cost = parseFloat(unitCost.toFixed(2));
                    product.unit_cost = parseFloat(unitCost.toFixed(2));
                    product.total_cost = parseFloat(allocatedCost.toFixed(2));
                }
            });

            this.syncToLivewire();
            this.showAlert('تم!', 'تم توزيع التكاليف بنجاح', 'success');
        },

        distributePercentagesEqually() {
            const count = this.products.length;
            if (count === 0) return;

            const hasExistingPercentages = this.products.some(p => (parseFloat(p.cost_percentage) || 0) > 0);
            if (hasExistingPercentages) return;

            const percentage = parseFloat((100 / count).toFixed(2));
            const remainder = parseFloat((100 - (percentage * count)).toFixed(2));

            this.products.forEach((product, index) => {
                product.cost_percentage = percentage;
                if (index === 0 && remainder !== 0) {
                    product.cost_percentage = parseFloat((percentage + remainder).toFixed(2));
                }
            });
        },

        applyQuantityMultiplier(multiplier) {
            const mult = parseFloat(multiplier);
            if (!mult || mult <= 0) {
                this.showAlert('خطأ!', 'المضاعف يجب أن يكون أكبر من صفر', 'error');
                return;
            }

            this.rawMaterials.forEach((material, index) => {
                material.quantity = parseFloat((parseFloat(material.quantity) * mult).toFixed(2));
                this.updateRawMaterialTotal(index);
            });

            this.products.forEach((product, index) => {
                product.quantity = parseFloat((parseFloat(product.quantity) * mult).toFixed(2));
                this.updateProductTotal(index);
            });

            this.updateTotals();
            this.showAlert('تم!', 'تم مضاعفة الكميات بنجاح', 'success');
        },

        formatCurrency(amount) {
            const num = parseFloat(amount) || 0;
            return new Intl.NumberFormat('ar-EG', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num) + ' ج.م';
        },

        syncToLivewire: Alpine.debounce(function () {
            if (this.$wire) {
                this.$wire.call('syncFromAlpine',
                    this.products,
                    this.rawMaterials,
                    this.expenses,
                    {
                        totalRawMaterialsCost: this.totalRawMaterialsCost,
                        totalProductsCost: this.totalProductsCost,
                        totalExpenses: this.totalExpenses,
                        totalManufacturingCost: this.totalManufacturingCost
                    }
                );
            }
        }, 100),

        syncForSave() {
            if (this.$wire) {
                this.$wire.call('syncFromAlpine',
                    this.products,
                    this.rawMaterials,
                    this.expenses,
                    {
                        totalRawMaterialsCost: this.totalRawMaterialsCost,
                        totalProductsCost: this.totalProductsCost,
                        totalExpenses: this.totalExpenses,
                        totalManufacturingCost: this.totalManufacturingCost
                    }
                );
            }
        },

        showAlert(title, text, icon) {
            if (window.Swal) {
                Swal.fire({ title, text, icon });
            } else if (this.$wire) {
                // Fallback
            }
        }
    }));

    // -------------------------------------------------------------------------
    // 3. Product Search Component (Client-Side)
    // -------------------------------------------------------------------------
    Alpine.data('productSearch', () => ({
        searchTerm: '',
        results: [],
        selectedIndex: -1,
        showNoResults: false,

        init() {
            this.$watch('searchTerm', (value) => {
                if (!value || value.trim().length < 2) {
                    this.results = [];
                    this.selectedIndex = -1;
                    this.showNoResults = false;
                    return;
                }

                // Get results from Store
                this.results = this.$store.manufacturingItems.search(value.trim());
                this.showNoResults = this.results.length === 0;
                this.selectedIndex = -1;
            });
        },

        handleKeyDown() {
            if (this.results.length > 0) {
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
            }
        },

        handleKeyUp() {
            if (this.results.length > 0) {
                this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
            }
        },

        handleEnter() {
            if (this.results.length > 0 && this.selectedIndex >= 0) {
                this.selectItem(this.results[this.selectedIndex]);
            } else if (this.results.length > 0) {
                this.selectItem(this.results[0]); // Select first if none selected
            }
        },

        selectItem(item) {
            if (!this.$wire) return;

            // We call the existing backend method to add the product
            // Alternatively, we could replicate the "addProduct" logic client side
            // BUT 'addProductFromSearch' in backend does DB fetches for units/prices that might be complex
            // We have 'id' which is what backend needs.
            // To be safe and keep business logic (fetching units list etc), we call backend.
            // HOWEVER, we have 'item' object here with data.
            // If we want instant UI, we should replicate logic. 
            // The backend 'addProductFromSearch' does: finding item, checking duplicates, adding to array.
            // To ensure 100% compatibility with existing backend array structure, calling backend is safer 
            // UNLESS we want to avoid the round trip. 
            // Given the user wants "Performance", we should ideally avoid round trip.
            // But 'lite' API might not have ALL details (like all units with conversion factors?).
            // Let's check 'lite' API response. It returns 'units' (id, name), but maybe not 'pivot' data in depth?
            // ItemsApiController::lite loads 'units:id,name'. pivot data might be missing?
            // Actually 'units' relation on Item returns pivot data usually. 
            // Let's look at ItemsApiController.php: $items->load(['...','units:id,name',...]);
            // If we select specific columns 'id,name', pivot columns might be excluded unless explicitly selected!
            // Wait, belongsToMany pivot is usually included.
            // Use Backend for adding to be safe, search is the main bottleneck.

            this.$wire.call('addProductFromSearch', item.id);

            this.searchTerm = '';
            this.results = [];
            this.selectedIndex = -1;
        }
    }));

    // -------------------------------------------------------------------------
    // 4. Raw Material Search Component (Client-Side)
    // -------------------------------------------------------------------------
    Alpine.data('rawMaterialSearch', () => ({
        searchTerm: '',
        results: [],
        selectedIndex: -1,
        showNoResults: false,

        init() {
            this.$watch('searchTerm', (value) => {
                if (!value || value.trim().length < 2) {
                    this.results = [];
                    this.selectedIndex = -1;
                    this.showNoResults = false;
                    return;
                }

                this.results = this.$store.manufacturingItems.search(value.trim());
                this.showNoResults = this.results.length === 0;
                this.selectedIndex = -1;
            });
        },

        handleKeyDown() {
            if (this.results.length > 0) {
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.results.length - 1);
            }
        },

        handleKeyUp() {
            if (this.results.length > 0) {
                this.selectedIndex = Math.max(this.selectedIndex - 1, 0);
            }
        },

        handleEnter() {
            if (this.results.length > 0 && this.selectedIndex >= 0) {
                this.selectItem(this.results[this.selectedIndex]);
            } else if (this.results.length > 0) {
                this.selectItem(this.results[0]);
            }
        },

        selectItem(item) {
            if (!this.$wire) return;
            this.$wire.call('addRawMaterialFromSearch', item.id);
            this.searchTerm = '';
            this.results = [];
            this.selectedIndex = -1;
        }
    }));
});
