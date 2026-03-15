/**
 * Manufacturing Form Main Controller
 * Vanilla JavaScript with Fuse.js for client-side search
 */

import {
    showLoading,
    hideLoading,
    fetchJSON,
    showToast,
} from "./manufacturing-utilities.js";

import {
    calculateAllTotals,
    updateTotalsDisplay,
    distributeCostsByPercentage,
} from "./manufacturing-calculations.js";

import {
    renderProductsTable,
    renderRawMaterialsTable,
    renderExpensesTable,
    renderSearchResults,
} from "./manufacturing-render.js";

import {
    updateProductField,
    updateRawMaterialField,
    updateExpenseField,
    removeProduct,
    removeRawMaterial,
    removeExpense,
    syncFormInputs,
} from "./manufacturing-updates.js";

import {
    validateBeforeSave,
    setupRealtimeValidation,
    validateRawMaterialStock,
} from "./manufacturing-validations.js";

// Application state
const state = {
    products: [],
    rawMaterials: [],
    expenses: [],
    config: {},
    expenseAccounts: {},
    allItems: [], // All items loaded once for client-side search
    selectedSearchIndex: -1, // For keyboard navigation
    currentSearchType: null, // 'product' or 'rawMaterial'
    fuseProducts: null, // Fuse.js instance for products
    fuseRawMaterials: null, // Fuse.js instance for raw materials
    costsDistributed: false, // Track if costs have been distributed
    lastRawMaterialsTotal: 0, // Track last raw materials total to detect changes
};

// Initialize application
document.addEventListener("DOMContentLoaded", async function () {
    // Load configuration from window
    state.config = window.manufacturingConfig || {};
    state.expenseAccounts = window.expenseAccounts || {};

    console.log("Manufacturing form initialized");
    console.log("Config:", state.config);

    // Load all items for client-side search
    await loadAllItems();

    // Initialize event listeners
    initializeEventListeners();

    // Setup real-time validations
    setupRealtimeValidation(state);

    // Load initial data if editing invoice
    if (state.config.invoiceId) {
        await loadInvoiceData(state.config.invoiceId);
    }

    // Load initial data for template editing
    if (state.config.initialData) {
        console.log("📦 Loading initial template data...");
        console.log("Initial data:", state.config.initialData);

        // Process initial products to ensure cost matches selected unit
        state.products = (state.config.initialData.products || []).map(product => {
            let baseCost = parseFloat(product.average_cost) || 0;
            let unitFactor = 1;

            if (product.unitsList && product.unitsList.length > 0) {
                let selectedUnit = product.unitsList.find(u => u.id == product.unit_id);
                if (!selectedUnit) selectedUnit = product.unitsList[0];
                unitFactor = parseFloat(selectedUnit.u_val || 1);
            }

            const calculatedPrice = baseCost * unitFactor;
            const quantity = parseFloat(product.quantity) || 0;

            console.log(`📦 تحميل المنتج الأولي: ${product.name}`, {
                'الوحدة': product.unit_id,
                'المعامل': unitFactor,
                'التكلفة للوحدة': calculatedPrice,
                'الإجمالي': quantity * calculatedPrice
            });

            return {
                ...product,
                unit_cost: calculatedPrice,
                average_cost: baseCost,
                total_cost: quantity * calculatedPrice
            };
        });

        // Process initial raw materials to ensure cost matches selected unit
        state.rawMaterials = (state.config.initialData.rawMaterials || []).map(material => {
            let baseCost = parseFloat(material.average_cost) || 0;
            let unitFactor = 1;

            if (material.unitsList && material.unitsList.length > 0) {
                let selectedUnit = material.unitsList.find(u => u.id == material.unit_id);
                if (!selectedUnit) selectedUnit = material.unitsList[0];
                unitFactor = parseFloat(selectedUnit.u_val || 1);
            }

            const calculatedPrice = baseCost * unitFactor;
            const quantity = parseFloat(material.quantity) || 0;

            console.log(`🔧 تحميل المادة الخام الأولية: ${material.name}`, {
                'الوحدة': material.unit_id,
                'المعامل': unitFactor,
                'التكلفة للوحدة': calculatedPrice,
                'الإجمالي': quantity * calculatedPrice
            });

            return {
                ...material,
                unit_cost: calculatedPrice,
                average_cost: baseCost,
                total_cost: quantity * calculatedPrice
            };
        });

        state.expenses = state.config.initialData.expenses || [];

        console.log("Loaded:", {
            products: state.products.length,
            rawMaterials: state.rawMaterials.length,
            expenses: state.expenses.length,
        });

        // Render all tables
        renderProducts();
        renderRawMaterials();
        renderExpenses();

        // Calculate and sync
        calculateAllTotals(state);
        updateTotalsDisplay(state);
        syncFormInputs(state);

        console.log("✅ Initial template data loaded");
    }
});

// Load all items for client-side search
async function loadAllItems() {
    try {
        const branchId = document.getElementById("branch-id")?.value;
        const url = `/manufacturing/api/all-items${
            branchId ? `?branch_id=${branchId}` : ""
        }`;

        console.log("📡 Loading all items for client-side search...");
        showLoading("جاري تحميل الأصناف...");

        const response = await fetchJSON(url);

        if (response.success) {
            state.allItems = response.items;
            console.log(`✅ Loaded ${state.allItems.length} items`);

            // Initialize Fuse.js for products and raw materials
            if (typeof Fuse !== "undefined") {
                // Fuse.js for products (same items, will filter by type in search)
                state.fuseProducts = new Fuse(state.allItems, {
                    keys: ["name", "code", "barcode"],
                    threshold: 0.3,
                    ignoreLocation: true,
                    includeScore: true,
                });

                // Fuse.js for raw materials (same items, will filter by type in search)
                state.fuseRawMaterials = new Fuse(state.allItems, {
                    keys: ["name", "code", "barcode"],
                    threshold: 0.3,
                    ignoreLocation: true,
                    includeScore: true,
                });

                console.log("✅ Fuse.js initialized for search");
            } else {
                console.error(
                    "❌ Fuse.js not loaded! Search will use basic filtering."
                );
            }

            hideLoading();
        }
    } catch (error) {
        console.error("❌ Failed to load items:", error);
        showToast("فشل تحميل الأصناف", "error");
        hideLoading();
    }
}

// Initialize all event listeners
function initializeEventListeners() {
    // Tab switching
    initializeTabs();

    // Modals
    initializeModals();

    // Product search - instant with Fuse.js
    const productSearch = document.getElementById("product-search");
    const productResults = document.getElementById("product-search-results");
    if (productSearch) {
        productSearch.addEventListener("input", handleProductSearch);
        productSearch.addEventListener("keydown", handleSearchKeydown);
        productSearch.addEventListener("focus", handleProductSearchFocus);
    }

    // Raw material search - instant with Fuse.js
    const rawMaterialSearch = document.getElementById("raw-material-search");
    const rawMaterialResults = document.getElementById("raw-material-search-results");
    if (rawMaterialSearch) {
        rawMaterialSearch.addEventListener("input", handleRawMaterialSearch);
        rawMaterialSearch.addEventListener("keydown", handleSearchKeydown);
        rawMaterialSearch.addEventListener(
            "focus",
            handleRawMaterialSearchFocus
        );
    }

    // Close search dropdowns when clicking outside
    document.addEventListener("click", function (event) {
        console.log("🖱️ Click detected:", event.target);
        
        // Check if click is outside product search
        if (productSearch && productResults) {
            const productSearchContainer = productSearch.parentElement;
            console.log("Product container:", productSearchContainer);
            console.log("Contains click?", productSearchContainer?.contains(event.target));
            
            if (productSearchContainer && !productSearchContainer.contains(event.target)) {
                console.log("✅ Hiding product results");
                productResults.classList.add("hidden");
            }
        }

        // Check if click is outside raw material search
        if (rawMaterialSearch && rawMaterialResults) {
            const rawMaterialSearchContainer = rawMaterialSearch.parentElement;
            console.log("Raw material container:", rawMaterialSearchContainer);
            console.log("Contains click?", rawMaterialSearchContainer?.contains(event.target));
            
            if (rawMaterialSearchContainer && !rawMaterialSearchContainer.contains(event.target)) {
                console.log("✅ Hiding raw material results");
                rawMaterialResults.classList.add("hidden");
            }
        }
    });

    // Add expense button
    const addExpenseBtn = document.getElementById("btn-add-expense");
    if (addExpenseBtn) {
        addExpenseBtn.addEventListener("click", handleAddExpense);
        console.log("✅ Add expense button listener attached");
    } else {
        console.error("❌ Add expense button not found!");
    }

    // Distribute costs button
    const distributeCostsBtn = document.getElementById("btn-distribute-costs");
    if (distributeCostsBtn) {
        distributeCostsBtn.addEventListener("click", handleDistributeCosts);
        console.log("✅ Distribute costs button listener attached");
    } else {
        console.error("❌ Distribute costs button not found!");
    }

    // Save invoice button
    const saveBtn = document.getElementById("btn-save-invoice");
    if (saveBtn) {
        saveBtn.addEventListener("click", handleSaveInvoice);
        console.log("✅ Save invoice button listener attached");
    } else {
        console.error("❌ Save invoice button not found!");
    }

    // Branch change - reload items
    const branchSelect = document.getElementById("branch-id");
    if (branchSelect) {
        branchSelect.addEventListener("change", function () {
            loadAllItems();
            // Update hidden input
            const branchInput = document.getElementById("form-branch-id");
            if (branchInput) {
                branchInput.value = this.value;
            }
        });
    }

    // Sync account selects with hidden inputs
    const productAccountSelect = document.getElementById("product-account");
    if (productAccountSelect) {
        productAccountSelect.addEventListener("change", function () {
            const input = document.getElementById("product-account-input");
            if (input) input.value = this.value;
        });
        // Set initial value
        const input = document.getElementById("product-account-input");
        if (input) input.value = productAccountSelect.value;
    }

    const rawAccountSelect = document.getElementById("raw-material-account");
    if (rawAccountSelect) {
        rawAccountSelect.addEventListener("change", function () {
            const input = document.getElementById("raw-account-input");
            if (input) input.value = this.value;

            // Update item details if an item is selected
            if (
                window.ManufacturingApp.lastSelectedIndex !== undefined &&
                window.ManufacturingApp.lastSelectedType
            ) {
                showItemDetails(
                    window.ManufacturingApp.lastSelectedIndex,
                    window.ManufacturingApp.lastSelectedType
                );
            }
        });
        // Set initial value
        const input = document.getElementById("raw-account-input");
        if (input) input.value = rawAccountSelect.value;
    }

    const employeeSelect = document.getElementById("employee-select");
    if (employeeSelect) {
        employeeSelect.addEventListener("change", function () {
            const input = document.getElementById("employee-id");
            if (input) input.value = this.value;
        });
        // Set initial value
        const input = document.getElementById("employee-id");
        if (input) input.value = employeeSelect.value;
    }
}

