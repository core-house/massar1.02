/**
 * Manufacturing Updates
 * Handle form updates and data synchronization
 */

import { parseFloatSafe } from "./manufacturing-utilities.js";
import {
    calculateProductTotal,
    calculateRawMaterialTotal,
} from "./manufacturing-calculations.js";

// Update product field
export function updateProductField(products, index, field, value) {
    if (!products[index]) return products;

    const product = products[index];

    // Parse value based on field type
    if (
        field === "quantity" ||
        field === "unit_cost" ||
        field === "cost_percentage"
    ) {
        product[field] = parseFloat(value) || 0;
    } else if (field === "unit_id") {
        const oldUnitId = product[field];
        product[field] = parseInt(value) || null;

        // Update unit cost when unit changes
        if (
            oldUnitId !== product.unit_id &&
            product.units &&
            product.units.length > 0
        ) {
            const selectedUnit = product.units.find(
                (u) => u.id == product.unit_id
            );
            const oldUnit = product.units.find((u) => u.id == oldUnitId);
            if (selectedUnit) {
                // Calculate base unit cost
                let baseUnitCost = 0;

                // Priority 1: Use average_cost if available and valid
                if (product.average_cost !== undefined && product.average_cost !== null && product.average_cost > 0) {
                    baseUnitCost = parseFloat(product.average_cost);
                }
                // Priority 2: Calculate from current unit_cost and old unit factor
                else if (
                    oldUnit &&
                    oldUnit.u_val &&
                    product.unit_cost &&
                    product.unit_cost > 0
                ) {
                    baseUnitCost = product.unit_cost / parseFloat(oldUnit.u_val);
                }
                // Priority 3: Fallback
                else {
                    baseUnitCost = parseFloat(product.unit_cost || 0);
                }

                const unitFactor = parseFloat(selectedUnit.pivot ? selectedUnit.pivot.u_val : (selectedUnit.u_val || 1));

                // Calculate new unit cost: base cost × unit factor
                const newUnitCost = baseUnitCost * unitFactor;
                product.unit_cost = newUnitCost;

                // Ensure average_cost is stored correctly
                product.average_cost = baseUnitCost;
            }
        }
    } else {
        product[field] = value;
    }

    // Recalculate total if quantity or unit_cost changed
    if (field === "quantity" || field === "unit_cost" || field === "unit_id") {
        product.total_cost = calculateProductTotal(product);
        // Force refresh all totals and sync
        if (typeof window.ManufacturingApp !== 'undefined') {
            window.ManufacturingApp.state.products[index] = product;
            window.ManufacturingApp.calculateAllTotals(window.ManufacturingApp.state);
            window.ManufacturingApp.syncFormInputs(window.ManufacturingApp.state);
        }
    }

    // Recalculate unit_cost if cost_percentage changed
    if (field === "cost_percentage") {
        // This will be handled by distribute costs function
    }

    return [...products];
}

