/**
 * Manufacturing Calculations
 * All calculation logic for manufacturing invoices
 */

import { parseFloatSafe, formatNumber } from "./manufacturing-utilities.js";

// Calculate product total
export function calculateProductTotal(product) {
    const quantity = parseFloatSafe(product.quantity);
    const unitCost = parseFloatSafe(product.unit_cost);
    return quantity * unitCost;
}

// Calculate raw material total
export function calculateRawMaterialTotal(material) {
    const quantity = parseFloatSafe(material.quantity);
    const unitCost = parseFloatSafe(material.unit_cost);
    return quantity * unitCost;
}

// Calculate all totals
export function calculateAllTotals(state) {
    const products = state.products || [];
    const rawMaterials = state.rawMaterials || [];
    const expenses = state.expenses || [];

    const totals = {
        products: 0,
        rawMaterials: 0,
        expenses: 0,
        manufacturing: 0,
    };

    // Calculate products total
    products.forEach((product, index) => {
        const total = calculateProductTotal(product);
        totals.products += total;
    });

    // Calculate raw materials total
    rawMaterials.forEach((material, index) => {
        const total = calculateRawMaterialTotal(material);
        totals.rawMaterials += total;
    });

    // Calculate expenses total
    expenses.forEach((expense, index) => {
        const amount = parseFloatSafe(expense.amount);
        totals.expenses += amount;
    });

    // Calculate manufacturing cost (raw materials + expenses)
    totals.manufacturing = totals.rawMaterials + totals.expenses;

    return totals;
}

// Distribute costs by percentage
export function distributeCostsByPercentage(state) {
    const products = state.products || [];
    const totals = calculateAllTotals(state);
    const totalManufacturingCost = totals.manufacturing;

    const totalPercentage = products.reduce(
        (sum, p) => sum + parseFloatSafe(p.cost_percentage),
        0
    );

    // If no percentages set, distribute equally
    if (totalPercentage === 0) {
        console.log("⚠️ No percentages set, distributing equally");
        distributeCostsEqually(state);
        return;
    }

    state.products = products.map((product) => {
        const percentage = parseFloatSafe(product.cost_percentage);
        const allocatedCost = (percentage / 100) * totalManufacturingCost;
        const quantity = parseFloatSafe(product.quantity);
        const unitCost = quantity > 0 ? allocatedCost / quantity : 0;

        console.log("📦 Product cost distribution:", {
            name: product.name,
            percentage,
            allocatedCost,
            quantity,
            unitCost,
        });

        return {
            ...product,
            average_cost: unitCost,
            unit_cost: unitCost,
            total_cost: allocatedCost,
        };
    });
}

// Distribute costs equally
export function distributeCostsEqually(state) {
    const products = state.products || [];
    const totals = calculateAllTotals(state);
    const totalManufacturingCost = totals.manufacturing;

    if (products.length === 0) {
        return;
    }

    const costPerProduct = totalManufacturingCost / products.length;
    const percentagePerProduct = 100 / products.length;

    state.products = products.map((product) => {
        const quantity = parseFloatSafe(product.quantity);
        const unitCost = quantity > 0 ? costPerProduct / quantity : 0;

        return {
            ...product,
            cost_percentage: percentagePerProduct,
            average_cost: unitCost,
            unit_cost: unitCost,
            total_cost: costPerProduct,
        };
    });

}

// Update totals in DOM
export function updateTotalsDisplay(state) {
    const totals = calculateAllTotals(state);

    // Helper to format currency
    const formatCurrency = (value) =>
        formatNumber(value) + " " + (window.__("EGP") || "EGP");

    // Update raw materials cost
    const rawMaterialsEl = document.getElementById("summary-raw-materials");
    if (rawMaterialsEl) {
        rawMaterialsEl.textContent = formatCurrency(totals.rawMaterials);
    }

    // Update expenses
    const expensesEl = document.getElementById("summary-expenses");
    if (expensesEl) {
        expensesEl.textContent = formatCurrency(totals.expenses);
    }

    // Update invoice cost (raw materials + expenses)
    const invoiceCostEl = document.getElementById("summary-invoice-cost");
    if (invoiceCostEl) {
        invoiceCostEl.textContent = formatCurrency(totals.manufacturing);
    }

    // Update products cost
    const productsEl = document.getElementById("summary-products");
    if (productsEl) {
        productsEl.textContent = formatCurrency(totals.products);
    }

    // Update standard cost (same as manufacturing for now)
    const standardCostEl = document.getElementById("summary-standard-cost");
    if (standardCostEl) {
        standardCostEl.textContent = formatCurrency(totals.manufacturing);
    }

    // Calculate and update variance
    const variance = totals.products - totals.manufacturing;
    const varianceAmountEl = document.getElementById("summary-variance-amount");
    const variancePercentageEl = document.getElementById(
        "summary-variance-percentage"
    );

    if (varianceAmountEl) {
        varianceAmountEl.textContent = formatCurrency(Math.abs(variance));

        // Update color based on positive/negative
        if (variance >= 0) {
            varianceAmountEl.classList.remove("text-red-600");
            varianceAmountEl.classList.add("text-emerald-600");
        } else {
            varianceAmountEl.classList.remove("text-emerald-600");
            varianceAmountEl.classList.add("text-red-600");
        }
    }

    if (variancePercentageEl) {
        const percentage =
            totals.manufacturing > 0
                ? ((Math.abs(variance) / totals.manufacturing) * 100).toFixed(2)
                : 0;
        variancePercentageEl.textContent = percentage + "%";

        // Update badge color
        if (variance >= 0) {
            variancePercentageEl.classList.remove("bg-red-100", "text-red-700");
            variancePercentageEl.classList.add(
                "bg-emerald-100",
                "text-emerald-700"
            );
        } else {
            variancePercentageEl.classList.remove(
                "bg-emerald-100",
                "text-emerald-700"
            );
            variancePercentageEl.classList.add("bg-red-100", "text-red-700");
        }
    }

    // Show/hide distribution note
    const distributionNote = document.getElementById("distribution-note");
    const distributionTotal = document.getElementById("distribution-total");

    if (distributionNote && distributionTotal) {
        if (totals.products > 0) {
            distributionNote.classList.remove("hidden");
            distributionTotal.textContent = formatNumber(totals.manufacturing);
        } else {
            distributionNote.classList.add("hidden");
        }
    }

    // Update save button state based on variance validation
    updateSaveButtonState(variance);

    // Show/hide variance warning
    updateVarianceWarning(variance);
}

// Update save button state based on variance
function updateSaveButtonState(variance) {
    const saveBtn = document.getElementById("btn-save-invoice");
    if (!saveBtn) return;

    if (variance > 0) {
        // Disable save button when variance is positive
        saveBtn.disabled = true;
        saveBtn.classList.add("opacity-50", "cursor-not-allowed");
        saveBtn.classList.remove("hover:bg-accent");
        saveBtn.title = "لا يمكن الحفظ: قيمة المنتجات أكبر من تكلفة التصنيع";
    } else {
        // Enable save button when variance is valid
        saveBtn.disabled = false;
        saveBtn.classList.remove("opacity-50", "cursor-not-allowed");
        saveBtn.classList.add("hover:bg-accent");
        saveBtn.title = window.__("Save Invoice") || "حفظ الفاتورة";
    }
}

// Update variance warning visibility
function updateVarianceWarning(variance) {
    const warningEl = document.getElementById("variance-warning");
    if (!warningEl) return;

    if (variance > 0) {
        warningEl.classList.remove("hidden");
    } else {
        warningEl.classList.add("hidden");
    }
}