// Initialize tabs
function initializeTabs() {
    const tabButtons = {
        "tab-raw-materials": "tab-content-raw-materials",
        "tab-expenses": "tab-content-expenses",
    };

    Object.keys(tabButtons).forEach((tabId) => {
        const tabButton = document.getElementById(tabId);
        if (tabButton) {
            tabButton.addEventListener("click", () => {
                console.log("🔄 Switching to tab:", tabId);
                switchTab(tabId, tabButtons);
            });
        }
    });

    console.log("✅ Tabs initialized");
}

// Initialize modals
function initializeModals() {
    // Save template button
    const btnSaveTemplate = document.getElementById("btn-save-template");
    if (btnSaveTemplate) {
        btnSaveTemplate.addEventListener("click", () => {
            openModal("modal-save-template");
        });
        console.log("✅ Save template button listener attached");
    }

    // Load template button
    const btnLoadTemplate = document.getElementById("btn-load-template");
    if (btnLoadTemplate) {
        btnLoadTemplate.addEventListener("click", () => {
            openModal("modal-load-template");
            loadTemplatesList();
        });
        console.log("✅ Load template button listener attached");
    }

    // Confirm save template button
    const btnConfirmSaveTemplate = document.getElementById(
        "btn-confirm-save-template"
    );
    if (btnConfirmSaveTemplate) {
        btnConfirmSaveTemplate.addEventListener("click", handleSaveTemplate);
    }

    // Confirm load template button
    const btnConfirmLoadTemplate = document.getElementById(
        "btn-confirm-load-template"
    );
    if (btnConfirmLoadTemplate) {
        btnConfirmLoadTemplate.addEventListener("click", handleLoadTemplate);
    }

    // Close buttons
    document.querySelectorAll(".modal-close").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const modal = e.target.closest('[id^="modal-"]');
            if (modal) {
                closeModal(modal.id);
            }
        });
    });

    // Close on backdrop click
    document.querySelectorAll('[id^="modal-"]').forEach((modal) => {
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                closeModal(modal.id);
            }
        });
    });

    console.log("✅ Modals initialized");
}

// Open modal
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove("hidden");
        document.body.style.overflow = "hidden";
        console.log("📂 Modal opened:", modalId);
    }
}

// Close modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("hidden");
        document.body.style.overflow = "";
        console.log("📁 Modal closed:", modalId);
    }
}

// Handle save template
async function handleSaveTemplate() {
    const templateName = document.getElementById("template-name")?.value;

    if (!templateName || templateName.trim() === "") {
        showToast("يرجى إدخال اسم القالب", "error");
        return;
    }

    // Validate
    if (state.products.length === 0) {
        showToast("يجب إضافة منتج واحد على الأقل", "error");
        return;
    }

    if (state.rawMaterials.length === 0) {
        showToast("يجب إضافة مادة خام واحدة على الأقل", "error");
        return;
    }

    // Validate cost distribution (percentages must sum to 100%)
    const totalPercentage = state.products.reduce((sum, product) => {
        return sum + (parseFloat(product.cost_percentage) || 0);
    }, 0);

    // Check if there are expenses
    const hasExpenses = state.expenses.length > 0;
    const totalExpenses = state.expenses.reduce((sum, expense) => {
        return sum + (parseFloat(expense.amount) || 0);
    }, 0);

    if (Math.abs(totalPercentage - 100) > 0.01) {
        hideLoading();
        showToast(
            `يجب توزيع التكاليف أولاً! مجموع النسب الحالي: ${totalPercentage.toFixed(2)}% (يجب أن يكون 100%)`,
            "error"
        );
        
        // Highlight distribute costs button
        const distributeBtn = document.getElementById("btn-distribute-costs");
        if (distributeBtn) {
            distributeBtn.classList.add("animate-pulse", "btn-danger");
            distributeBtn.classList.remove("btn-outline-secondary");
            setTimeout(() => {
                distributeBtn.classList.remove("animate-pulse", "btn-danger");
                distributeBtn.classList.add("btn-outline-secondary");
            }, 3000);
        }
        
        return;
    }

    // If there are expenses, validate that costs include them
    if (hasExpenses && totalExpenses > 0) {
        const totals = calculateAllTotals(state);
        const rawMaterialsCost = totals.rawMaterials;
        const totalManufacturingCost = totals.manufacturing;
        const productsCost = totals.products;

        console.log('💰 التحقق من توزيع المصروفات:', {
            'تكلفة المواد الخام': rawMaterialsCost,
            'إجمالي المصروفات': totalExpenses,
            'تكلفة التصنيع الكلية': totalManufacturingCost,
            'تكلفة المنتجات': productsCost,
            'الفرق': Math.abs(productsCost - totalManufacturingCost)
        });

        if (productsCost < (totalManufacturingCost - 0.1)) {
            const difference = totalManufacturingCost - productsCost;
            hideLoading();
            showToast(
                `يوجد مصروفات إضافية بقيمة ${formatNumber(totalExpenses)} جنيه لم يتم توزيعها! الفرق: ${formatNumber(difference)} جنيه. يجب الضغط على زر "توزيع التكاليف" لتوزيع المصروفات على المنتجات`,
                "error"
            );
            
            // Highlight distribute costs button
            const distributeBtn = document.getElementById("btn-distribute-costs");
            if (distributeBtn) {
                distributeBtn.classList.add("animate-pulse", "btn-danger");
                distributeBtn.classList.remove("btn-outline-secondary");
                setTimeout(() => {
                    distributeBtn.classList.remove("animate-pulse", "btn-danger");
                    distributeBtn.classList.add("btn-outline-secondary");
                }, 3000);
            }
            
            return;
        }
    }

    console.log("💾 Saving template:", templateName);

    try {
        showLoading("جاري حفظ النموذج...");

        // Get form
        const form = document.getElementById("manufacturing-form");
        if (!form) {
            console.error("❌ Form not found!");
            hideLoading();
            showToast("خطأ: لم يتم العثور على النموذج", "error");
            return;
        }

        // Sync all form inputs first (this includes unit_id)
        syncFormInputs(state);

        // Set data arrays in hidden inputs with proper unit_id
        const productsInput = document.getElementById("form-products");
        const rawMaterialsInput = document.getElementById("form-raw-materials");
        const expensesInput = document.getElementById("form-expenses");

        if (!productsInput || !rawMaterialsInput || !expensesInput) {
            console.error("❌ Hidden inputs not found!");
            hideLoading();
            showToast("خطأ: لم يتم العثور على حقول البيانات", "error");
            return;
        }

        // Ensure unit_id is included in JSON
        const productsWithUnits = state.products.map((p) => ({
            id: p.id,
            name: p.name,
            quantity: p.quantity,
            unit_id: p.unit_id, // Important: include unit_id
            unit_cost: p.unit_cost || p.average_cost || 0,
            average_cost: p.average_cost || 0,
            total_cost: p.total_cost || 0,
            cost_percentage: p.cost_percentage || 0,
        }));

        const rawMaterialsWithUnits = state.rawMaterials.map((m) => ({
            id: m.id,
            name: m.name,
            quantity: m.quantity,
            unit_id: m.unit_id, // Important: include unit_id
            unit_cost: m.unit_cost || m.average_cost || 0,
            average_cost: m.average_cost || 0,
            total_cost: m.total_cost || 0,
        }));

        console.log("📦 Products with units:", productsWithUnits);
        console.log("📦 Raw materials with units:", rawMaterialsWithUnits);
        console.log(
            "📦 Products unit_ids:",
            productsWithUnits.map((p) => ({ name: p.name, unit_id: p.unit_id }))
        );
        console.log(
            "📦 Materials unit_ids:",
            rawMaterialsWithUnits.map((m) => ({
                name: m.name,
                unit_id: m.unit_id,
            }))
        );

        productsInput.value = JSON.stringify(productsWithUnits);
        rawMaterialsInput.value = JSON.stringify(rawMaterialsWithUnits);
        expensesInput.value = JSON.stringify(state.expenses);

        // Add template flag and name
        let isTemplateInput = document.getElementById("form-is-template");
        if (!isTemplateInput) {
            isTemplateInput = document.createElement("input");
            isTemplateInput.type = "hidden";
            isTemplateInput.id = "form-is-template";
            isTemplateInput.name = "is_template";
            form.appendChild(isTemplateInput);
        }
        isTemplateInput.value = "1";

        let templateNameInput = document.getElementById("form-template-name");
        if (!templateNameInput) {
            templateNameInput = document.createElement("input");
            templateNameInput.type = "hidden";
            templateNameInput.id = "form-template-name";
            templateNameInput.name = "template_name";
            form.appendChild(templateNameInput);
        }
        templateNameInput.value = templateName.trim();

        // Add expected time
        const expectedTime = document.getElementById("template-expected-time")?.value;
        let expectedTimeInput = document.getElementById("form-expected-time");
        if (!expectedTimeInput) {
            expectedTimeInput = document.createElement("input");
            expectedTimeInput.type = "hidden";
            expectedTimeInput.id = "form-expected-time";
            expectedTimeInput.name = "expected_time";
            form.appendChild(expectedTimeInput);
        }
        expectedTimeInput.value = expectedTime ? expectedTime.trim() : "";

        console.log("💾 حفظ النموذج:", {
            'اسم النموذج': templateName.trim(),
            'الوقت المتوقع': expectedTime
        });

        closeModal("modal-save-template");

        // Clear inputs
        if (document.getElementById("template-name")) {
            document.getElementById("template-name").value = "";
        }
        if (document.getElementById("template-expected-time")) {
            document.getElementById("template-expected-time").value = "";
        }

        // Submit form to save in database
        form.submit();
    } catch (error) {
        console.error("❌ Save template error:", error);
        showToast("حدث خطأ أثناء حفظ النموذج: " + error.message, "error");
        hideLoading();
    }
}