// Update raw material field
export function updateRawMaterialField(materials, index, field, value) {
    if (!materials[index]) {
        return materials;
    }

    const material = materials[index];

    // Parse value based on field type
    if (field === "quantity") {
        material[field] = parseFloat(value) || 0;
    } else if (field === "unit_id") {
        const oldUnitId = material[field];
        material[field] = parseInt(value) || null;

        // Update unit cost when unit changes
        if (
            oldUnitId !== material.unit_id &&
            material.units &&
            material.units.length > 0
        ) {
            const selectedUnit = material.units.find(
                (u) => u.id == material.unit_id
            );
            const oldUnit = material.units.find((u) => u.id == oldUnitId);

            if (selectedUnit) {
                // Calculate base unit cost
                let baseUnitCost = 0;

                // Priority 1: Use average_cost if available and valid
                if (material.average_cost !== undefined && material.average_cost !== null && material.average_cost > 0) {
                    baseUnitCost = parseFloat(material.average_cost);
                }
                // Priority 2: Calculate from current unit_cost and old unit factor
                else if (
                    oldUnit &&
                    oldUnit.u_val &&
                    material.unit_cost &&
                    material.unit_cost > 0
                ) {
                    baseUnitCost = material.unit_cost / parseFloat(oldUnit.u_val);
                }
                // Priority 3: Fallback
                else {
                    baseUnitCost = parseFloat(material.unit_cost || 0);
                }

                const unitFactor = parseFloat(selectedUnit.pivot ? selectedUnit.pivot.u_val : (selectedUnit.u_val || 1));

                // Calculate new unit cost: base cost × unit factor
                const newUnitCost = baseUnitCost * unitFactor;
                material.unit_cost = newUnitCost;

                // Ensure average_cost is stored correctly
                material.average_cost = baseUnitCost;
            }
        }
    } else {
        material[field] = value;
    }

    // Recalculate total if quantity or unit_cost changed
    if (field === "quantity" || field === "unit_cost" || field === "unit_id") {
        material.total_cost = calculateRawMaterialTotal(material);
        // Force refresh all totals and sync
        if (typeof window.ManufacturingApp !== 'undefined') {
            window.ManufacturingApp.state.rawMaterials[index] = material;
            window.ManufacturingApp.calculateAllTotals(window.ManufacturingApp.state);
            window.ManufacturingApp.syncFormInputs(window.ManufacturingApp.state);
        }
    }

    return [...materials];
}

// Update expense field
export function updateExpenseField(expenses, index, field, value) {
    if (!expenses[index]) return expenses;

    expenses[index][field] = value;

    // Force refresh all totals and sync
    if (typeof window.ManufacturingApp !== 'undefined') {
        window.ManufacturingApp.state.expenses[index] = expenses[index];
        window.ManufacturingApp.calculateAllTotals(window.ManufacturingApp.state);
        window.ManufacturingApp.syncFormInputs(window.ManufacturingApp.state);
    }

    return [...expenses];
}

// Add product
export function addProduct(products, productData, rawMaterials = []) {
    // Validation: Check if item already exists in raw materials
    const existsInRawMaterials = rawMaterials.some(material => material.id === productData.id);
    if (existsInRawMaterials) {
        throw new Error(window.__('manufacturing.item_exists_in_raw_materials') || 'هذا الصنف موجود بالفعل في الخامات');
    }

    // Validation: Check if item already exists in products
    const existsInProducts = products.some(product => product.id === productData.id);
    if (existsInProducts) {
        throw new Error(window.__('manufacturing.item_already_added') || 'هذا الصنف مضاف بالفعل');
    }

    const defaultUnit =
        productData.units && productData.units.length > 0
            ? productData.units[0]
            : null;

    const baseUnitCost = productData.average_cost || 0;
    const unitFactor = defaultUnit ? (defaultUnit.u_val || 1) : 1;
    const displayUnitCost = baseUnitCost * unitFactor;

    return [
        ...products,
        {
            id: productData.id,
            name: productData.name,
            quantity: 1,
            unit_id: defaultUnit ? defaultUnit.id : null,
            unit_name: defaultUnit ? defaultUnit.name : '',
            unit_cost: displayUnitCost,
            average_cost: baseUnitCost,
            cost_percentage: 0,
            total_cost: displayUnitCost,
            units: productData.units || [],
            unitsList: productData.units || [],
        },
    ];
}

// Remove product
export function removeProduct(products, index) {
    return products.filter((_, i) => i !== index);
}

// Add raw material
export function addRawMaterial(materials, materialData, products = []) {
    // Validation: Check if item already exists in products
    const existsInProducts = products.some(product => product.id === materialData.id);
    if (existsInProducts) {
        throw new Error(window.__('manufacturing.item_exists_in_products') || 'هذا الصنف موجود بالفعل في المنتجات');
    }

    // Validation: Check if item already exists in raw materials
    const existsInRawMaterials = materials.some(material => material.id === materialData.id);
    if (existsInRawMaterials) {
        throw new Error(window.__('manufacturing.item_already_added') || 'هذا الصنف مضاف بالفعل');
    }

    const defaultUnit =
        materialData.units && materialData.units.length > 0
            ? materialData.units[0]
            : null;

    const baseUnitCost = materialData.average_cost || 0;
    const unitFactor = defaultUnit ? (defaultUnit.u_val || 1) : 1;
    const displayUnitCost = baseUnitCost * unitFactor;

    return [
        ...materials,
        {
            id: materialData.id,
            name: materialData.name,
            quantity: 1,
            unit_id: defaultUnit ? defaultUnit.id : null,
            unit_name: defaultUnit ? defaultUnit.name : '',
            unit_cost: displayUnitCost,
            average_cost: baseUnitCost,
            total_cost: displayUnitCost,
            available_stock: 0,
            units: materialData.units || [],
            unitsList: materialData.units || [],
        },
    ];
}

// Remove raw material
export function removeRawMaterial(materials, index) {
    return materials.filter((_, i) => i !== index);
}

// Add expense
export function addExpense(expenses, defaultAccountId) {
    return [
        ...expenses,
        {
            amount: 0,
            account_id: defaultAccountId,
            description: "",
        },
    ];
}

// Remove expense
export function removeExpense(expenses, index) {
    return expenses.filter((_, i) => i !== index);
}

// Sync form hidden inputs
export function syncFormInputs(formData) {
    // Sync basic fields
    const fields = [
        "pro-id",
        "invoice-date",
        "branch-id",
        "employee-id",
        "raw-account",
        "product-account",
        "operating-account",
        "description",
        "patch-number",
    ];

    fields.forEach((fieldId) => {
        const element = document.getElementById(fieldId);
        const formElement = document.getElementById(`form-${fieldId}`);
        if (element && formElement) {
            formElement.value = element.value;
        }
    });

    // Sync products
    syncProductsToForm(formData.products);

    // Sync raw materials
    syncRawMaterialsToForm(formData.rawMaterials);

    // Sync expenses
    syncExpensesToForm(formData.expenses);
}

// Sync products to hidden form inputs
function syncProductsToForm(products) {
    const container = document.getElementById("form-products-container");
    if (!container) return;

    container.innerHTML = products
        .map(
            (product, index) => `
        <input type="hidden" name="products[${index}][id]" value="${
                product.id
            }">
        <input type="hidden" name="products[${index}][quantity]" value="${
                product.quantity
            }">
        <input type="hidden" name="products[${index}][unit_id]" value="${
                product.unit_id || ""
            }">
        <input type="hidden" name="products[${index}][unit_cost]" value="${
                product.unit_cost || product.average_cost || 0
            }">
        <input type="hidden" name="products[${index}][total_cost]" value="${
                product.total_cost || 0
            }">
        <input type="hidden" name="products[${index}][cost_percentage]" value="${
                product.cost_percentage || 0
            }">
    `
        )
        .join("");
}

// Sync raw materials to hidden form inputs
function syncRawMaterialsToForm(materials) {
    const container = document.getElementById("form-raw-materials-container");
    if (!container) return;

    container.innerHTML = materials
        .map(
            (material, index) => `
        <input type="hidden" name="raw_materials[${index}][id]" value="${material.id}">
        <input type="hidden" name="raw_materials[${index}][quantity]" value="${material.quantity}">
        <input type="hidden" name="raw_materials[${index}][unit_id]" value="${material.unit_id || ""}">
        <input type="hidden" name="raw_materials[${index}][unit_cost]" value="${material.unit_cost || material.average_cost || 0}">
        <input type="hidden" name="raw_materials[${index}][total_cost]" value="${material.total_cost || 0}">
    `
        )
        .join("");
}

// Sync expenses to hidden form inputs
function syncExpensesToForm(expenses) {
    const container = document.getElementById("form-expenses-container");
    if (!container) return;

    container.innerHTML = expenses
        .map(
            (expense, index) => `
        <input type="hidden" name="expenses[${index}][amount]" value="${expense.amount}">
        <input type="hidden" name="expenses[${index}][account_id]" value="${expense.account_id}">
        <input type="hidden" name="expenses[${index}][description]" value="${expense.description}">
    `
        )
        .join("");
}