// Handle load template
function handleLoadTemplate() {
    console.log("📂 Loading template...");
    // TODO: Implement template loading
    showToast("جاري تحميل القالب...", "info");
    closeModal("modal-load-template");
}

// Load templates list
async function loadTemplatesList() {
    const container = document.getElementById("template-modal-content");
    if (!container) return;

    console.log("📋 Loading templates list from server...");

    try {
        // Show loading
        container.innerHTML = `
            <div class="text-center py-12">
                <div class="animate-spin h-8 w-8 border-4 border-primary border-t-transparent rounded-full mx-auto"></div>
                <p class="mt-4 text-gray-500">جاري تحميل النماذج...</p>
            </div>
        `;

        // Fetch templates from server
        const response = await fetch("/manufacturing/api/active-templates");
        if (!response.ok) {
            throw new Error("Failed to fetch templates");
        }

        const data = await response.json();
        const templates = data.templates || [];

        console.log("📦 Found templates:", templates.length);

        if (templates.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <svg class="h-16 w-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">لا توجد نماذج محفوظة</p>
                    <p class="text-gray-400 text-xs mt-2">احفظ نموذجاً أولاً لرؤيته هنا</p>
                </div>
            `;
            return;
        }

        // Render templates list with filter
        container.innerHTML = `
            <!-- Search Filter -->
            <div class="mb-4">
                <div class="relative">
                    <input type="text" 
                        id="template-search-filter" 
                        class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="ابحث عن نموذج بالاسم..."
                        autocomplete="off">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </div>
            </div>

            <!-- Templates Table -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">اسم النموذج</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">المنتجات</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">الخامات</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">التكلفة</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">المضاعف</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="templates-table-body">
                        ${templates
                            .map(
                                (template) => `
                            <tr class="template-row hover:bg-gray-50 transition-colors" data-template-name="${escapeHtml(template.name).toLowerCase()}">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">${escapeHtml(template.name)}</div>
                                    <div class="text-xs text-gray-500">${new Date(template.date).toLocaleDateString("ar-EG")}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        📦 ${template.data.products?.length || 0}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        🔧 ${template.data.rawMaterials?.length || 0}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-sm font-medium text-gray-900">${formatNumber(
                                        (template.data.products || []).reduce((sum, p) => sum + (p.total_cost || 0), 0) +
                                        (template.data.rawMaterials || []).reduce((sum, m) => sum + (m.total_cost || 0), 0) +
                                        (template.data.expenses || []).reduce((sum, e) => sum + (e.amount || 0), 0)
                                    )}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number"
                                        class="quantity-multiplier w-20 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-primary focus:border-primary text-center"
                                        data-template-id="${template.id}"
                                        value="1"
                                        min="0.1"
                                        step="0.1">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" 
                                        class="btn-load-template-item px-4 py-2 bg-primary text-white rounded-lg hover:bg-accent transition-all text-sm font-medium inline-flex items-center gap-2" 
                                        data-template-id="${template.id}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                        </svg>
                                        تحميل
                                    </button>
                                </td>
                            </tr>
                        `
                            )
                            .join("")}
                    </tbody>
                </table>
            </div>
        `;

        // Add filter functionality
        const filterInput = document.getElementById("template-search-filter");
        if (filterInput) {
            filterInput.addEventListener("input", function() {
                const searchTerm = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll(".template-row");
                
                rows.forEach(row => {
                    const templateName = row.dataset.templateName || "";
                    if (templateName.includes(searchTerm)) {
                        row.classList.remove("hidden");
                    } else {
                        row.classList.add("hidden");
                    }
                });
            });
        }

        // Attach event listeners
        container.querySelectorAll(".btn-load-template-item").forEach((btn) => {
            btn.addEventListener("click", async (e) => {
                const templateId = parseInt(e.currentTarget.dataset.templateId);
                const multiplierInput = container.querySelector(
                    `.quantity-multiplier[data-template-id="${templateId}"]`
                );
                const multiplier = parseFloat(multiplierInput?.value || 1);

                // Find template - data is already loaded!
                const template = templates.find((t) => t.id === templateId);
                if (template) {
                    // Use cached data instead of fetching again
                    await loadTemplateDataDirect(template.data, multiplier);
                }
            });
        });
    } catch (error) {
        console.error("❌ Failed to load templates:", error);
        container.innerHTML = `
            <div class="text-center py-12">
                <svg class="h-16 w-16 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
                <p class="text-red-500 text-sm">فشل تحميل النماذج</p>
                <p class="text-gray-400 text-xs mt-2">${error.message}</p>
            </div>
        `;
    }
}

// Helper to escape HTML
function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

// Load template data from server
async function loadTemplateDataFromServer(templateId, multiplier = 1) {
    console.log(
        "📂 Loading template from server:",
        templateId,
        "Multiplier:",
        multiplier
    );

    try {
        showLoading("جاري تحميل النموذج...");

        // Fetch template data from server
        const response = await fetch(
            `/manufacturing/templates/${templateId}/data`
        );
        if (!response.ok) {
            throw new Error("Failed to fetch template data");
        }

        const templateData = await response.json();

        // Validate multiplier
        if (multiplier <= 0 || isNaN(multiplier)) {
            showToast("يرجى إدخال رقم صحيح للمضاعفة", "error");
            hideLoading();
            return;
        }

        state.products = (templateData.products || []).map((product) => {
            // Find current item in state.allItems
            const currentItem = state.allItems.find(item => item.id == product.id);
            let baseCost = parseFloat(product.average_cost) || 0;

            let selectedUnit = null;
            let unitFactor = 1;

            if (currentItem) {
                baseCost = parseFloat(currentItem.average_cost) || baseCost;
                if (currentItem.units && currentItem.units.length > 0) {
                    selectedUnit = currentItem.units.find(u => u.id == product.unit_id);
                    if (!selectedUnit) {
                        selectedUnit = currentItem.units[0];
                    }
                    unitFactor = parseFloat(selectedUnit.pivot ? selectedUnit.pivot.u_val : (selectedUnit.u_val || 1));
                }
            } else if (product.unitsList && product.unitsList.length > 0) {
                selectedUnit = product.unitsList.find(u => u.id == product.unit_id);
                if (!selectedUnit) selectedUnit = product.unitsList[0];
                unitFactor = parseFloat(selectedUnit.u_val || 1);
            }

            const calculatedPrice = baseCost * unitFactor;
            const quantity = (parseFloat(product.quantity) || 0) * multiplier;

            console.log(`📦 تحميل المنتج: ${product.name}`, {
                'الوحدة المختارة': product.unit_id,
                'معامل الوحدة': unitFactor,
                'التكلفة الأساسية': baseCost,
                'التكلفة بعد الوحدة': calculatedPrice,
                'الكمية المضروبة': quantity
            });

            return {
                ...product,
                unit_cost: calculatedPrice,
                average_cost: baseCost,
                quantity: quantity,
                total_cost: quantity * calculatedPrice,
            };
        });

        // Load raw materials with multiplied quantities - use current average cost and unit factor
        state.rawMaterials = (templateData.rawMaterials || []).map(
            (material) => {
                // Find current item in state.allItems
                const currentItem = state.allItems.find(item => item.id == material.id);
                let baseCost = parseFloat(material.average_cost) || 0;

                let selectedUnit = null;
                let unitFactor = 1;

                if (currentItem) {
                    baseCost = parseFloat(currentItem.average_cost) || baseCost;
                    if (currentItem.units && currentItem.units.length > 0) {
                        selectedUnit = currentItem.units.find(u => u.id == material.unit_id);
                        if (!selectedUnit) {
                            selectedUnit = currentItem.units[0];
                        }
                        unitFactor = parseFloat(selectedUnit.pivot ? selectedUnit.pivot.u_val : (selectedUnit.u_val || 1));
                    }
                } else if (material.unitsList && material.unitsList.length > 0) {
                    selectedUnit = material.unitsList.find(u => u.id == material.unit_id);
                    if (!selectedUnit) selectedUnit = material.unitsList[0];
                    unitFactor = parseFloat(selectedUnit.u_val || 1);
                }

                const calculatedPrice = baseCost * unitFactor;
                const quantity = (parseFloat(material.quantity) || 0) * multiplier;

                console.log(`🔧 تحميل المادة الخام: ${material.name}`, {
                    'الوحدة المختارة': material.unit_id,
                    'معامل الوحدة': unitFactor,
                    'التكلفة الأساسية': baseCost,
                    'التكلفة بعد الوحدة': calculatedPrice,
                    'الكمية المضروبة': quantity
                });

                return {
                    ...material,
                    unit_cost: calculatedPrice,
                    average_cost: baseCost,
                    quantity: quantity,
                    total_cost: quantity * calculatedPrice,
                };
            }
        );

        // Load expenses with multiplied amounts
        state.expenses = (templateData.expenses || []).map((expense) => {
            const originalAmount = parseFloat(expense.amount) || 0;
            const multipliedAmount = originalAmount * multiplier;

            console.log(`💰 تحميل المصروف:`, {
                'الوصف': expense.description || 'N/A',
                'المبلغ الأصلي': originalAmount,
                'المضاعف': multiplier,
                'المبلغ بعد المضاعفة': multipliedAmount
            });

            return {
                ...expense,
                amount: multipliedAmount,
            };
        });

        // Render tables
        renderProducts();
        renderRawMaterials();
        renderExpenses();

        // Calculate totals
        calculateAllTotals(state);
        updateTotalsDisplay(state);

        // Auto-distribute costs after loading template
        console.log("🎯 Auto-distributing costs after template load...");
        handleDistributeCosts();
        
        // Round unit_cost to nearest 4
        console.log("🔢 Rounding unit_cost to nearest 4...");
        state.products = state.products.map(product => {
            const originalUnitCost = product.unit_cost;
            const roundedUnitCost = Math.ceil(originalUnitCost / 4) * 4;
            const quantity = parseFloat(product.quantity) || 0;
            
            console.log(`📦 ${product.name}: ${originalUnitCost.toFixed(2)} → ${roundedUnitCost}`);
            
            return {
                ...product,
                unit_cost: roundedUnitCost,
                average_cost: roundedUnitCost,
                total_cost: roundedUnitCost * quantity
            };
        });
        
        // Re-render products with rounded prices
        renderProducts();
        updateTotalsDisplay(state);

        // Close modal
        closeModal("modal-load-template");

        hideLoading();
        showToast(
            `تم تحميل النموذج بنجاح${
                multiplier !== 1 ? ` (الكميات × ${multiplier})` : ""
            }`,
            "success"
        );
    } catch (error) {
        console.error("❌ Failed to load template:", error);
        hideLoading();
        showToast("فشل تحميل النموذج: " + error.message, "error");
    }
}

// Load template data directly from cached data (client-side, no server request)
async function loadTemplateDataDirect(templateData, multiplier = 1) {
    console.log("⚡ Loading template data (client-side):", { multiplier });

    try {
        // Validate multiplier
        if (multiplier <= 0 || isNaN(multiplier)) {
            showToast("يرجى إدخال رقم صحيح للمضاعفة", "error");
            return;
        }

        showLoading("جاري تحميل النموذج...");

        // Load products with multiplied quantities
        state.products = (templateData.products || []).map((product) => {
            const currentItem = state.allItems.find(item => item.id == product.id);
            let baseCost = parseFloat(product.average_cost) || 0;
            let unitFactor = 1;

            if (currentItem) {
                baseCost = parseFloat(currentItem.average_cost) || baseCost;
                if (currentItem.units && currentItem.units.length > 0) {
                    const selectedUnit = currentItem.units.find(u => u.id == product.unit_id);
                    if (selectedUnit) {
                        unitFactor = parseFloat(selectedUnit.pivot ? selectedUnit.pivot.u_val : (selectedUnit.u_val || 1));
                    }
                }
            }

            const calculatedPrice = baseCost * unitFactor;
            const quantity = (parseFloat(product.quantity) || 0) * multiplier;

            return {
                ...product,
                unit_cost: calculatedPrice,
                average_cost: baseCost,
                quantity: quantity,
                total_cost: quantity * calculatedPrice,
                units: currentItem?.units || product.units || [],
                unitsList: currentItem?.units || product.unitsList || [],
            };
        });

        // Load raw materials with multiplied quantities
        state.rawMaterials = (templateData.rawMaterials || []).map((material) => {
            const currentItem = state.allItems.find(item => item.id == material.id);
            let baseCost = parseFloat(material.average_cost) || 0;
            let unitFactor = 1;

            if (currentItem) {
                baseCost = parseFloat(currentItem.average_cost) || baseCost;
                if (currentItem.units && currentItem.units.length > 0) {
                    const selectedUnit = currentItem.units.find(u => u.id == material.unit_id);
                    if (selectedUnit) {
                        unitFactor = parseFloat(selectedUnit.pivot ? selectedUnit.pivot.u_val : (selectedUnit.u_val || 1));
                    }
                }
            }

            const calculatedPrice = baseCost * unitFactor;
            const quantity = (parseFloat(material.quantity) || 0) * multiplier;

            return {
                ...material,
                unit_cost: calculatedPrice,
                average_cost: baseCost,
                quantity: quantity,
                total_cost: quantity * calculatedPrice,
                units: currentItem?.units || material.units || [],
                unitsList: currentItem?.units || material.unitsList || [],
            };
        });

        // Load expenses with multiplied amounts
        state.expenses = (templateData.expenses || []).map((expense) => ({
            ...expense,
            amount: (parseFloat(expense.amount) || 0) * multiplier,
        }));

        // Render tables
        renderProducts();
        renderRawMaterials();
        renderExpenses();

        // Calculate totals
        calculateAllTotals(state);
        updateTotalsDisplay(state);
        
        // Auto-distribute costs after loading template
        console.log("🎯 Auto-distributing costs after template load...");
        console.log("📊 State before distribution:", JSON.parse(JSON.stringify(state.products)));
        handleDistributeCosts();
        console.log("📊 State after distribution:", JSON.parse(JSON.stringify(state.products)));
        
        // Round unit_cost to nearest 4
        console.log("🔢 Rounding unit_cost to nearest 4...");
        state.products = state.products.map(product => {
            const originalUnitCost = product.unit_cost;
            const roundedUnitCost = Math.ceil(originalUnitCost / 4) * 4;
            const quantity = parseFloat(product.quantity) || 0;
            
            console.log(`📦 ${product.name}: ${originalUnitCost.toFixed(2)} → ${roundedUnitCost}`);
            
            return {
                ...product,
                unit_cost: roundedUnitCost,
                average_cost: roundedUnitCost,
                total_cost: roundedUnitCost * quantity
            };
        });
        
        // Re-render products with rounded prices
        renderProducts();
        updateTotalsDisplay(state);

        // Close modal
        closeModal("modal-load-template");

        hideLoading();
        showToast(
            `تم تحميل النموذج بنجاح${multiplier !== 1 ? ` (الكميات × ${multiplier})` : ""}`,
            "success"
        );
    } catch (error) {
        console.error("❌ Failed to load template:", error);
        hideLoading();
        showToast("فشل تحميل النموذج: " + error.message, "error");
    }
}

// Load template data into form
function loadTemplateData(template, multiplier = 1) {
    console.log(
        "📂 Loading template data:",
        template,
        "Multiplier:",
        multiplier
    );

    if (!template || !template.data) {
        showToast("خطأ في تحميل القالب", "error");
        return;
    }

    // Validate multiplier
    if (multiplier <= 0 || isNaN(multiplier)) {
        showToast("يرجى إدخال رقم صحيح للمضاعفة", "error");
        return;
    }

    // Load products with multiplied quantities and CURRENT prices
    state.products = (template.data.products || []).map((product) => {
        // Find current item data to get latest average_cost
        const currentItem = state.allItems.find(
            (item) => item.id === product.id
        );

        if (currentItem) {
            const defaultUnit =
                currentItem.units && currentItem.units.length > 0
                    ? currentItem.units[0]
                    : null;
            const selectedUnit =
                product.unit_id && currentItem.units
                    ? currentItem.units.find((u) => u.id == product.unit_id)
                    : defaultUnit;

            // ✅ حساب التكلفة حسب الوحدة المحددة في النموذج
            const baseUnitCost = currentItem.average_cost || 0; // تكلفة الوحدة الأساسية
            const unitFactor = selectedUnit ? parseFloat(selectedUnit.u_val) || 1 : 1; // معامل التحويل للوحدة المحددة
            const displayUnitCost = baseUnitCost * unitFactor; // التكلفة بالوحدة المحددة (كرتونة/قطعة)

            console.log(`💰 Updated product price: ${product.name}`, {
                oldPrice: product.unit_cost,
                newPrice: displayUnitCost,
                baseUnitCost,
                unitFactor,
                selectedUnit: selectedUnit?.name || 'N/A',
            });

            return {
                ...product,
                units: currentItem.units || [],
                unitsList: currentItem.units || [],
                average_cost: baseUnitCost, // تكلفة الوحدة الأساسية
                unit_cost: displayUnitCost, // التكلفة بالوحدة المحددة
                quantity: (parseFloat(product.quantity) || 0) * multiplier,
                total_cost:
                    (parseFloat(product.quantity) || 0) *
                    multiplier *
                    displayUnitCost,
            };
        }

        // If item not found, use template data
        console.warn(
            `⚠️ Item not found in current items: ${product.name} (ID: ${product.id})`
        );
        return {
            ...product,
            quantity: (parseFloat(product.quantity) || 0) * multiplier,
            total_cost:
                (parseFloat(product.quantity) || 0) *
                multiplier *
                (parseFloat(product.unit_cost) || 0),
        };
    });

    // Load raw materials with multiplied quantities and CURRENT prices
    state.rawMaterials = (template.data.rawMaterials || []).map((material) => {
        // Find current item data to get latest average_cost
        const currentItem = state.allItems.find(
            (item) => item.id === material.id
        );

        if (currentItem) {
            const defaultUnit =
                currentItem.units && currentItem.units.length > 0
                    ? currentItem.units[0]
                    : null;
            const selectedUnit =
                material.unit_id && currentItem.units
                    ? currentItem.units.find((u) => u.id == material.unit_id)
                    : defaultUnit;

            // ✅ حساب التكلفة حسب الوحدة المحددة في النموذج
            const baseUnitCost = currentItem.average_cost || 0; // تكلفة الوحدة الأساسية
            const unitFactor = selectedUnit ? parseFloat(selectedUnit.u_val) || 1 : 1; // معامل التحويل للوحدة المحددة
            const displayUnitCost = baseUnitCost * unitFactor; // التكلفة بالوحدة المحددة (كرتونة/قطعة)

            console.log(`💰 Updated material price: ${material.name}`, {
                oldPrice: material.unit_cost,
                newPrice: displayUnitCost,
                baseUnitCost,
                unitFactor,
                selectedUnit: selectedUnit?.name || 'N/A',
            });

            return {
                ...material,
                units: currentItem.units || [],
                unitsList: currentItem.units || [],
                average_cost: baseUnitCost, // تكلفة الوحدة الأساسية
                unit_cost: displayUnitCost, // التكلفة بالوحدة المحددة
                quantity: (parseFloat(material.quantity) || 0) * multiplier,
                total_cost:
                    (parseFloat(material.quantity) || 0) *
                    multiplier *
                    displayUnitCost,
            };
        }

        // If item not found, use template data
        console.warn(
            `⚠️ Item not found in current items: ${material.name} (ID: ${material.id})`
        );
        return {
            ...material,
            quantity: (parseFloat(material.quantity) || 0) * multiplier,
            total_cost:
                (parseFloat(material.quantity) || 0) *
                multiplier *
                (parseFloat(material.unit_cost) || 0),
        };
    });

    // Load expenses with multiplied amounts
    state.expenses = (template.data.expenses || []).map((expense) => {
        const originalAmount = parseFloat(expense.amount) || 0;
        const multipliedAmount = originalAmount * multiplier;

        console.log(`💰 تحميل المصروف:`, {
            'الوصف': expense.description || 'N/A',
            'المبلغ الأصلي': originalAmount,
            'المضاعف': multiplier,
            'المبلغ بعد المضاعفة': multipliedAmount
        });

        return {
            ...expense,
            amount: multipliedAmount,
        };
    });

    console.log("✅ Template loaded with multiplier and updated prices:", {
        multiplier,
        products: state.products.length,
        rawMaterials: state.rawMaterials.length,
        expenses: state.expenses.length,
        expensesData: state.expenses,
    });

    // Re-render all tables
    renderProducts();
    renderRawMaterials();
    renderExpenses();

    // Update totals
    updateTotalsDisplay(state);

    // Auto-distribute costs after loading template
    console.log("🎯 Auto-distributing costs after template load...");
    handleDistributeCosts();
    
    // Round unit_cost to nearest 4
    console.log("🔢 Rounding unit_cost to nearest 4...");
    state.products = state.products.map(product => {
        const originalUnitCost = product.unit_cost;
        const roundedUnitCost = Math.ceil(originalUnitCost / 4) * 4;
        const quantity = parseFloat(product.quantity) || 0;
        
        console.log(`📦 ${product.name}: ${originalUnitCost.toFixed(2)} → ${roundedUnitCost}`);
        
        return {
            ...product,
            unit_cost: roundedUnitCost,
            average_cost: roundedUnitCost,
            total_cost: roundedUnitCost * quantity
        };
    });
    
    // Re-render products with rounded prices
    renderProducts();
    updateTotalsDisplay(state);

    // Close modal
    closeModal("modal-load-template");

    const message =
        multiplier === 1
            ? `تم تحميل القالب: ${template.name} (بالأسعار الحالية)`
            : `تم تحميل القالب: ${template.name} (الكميات × ${multiplier} بالأسعار الحالية)`;

    showToast(message, "success");
}

// Delete template
function deleteTemplate(index) {
    if (
        !confirm(
            window.__("Are you sure you want to delete this template?") ||
                "هل أنت متأكد من حذف هذا القالب؟"
        )
    ) {
        return;
    }

    console.log("🗑️ Deleting template at index:", index);

    const templates = JSON.parse(
        localStorage.getItem("manufacturing_templates") || "[]"
    );
    templates.splice(index, 1);
    localStorage.setItem("manufacturing_templates", JSON.stringify(templates));

    // Reload list
    loadTemplatesList();

    showToast("تم حذف القالب", "success");
}

// Switch tab
function switchTab(activeTabId, tabButtons) {
    // Update tab buttons
    Object.keys(tabButtons).forEach((tabId) => {
        const button = document.getElementById(tabId);
        const content = document.getElementById(tabButtons[tabId]);

        if (tabId === activeTabId) {
            // Active tab
            button.classList.remove(
                "text-gray-400",
                "hover:text-gray-700",
                "font-medium"
            );
            button.classList.add(
                "text-primary",
                "font-bold",
                "border-b-2",
                "border-primary"
            );
            content.classList.remove("hidden");
        } else {
            // Inactive tab
            button.classList.remove(
                "text-primary",
                "font-bold",
                "border-b-2",
                "border-primary"
            );
            button.classList.add(
                "text-gray-400",
                "hover:text-gray-700",
                "font-medium"
            );
            content.classList.add("hidden");
        }
    });
}

// Render products table with callbacks
function renderProducts() {
    renderProductsTable(state.products, {
        onUpdate: (index, field, value) => {
            console.log("🔄 Product update:", { index, field, value });
            state.products = updateProductField(
                state.products,
                index,
                field,
                value
            );
            updateTotalsDisplay(state);

            // If unit changed, re-render entire table to update all fields
            if (field === "unit_id") {
                console.log("🔄 Unit changed, re-rendering products table");
                renderProducts();
            } else {
                // Only update the specific cells that need updating
                updateProductRow(index);
            }
        },
        onRemove: (index) => {
            console.log("🗑️ Remove product:", index);
            state.products = removeProduct(state.products, index);
            updateTotalsDisplay(state);
            renderProducts(); // Re-render to update display
            showToast("تم حذف المنتج", "success");
        },
    });
}

// Update a specific product row without re-rendering entire table
function updateProductRow(index) {
    const product = state.products[index];
    if (!product) return;

    const tbody = document.getElementById("products-table-body");
    if (!tbody) return;

    const row = tbody.children[index];
    if (!row) return;

    // Update unit cost field (column 3 - index 3)
    const unitCostCell = row.children[3]?.querySelector("input");
    if (unitCostCell) {
        unitCostCell.value = product.unit_cost || product.average_cost || 0;
    }

    // Update total cost field (column 5 - index 5)
    const totalCell = row.children[5]?.querySelector("input");
    if (totalCell) {
        const formatCurrency = (value) =>
            formatNumber(value) + " " + (window.__("EGP") || "EGP");
        totalCell.value = formatCurrency(product.total_cost || 0);
    }
}

// Render raw materials table with callbacks
function renderRawMaterials() {
    renderRawMaterialsTable(state.rawMaterials, {
        onUpdate: (index, field, value) => {
            console.log("🔄 Raw material update:", { index, field, value });

            // Store old unit_cost for comparison
            const oldUnitCost = state.rawMaterials[index]?.unit_cost;

            state.rawMaterials = updateRawMaterialField(
                state.rawMaterials,
                index,
                field,
                value
            );

            const newUnitCost = state.rawMaterials[index]?.unit_cost;

            console.log("💰 Unit cost change:", {
                field,
                oldUnitCost,
                newUnitCost,
                changed: oldUnitCost !== newUnitCost,
            });

            // Update totals first to get accurate calculations
            updateTotalsDisplay(state);

            // Mark costs as not distributed if raw materials changed after distribution
            if (state.costsDistributed && (field === "quantity" || field === "unit_id")) {
                const totals = calculateAllTotals(state);
                const currentTotal = totals.rawMaterials + totals.expenses;
                
                console.log("🔍 تغيير في المواد الخام:", {
                    'تم التوزيع؟': state.costsDistributed,
                    'الحقل المتغير': field,
                    'الإجمالي السابق': state.lastRawMaterialsTotal,
                    'الإجمالي الحالي': currentTotal,
                    'الفرق': Math.abs(currentTotal - state.lastRawMaterialsTotal)
                });
                
                // If total changed significantly (more than 0.1 EGP), mark as needs redistribution
                if (Math.abs(currentTotal - state.lastRawMaterialsTotal) > 0.1) {
                    state.costsDistributed = false;
                    console.log("⚠️ تغيرت تكلفة المواد الخام، يجب إعادة توزيع التكاليف قبل الحفظ");
                }
            }

            // Always re-render when unit changes to show updated price
            if (field === "unit_id") {
                console.log(
                    "🔄 Unit changed, re-rendering raw materials table"
                );
                // Re-render will show the new unit_cost
                renderRawMaterials();
            } else if (field === "quantity") {
                // Only update total when quantity changes
                updateRawMaterialRow(index);
            }
        },
        onRemove: (index) => {
            console.log("🗑️ Remove raw material:", index);
            state.rawMaterials = removeRawMaterial(state.rawMaterials, index);
            
            // Mark as needs redistribution if costs were distributed
            if (state.costsDistributed) {
                state.costsDistributed = false;
                console.log("⚠️ تم حذف مادة خام، يجب إعادة توزيع التكاليف قبل الحفظ");
            }
            
            updateTotalsDisplay(state);
            renderRawMaterials(); // Re-render to update display
            showToast("تم حذف المادة الخام", "success");
        },
    });
}

// Update a specific raw material row without re-rendering entire table
function updateRawMaterialRow(index) {
    const material = state.rawMaterials[index];
    if (!material) return;

    const tbody = document.getElementById("raw-materials-table-body");
    if (!tbody) return;

    const row = tbody.children[index];
    if (!row) return;

    console.log("🔄 Updating raw material row:", {
        index,
        name: material.name,
        unit_cost: material.unit_cost,
        total_cost: material.total_cost,
    });

    // Update unit cost field (column 3 - index 3)
    const costCell = row.children[3]?.querySelector(".material-unit-cost");
    if (costCell) {
        costCell.value = formatNumber(material.unit_cost || 0);
        console.log("✅ Updated unit cost display:", costCell.value);
    } else {
        console.warn("⚠️ Unit cost cell not found");
    }

    // Update total cost field (column 4 - index 4)
    const totalCell = row.children[4]?.querySelector("input");
    if (totalCell) {
        const formatCurrency = (value) =>
            formatNumber(value) + " " + (window.__("EGP") || "EGP");
        totalCell.value = formatCurrency(material.total_cost || 0);
        console.log("✅ Updated total cost display:", totalCell.value);
    } else {
        console.warn("⚠️ Total cost cell not found");
    }
}

// Helper function for formatting (imported from utilities)
function formatNumber(value) {
    const num = parseFloat(value) || 0;
    return num.toLocaleString("en-US", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}

// Render expenses table with callbacks
function renderExpenses() {
    renderExpensesTable(state.expenses, state.expenseAccounts, {
        onUpdate: (index, field, value) => {
            console.log("🔄 Expense update:", { index, field, value });
            state.expenses = updateExpenseField(
                state.expenses,
                index,
                field,
                value
            );
            
            // Mark costs as not distributed if expenses changed after distribution
            if (state.costsDistributed && field === "amount") {
                const totals = calculateAllTotals(state);
                const currentTotal = totals.rawMaterials + totals.expenses;
                
                // If total changed significantly (more than 0.1 EGP), mark as needs redistribution
                if (Math.abs(currentTotal - state.lastRawMaterialsTotal) > 0.1) {
                    state.costsDistributed = false;
                    console.log("⚠️ تغيرت المصروفات، يجب إعادة توزيع التكاليف قبل الحفظ");
                }
            }
            
            updateTotalsDisplay(state);
            // No need to re-render - expenses don't have calculated fields
        },
        onRemove: (index) => {
            console.log("🗑️ Remove expense:", index);
            state.expenses = removeExpense(state.expenses, index);
            
            // Mark as needs redistribution if costs were distributed
            if (state.costsDistributed) {
                state.costsDistributed = false;
                console.log("⚠️ تم حذف مصروف، يجب إعادة توزيع التكاليف قبل الحفظ");
            }
            
            updateTotalsDisplay(state);
            renderExpenses(); // Re-render to update display
            showToast("تم حذف المصروف", "success");
        },
    });
}

// Handle keyboard navigation in search results
function handleSearchKeydown(e) {
    const resultsId =
        e.target.id === "product-search"
            ? "product-search-results"
            : "raw-material-search-results";
    const resultsContainer = document.getElementById(resultsId);

    if (!resultsContainer || resultsContainer.children.length === 0) return;

    const items = Array.from(
        resultsContainer.querySelectorAll("[data-item-id]")
    );
    if (items.length === 0) return;

    let currentIndex = state.selectedSearchIndex;

    if (e.key === "ArrowDown") {
        e.preventDefault();
        currentIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
    } else if (e.key === "ArrowUp") {
        e.preventDefault();
        currentIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
    } else if (e.key === "Enter") {
        e.preventDefault();
        if (currentIndex >= 0 && items[currentIndex]) {
            items[currentIndex].click();
        }
        return;
    } else if (e.key === "Escape") {
        e.preventDefault();
        // Clear search and hide results
        e.target.value = "";
        renderSearchResults(resultsId, [], () => {});
        state.selectedSearchIndex = -1;
        return;
    } else {
        return;
    }

    state.selectedSearchIndex = currentIndex;

    // Update highlighting
    items.forEach((item, index) => {
        if (index === currentIndex) {
            item.classList.add("bg-primary", "text-white");
            item.classList.remove("hover:bg-gray-50");
            item.scrollIntoView({ block: "nearest", behavior: "smooth" });
        } else {
            item.classList.remove("bg-primary", "text-white");
            item.classList.add("hover:bg-gray-50");
        }
    });
}

// Handle product search (instant with Fuse.js)
function handleProductSearch(e) {
    const searchTerm = e.target.value.trim();

    console.log("🔍 Product search:", searchTerm);

    // Show all items if search is empty
    if (searchTerm.length === 0) {
        const allProducts = state.allItems
            .filter((item) => item.type === 1)
            .slice(0, 50);

        state.selectedSearchIndex = allProducts.length > 0 ? 0 : -1;
        state.currentSearchType = "product";
        renderSearchResults(
            "product-search-results",
            allProducts,
            handleProductSelect
        );
        return;
    }

    let results = [];

    if (state.fuseProducts && typeof Fuse !== "undefined") {
        // Use Fuse.js for fuzzy search
        const fuseResults = state.fuseProducts.search(searchTerm);
        results = fuseResults
            .map((r) => r.item)
            .filter((item) => item.type === 1) // Only inventory items
            .slice(0, 50);
    } else {
        // Fallback to basic filtering
        const searchLower = searchTerm.toLowerCase();
        results = state.allItems
            .filter((item) => {
                if (item.type !== 1) return false;

                const nameMatch = item.name.toLowerCase().includes(searchLower);
                const codeMatch =
                    item.code &&
                    item.code.toString().toLowerCase().includes(searchLower);
                const barcodeMatch =
                    item.barcode &&
                    item.barcode.some((b) =>
                        b.toLowerCase().includes(searchLower)
                    );

                return nameMatch || codeMatch || barcodeMatch;
            })
            .slice(0, 50);
    }

    console.log(`📋 Found ${results.length} products`);

    state.selectedSearchIndex = results.length > 0 ? 0 : -1;
    state.currentSearchType = "product";
    renderSearchResults("product-search-results", results, handleProductSelect);
}

// Handle product search focus
function handleProductSearchFocus(e) {
    // Show all items when focusing on empty input
    if (e.target.value.trim().length === 0) {
        const allProducts = state.allItems
            .filter((item) => item.type === 1)
            .slice(0, 50);

        state.selectedSearchIndex = allProducts.length > 0 ? 0 : -1;
        state.currentSearchType = "product";
        renderSearchResults(
            "product-search-results",
            allProducts,
            handleProductSelect
        );
    } else {
        handleProductSearch(e);
    }
}

// Handle raw material search (instant with Fuse.js)
function handleRawMaterialSearch(e) {
    const searchTerm = e.target.value.trim();

    console.log("🔍 Raw material search:", searchTerm);

    // Show all items if search is empty
    if (searchTerm.length === 0) {
        const allMaterials = state.allItems
            .filter((item) => item.type === 1)
            .slice(0, 50);

        state.selectedSearchIndex = allMaterials.length > 0 ? 0 : -1;
        state.currentSearchType = "rawMaterial";
        renderSearchResults(
            "raw-material-search-results",
            allMaterials,
            handleRawMaterialSelect
        );
        return;
    }

    let results = [];

    if (state.fuseRawMaterials && typeof Fuse !== "undefined") {
        // Use Fuse.js for fuzzy search
        const fuseResults = state.fuseRawMaterials.search(searchTerm);
        results = fuseResults
            .map((r) => r.item)
            .filter((item) => item.type === 1) // Only inventory items
            .slice(0, 50);
    } else {
        // Fallback to basic filtering
        const searchLower = searchTerm.toLowerCase();
        results = state.allItems
            .filter((item) => {
                if (item.type !== 1) return false;

                const nameMatch = item.name.toLowerCase().includes(searchLower);
                const codeMatch =
                    item.code &&
                    item.code.toString().toLowerCase().includes(searchLower);
                const barcodeMatch =
                    item.barcode &&
                    item.barcode.some((b) =>
                        b.toLowerCase().includes(searchLower)
                    );

                return nameMatch || codeMatch || barcodeMatch;
            })
            .slice(0, 50);
    }

    console.log(`📋 Found ${results.length} raw materials`);

    state.selectedSearchIndex = results.length > 0 ? 0 : -1;
    state.currentSearchType = "rawMaterial";
    renderSearchResults(
        "raw-material-search-results",
        results,
        handleRawMaterialSelect
    );
}

// Handle raw material search focus
function handleRawMaterialSearchFocus(e) {
    // Show all items when focusing on empty input
    if (e.target.value.trim().length === 0) {
        const allMaterials = state.allItems
            .filter((item) => item.type === 1)
            .slice(0, 50);

        state.selectedSearchIndex = allMaterials.length > 0 ? 0 : -1;
        state.currentSearchType = "rawMaterial";
        renderSearchResults(
            "raw-material-search-results",
            allMaterials,
            handleRawMaterialSelect
        );
    } else {
        handleRawMaterialSearch(e);
    }
}

// Handle product selection
function handleProductSelect(productId) {
    console.log("✅ Product selected:", productId);

    // Find item in allItems
    const item = state.allItems.find((i) => i.id === productId);
    if (!item) {
        console.error("❌ Item not found:", productId);
        return;
    }

    console.log("📦 Item data:", {
        id: item.id,
        name: item.name,
        code: item.code,
        units: item.units,
        average_cost: item.average_cost,
        barcode: item.barcode,
    });

    // Check if already added
    if (state.products.find((p) => p.id === productId)) {
        showToast("هذا المنتج موجود بالفعل", "warning");
        return;
    }

    // Add product to state with all data
    const defaultUnit =
        item.units && item.units.length > 0 ? item.units[0] : null;
    const baseUnitCost = item.average_cost || 0;
    const unitFactor = defaultUnit ? defaultUnit.u_val || 1 : 1;
    const displayUnitCost = baseUnitCost * unitFactor;

    // Validation: Check if item already exists in raw materials
    const existsInRawMaterials = state.rawMaterials.some(material => material.id === productId);
    if (existsInRawMaterials) {
        showToast(window.__('manufacturing.item_exists_in_raw_materials') || 'هذا الصنف موجود بالفعل في الخامات', 'error');
        return;
    }

    // Validation: Check if item already exists in products
    const existsInProducts = state.products.some(product => product.id === productId);
    if (existsInProducts) {
        showToast(window.__('manufacturing.item_already_added') || 'هذا الصنف مضاف بالفعل', 'error');
        return;
    }

    const product = {
        id: productId,
        name: item.name,
        code: item.code,
        units: item.units || [],
        unitsList: item.units || [],
        unit_id: defaultUnit ? defaultUnit.id : null,
        unit_name: defaultUnit ? defaultUnit.name : "",
        quantity: 1,
        average_cost: baseUnitCost, // Base unit cost (per smallest unit)
        unit_cost: displayUnitCost, // Display unit cost (per selected unit)
        cost_percentage: 0,
        total_cost: displayUnitCost, // Total = quantity * unit_cost
    };

    console.log("➕ Adding product to state:", {
        ...product,
        baseUnitCost,
        unitFactor,
        displayUnitCost,
    });

    state.products.push(product);

    // Re-render products table
    renderProducts();

    // Clear search
    document.getElementById("product-search").value = "";
    renderSearchResults("product-search-results", [], () => {});

    // Recalculate totals
    updateTotalsDisplay(state);
}

// Handle raw material selection
function handleRawMaterialSelect(materialId) {
    console.log("✅ Raw material selected:", materialId);

    // Find item in allItems
    const item = state.allItems.find((i) => i.id === materialId);
    if (!item) {
        console.error("❌ Item not found:", materialId);
        return;
    }

    console.log("📦 Item data:", {
        id: item.id,
        name: item.name,
        code: item.code,
        units: item.units,
        average_cost: item.average_cost,
        barcode: item.barcode,
    });

    // Check if already added
    // Validation: Check if item already exists in products
    const existsInProducts = state.products.some(product => product.id === materialId);
    if (existsInProducts) {
        showToast(window.__('manufacturing.item_exists_in_products') || 'هذا الصنف موجود بالفعل في المنتجات', 'error');
        return;
    }

    // Validation: Check if item already exists in raw materials
    if (state.rawMaterials.find((m) => m.id === materialId)) {
        showToast(window.__('manufacturing.item_already_added') || 'هذا الصنف مضاف بالفعل', 'warning');
        return;
    }

    // Add raw material to state with all data
    const defaultUnit =
        item.units && item.units.length > 0 ? item.units[0] : null;
    const baseUnitCost = item.average_cost || 0;
    const unitFactor = defaultUnit ? defaultUnit.u_val || 1 : 1;
    const displayUnitCost = baseUnitCost * unitFactor;

    const material = {
        id: materialId,
        name: item.name,
        code: item.code,
        units: item.units || [],
        unitsList: item.units || [],
        unit_id: defaultUnit ? defaultUnit.id : null,
        unit_name: defaultUnit ? defaultUnit.name : "",
        quantity: 1,
        average_cost: baseUnitCost, // Base unit cost (per smallest unit)
        unit_cost: displayUnitCost, // Display unit cost (per selected unit)
        total_cost: displayUnitCost, // Total = quantity * unit_cost
    };

    console.log("➕ Adding raw material to state:", {
        ...material,
        baseUnitCost,
        unitFactor,
        displayUnitCost,
    });

    state.rawMaterials.push(material);

    // Re-render raw materials table
    renderRawMaterials();

    // Clear search
    document.getElementById("raw-material-search").value = "";
    renderSearchResults("raw-material-search-results", [], () => {});

    // Recalculate totals
    updateTotalsDisplay(state);
}

// Handle distribute costs
function handleDistributeCosts() {
    console.log("🎯 Distribute costs button clicked");
    console.log("📊 Current state:", {
        productsCount: state.products.length,
        rawMaterialsCount: state.rawMaterials.length,
        expensesCount: state.expenses.length,
        products: state.products,
    });

    if (state.products.length === 0) {
        showToast("لا توجد منتجات لتوزيع التكاليف عليها", "warning");
        return;
    }

    console.log("🔄 Calling distributeCostsByPercentage...");
    distributeCostsByPercentage(state);

    console.log("📊 After distribution:", state.products);

    console.log("🔄 Updating totals display...");
    updateTotalsDisplay(state);

    console.log("🔄 Re-rendering products table...");
    // Re-render products table
    renderProducts();

    // Mark costs as distributed and save current raw materials total
    state.costsDistributed = true;
    const totals = calculateAllTotals(state);
    state.lastRawMaterialsTotal = totals.rawMaterials + totals.expenses;

    console.log("✅ Distribution complete", {
        'تم التوزيع': state.costsDistributed,
        'إجمالي المواد الخام والمصروفات': state.lastRawMaterialsTotal
    });
    
    showToast("تم توزيع التكاليف بنجاح", "success");
}

// Handle add expense
function handleAddExpense() {
    console.log("➕ Add expense button clicked");

    // Get default account ID from select
    const accountSelect = document.getElementById("expense-account");
    const defaultAccountId = accountSelect
        ? accountSelect.value
        : Object.keys(state.expenseAccounts)[0];

    console.log("📊 Default account ID:", defaultAccountId);

    // Add new expense
    state.expenses.push({
        amount: 0,
        account_id: defaultAccountId,
        description: "",
    });

    console.log("✅ Expense added:", state.expenses);

    // Mark as needs redistribution if costs were distributed
    if (state.costsDistributed) {
        state.costsDistributed = false;
        console.log("⚠️ تمت إضافة مصروف جديد، يجب إعادة توزيع التكاليف قبل الحفظ");
    }

    // Re-render expenses table
    renderExpenses();

    // Update totals
    updateTotalsDisplay(state);
}

// Handle save invoice
async function handleSaveInvoice() {
    try {
        // Run all validations
        const isValid = await validateBeforeSave(state);
        if (!isValid) {
            return;
        }

        // Validate cost distribution (percentages must sum to 100%)
        const totalPercentage = state.products.reduce((sum, product) => {
            return sum + (parseFloat(product.cost_percentage) || 0);
        }, 0);

        // Check if there are expenses
        const hasExpenses = state.expenses.length > 0;
        const totalExpenses = state.expenses.reduce((sum, expense) => {
            return sum + (parseFloat(expense.amount) || 0);
        }, 0);

        if (Math.abs(totalPercentage - 100) > 0.01) {
            showToast(
                `يجب توزيع التكاليف أولاً! مجموع النسب الحالي: ${totalPercentage.toFixed(2)}% (يجب أن يكون 100%)`,
                "error"
            );

            // Highlight distribute costs button
            const distributeBtn = document.getElementById("btn-distribute-costs");
            if (distributeBtn) {
                distributeBtn.classList.add("animate-pulse", "btn-danger");
                distributeBtn.classList.remove("btn-outline-secondary");
                setTimeout(() => {
                    distributeBtn.classList.remove("animate-pulse", "btn-danger");
                    distributeBtn.classList.add("btn-outline-secondary");
                }, 3000);
            }

            return;
        }

        // If there are expenses, validate that costs include them
        if (hasExpenses && totalExpenses > 0) {
            const totals = calculateAllTotals(state);
            const rawMaterialsCost = totals.rawMaterials;
            const totalManufacturingCost = totals.manufacturing; // This includes raw materials + expenses
            const productsCost = totals.products;

            // Check if products cost equals manufacturing cost (raw materials + expenses)
            // If products cost is less than manufacturing cost, expenses are not distributed
            // Allow small tolerance for rounding errors (0.1 EGP)
            if (productsCost < (totalManufacturingCost - 0.1)) {
                const difference = totalManufacturingCost - productsCost;
                showToast(
                    `يوجد مصروفات إضافية بقيمة ${formatNumber(totalExpenses)} جنيه لم يتم توزيعها! الفرق: ${formatNumber(difference)} جنيه. يجب الضغط على زر "توزيع التكاليف" لتوزيع المصروفات على المنتجات`,
                    "error"
                );

                // Highlight distribute costs button
                const distributeBtn = document.getElementById("btn-distribute-costs");
                if (distributeBtn) {
                    distributeBtn.classList.add("animate-pulse", "btn-danger");
                    distributeBtn.classList.remove("btn-outline-secondary");
                    setTimeout(() => {
                        distributeBtn.classList.remove("animate-pulse", "btn-danger");
                        distributeBtn.classList.add("btn-outline-secondary");
                    }, 3000);
                }

                return;
            }
        }

        // Validate variance (difference should not be positive)
        const totals = calculateAllTotals(state);
        const variance = totals.products - totals.manufacturing;

        if (variance > 0) {
            const varianceAmount = formatNumber(variance);
            showToast(
                `لا يمكن حفظ الفاتورة: قيمة المنتجات أكبر من تكلفة التصنيع بمقدار ${varianceAmount} ${
                    window.__("EGP") || "EGP"
                }`,
                "error"
            );

            // Highlight variance section
            const varianceSection = document.getElementById(
                "summary-variance-amount"
            );
            if (varianceSection) {
                varianceSection.parentElement.parentElement.classList.add(
                    "animate-pulse",
                    "border-2",
                    "border-red-500",
                    "rounded-lg",
                    "p-2"
                );
                setTimeout(() => {
                    varianceSection.parentElement.parentElement.classList.remove(
                        "animate-pulse",
                        "border-2",
                        "border-red-500",
                        "rounded-lg",
                        "p-2"
                    );
                }, 3000);
            }

            return;
        }

        showLoading("جاري الحفظ...");

        // Get form
        const form = document.getElementById("manufacturing-form");
        if (!form) {
            console.error("❌ Form not found!");
            hideLoading();
            showToast("خطأ: لم يتم العثور على النموذج", "error");
            return;
        }

        // Set data arrays in hidden inputs
        const productsInput = document.getElementById("form-products");
        const rawMaterialsInput = document.getElementById("form-raw-materials");
        const expensesInput = document.getElementById("form-expenses");

        if (!productsInput || !rawMaterialsInput || !expensesInput) {
            console.error("❌ Hidden inputs not found!", {
                productsInput: !!productsInput,
                rawMaterialsInput: !!rawMaterialsInput,
                expensesInput: !!expensesInput,
            });
            hideLoading();
            showToast("خطأ: لم يتم العثور على حقول البيانات", "error");
            return;
        }

        productsInput.value = JSON.stringify(state.products);
        rawMaterialsInput.value = JSON.stringify(state.rawMaterials);
        expensesInput.value = JSON.stringify(state.expenses);

        // Sync template metadata if in edit mode
        if (state.config.isEditMode) {
            const templateNameInput = document.getElementById(
                "template-name-input"
            );
            const expectedTimeInput =
                document.getElementById("actual-time-input");
            const productAccountSelect =
                document.getElementById("product-account");
            const rawMaterialAccountSelect = document.getElementById(
                "raw-material-account"
            );
            const employeeSelect = document.getElementById(
                "employee-select-view"
            );

            if (templateNameInput)
                document.getElementById("form-template-name").value =
                    templateNameInput.value;
            if (expectedTimeInput)
                document.getElementById("form-expected-time").value =
                    expectedTimeInput.value;
            if (productAccountSelect)
                document.getElementById("product-account-input").value =
                    productAccountSelect.value;
            if (rawMaterialAccountSelect)
                document.getElementById("raw-account-input").value =
                    rawMaterialAccountSelect.value;
            if (employeeSelect)
                document.getElementById("employee-id").value =
                    employeeSelect.value;
        }

        // Submit form
        form.submit();
    } catch (error) {
        console.error("❌ Save error:", error);
        showToast("حدث خطأ أثناء الحفظ: " + error.message, "error");
        hideLoading();
    }
}

// Load invoice data for editing
async function loadInvoiceData(invoiceId) {
    try {
        showLoading("Loading invoice...");

        // Check if we have existing data from the view
        if (window.existingInvoiceData) {
            console.log(
                "📦 Loading existing invoice data from view:",
                window.existingInvoiceData
            );

            // Load data into state
            state.products = window.existingInvoiceData.products || [];
            state.rawMaterials = window.existingInvoiceData.rawMaterials || [];
            state.expenses = window.existingInvoiceData.expenses || [];
            // Check units in raw materials
            if (state.rawMaterials.length > 0) {
                console.log(
                    "🔍 First raw material units:",
                    state.rawMaterials[0].units
                );
                console.log(
                    "🔍 First raw material unitsList:",
                    state.rawMaterials[0].unitsList
                );
            }

            // Render tables
            renderProducts();
            renderRawMaterials();
            renderExpenses();

            // Calculate totals
            updateTotalsDisplay(state);

            hideLoading();
            return;
        }

        // Fallback to API if no data in view
        const url = `/manufacturing/${invoiceId}/edit-data`;
        const data = await fetchJSON(url);

        // Load data into state
        state.products = data.products || [];
        state.rawMaterials = data.rawMaterials || [];
        state.expenses = data.expenses || [];

        // Render tables
        renderProducts();
        renderRawMaterials();
        renderExpenses();

        // Calculate totals
        updateTotalsDisplay(state);

        hideLoading();
    } catch (error) {
        console.error("Failed to load invoice:", error);
        showToast("فشل تحميل الفاتورة", "error");
        hideLoading();
    }
}

// Show item details in the card
function showItemDetails(index, type = "product") {
    console.log("👁️ Show item details:", { index, type });

    // Save last selected item for warehouse change updates
    window.ManufacturingApp.lastSelectedIndex = index;
    window.ManufacturingApp.lastSelectedType = type;

    const item =
        type === "product" ? state.products[index] : state.rawMaterials[index];

    if (!item) {
        console.warn("⚠️ Item not found at index:", index);
        return;
    }

    console.log("📦 Item from state:", item);

    // Helper function to safely update element
    const safeUpdate = (id, value) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
            console.log(`✅ Updated ${id}:`, value);
        } else {
            console.warn(`⚠️ Element not found: ${id}`);
        }
    };

    // Find the full item data from allItems array
    const fullItem = state.allItems.find((i) => i.id === item.id);
    console.log("📦 Full item from allItems:", fullItem);

    if (fullItem) {
        console.log("📊 Full item details:", {
            id: fullItem.id,
            name: fullItem.name,
            warehouse_stocks: fullItem.warehouse_stocks,
            stock_quantity: fullItem.stock_quantity,
            last_purchase_price: fullItem.last_purchase_price,
            average_cost: fullItem.average_cost,
        });
    }

    // Set item name
    safeUpdate("selected-item-name", item.name || "-");

    // Find unit name from item data
    const selectedUnit = item.unitsList?.find((u) => u.id == item.unit_id);
    const unitName = selectedUnit
        ? `${selectedUnit.name} (${formatNumber(selectedUnit.u_val || 1)} ${
              window.__("pieces") || "قطعة"
          })`
        : item.unit_name || "-";
    safeUpdate("selected-item-unit", unitName);

    // Set price from item (current price being used)
    safeUpdate(
        "selected-item-price",
        (item.unit_cost || item.average_cost || 0).toFixed(2)
    );

    // Set last purchase price from fullItem data
    const lastPurchasePrice = fullItem?.last_purchase_price || 0;
    safeUpdate("selected-item-last-price", lastPurchasePrice.toFixed(2));

    // Set average cost from fullItem data
    const averageCost = fullItem?.average_cost || item.average_cost || 0;
    safeUpdate("selected-item-avg-cost", averageCost.toFixed(2));

    // Set total stock from fullItem data
    const totalStock = fullItem?.stock_quantity || 0;
    safeUpdate("selected-item-total", totalStock.toLocaleString());

    // Get store name from raw material account select
    const storeSelect = document.getElementById("raw-material-account");
    const storeName =
        storeSelect && storeSelect.selectedIndex >= 0
            ? storeSelect.options[storeSelect.selectedIndex].text
            : "-";
    safeUpdate("selected-item-store", storeName);

    // Get warehouse stock from fullItem data
    const warehouseId = document.getElementById("raw-material-account")?.value;
    console.log("🏪 Selected warehouse ID:", warehouseId);

    if (warehouseId && fullItem?.warehouse_stocks) {
        console.log("📦 Warehouse stocks object:", fullItem.warehouse_stocks);
        console.log(
            "📦 Available warehouses:",
            Object.keys(fullItem.warehouse_stocks)
        );

        // Get stock for this specific warehouse from client-side data
        const warehouseStock = fullItem.warehouse_stocks[warehouseId] || 0;
        console.log(`📦 Stock in warehouse ${warehouseId}:`, warehouseStock);

        safeUpdate("selected-item-available", warehouseStock.toLocaleString());
    } else {
        console.log(
            "📦 No warehouse selected or no warehouse_stocks data, showing total stock"
        );
        // No warehouse selected, show total stock
        safeUpdate("selected-item-available", totalStock.toLocaleString());
    }

    console.log("✅ Item details updated successfully");
}

// Export state for debugging
window.manufacturingState = state;

// Export functions for global access
window.ManufacturingApp = {
    showItemDetails,
    calculateAllTotals: (stateObj) => {
        calculateAllTotals(stateObj || state);
        updateTotalsDisplay(stateObj || state);
    },
    syncFormInputs: (stateObj) => {
        syncFormInputs(stateObj || state);
    },
    state,
    lastSelectedIndex: undefined,
    lastSelectedType: undefined,
};
