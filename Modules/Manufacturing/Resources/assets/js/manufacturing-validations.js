/**
 * Manufacturing Invoice Validations - Real-time validation
 */

import { showToast } from "./manufacturing-utilities.js";

// Helpers
const __ = (key, replacements = {}) => {
    let text = window.__(key) || key;
    Object.keys(replacements).forEach(k => text = text.replace(`:${k}`, replacements[k]));
    return text;
};

const fetchAPI = async (endpoint, params = {}) => {
    const query = new URLSearchParams(params).toString();
    const response = await fetch(`/manufacturing/api/${endpoint}?${query}`);
    return response.json();
};

const getBaseQuantity = (quantity, unitId, units) => {
    if (!unitId || !units?.length) return quantity;
    const unit = units.find(u => u.id == unitId);
    return unit ? quantity * (unit.u_val || 1) : quantity;
};

const showErrors = (errors, type = 'error') => {
    if (errors.length) {
        errors.forEach(e => showToast(e, type));
        return false;
    }
    return true;
};

// Phase 1: Critical Validations
export async function validateDuplicateInvoice(proId, branchId) {
    try {
        const data = await fetchAPI('check-duplicate', { pro_id: proId, branch_id: branchId });
        if (data.exists) {
            showToast(__('manufacturing.duplicate_invoice_number', 'رقم الفاتورة موجود بالفعل'), 'error');
            return false;
        }
    } catch (error) {
        console.error('Error checking duplicate:', error);
    }
    return true;
}

export async function validateStockAvailability(rawMaterials, storeId) {
    const errors = [];
    for (const material of rawMaterials) {
        if (!material.id || material.quantity <= 0) continue;
        try {
            const baseQty = getBaseQuantity(material.quantity, material.unit_id, material.units);
            const data = await fetchAPI('get-available-stock', { item_id: material.id, store_id: storeId });
            const available = parseFloat(data.available_stock || 0);
            if (available < baseQty) {
                errors.push(__('manufacturing.insufficient_stock', {
                    item: material.name,
                    available: available.toFixed(2),
                    required: baseQty.toFixed(2)
                }));
            }
        } catch (error) {
            console.error(`Stock check error for ${material.id}:`, error);
        }
    }
    return showErrors(errors);
}

export function validateAccountsExist(acc1, acc2) {
    const errors = [];
    if (!acc1) errors.push(__('manufacturing.products_account_required', 'حساب المنتجات مطلوب'));
    if (!acc2) errors.push(__('manufacturing.raw_materials_account_required', 'حساب الخامات مطلوب'));
    return showErrors(errors);
}

export function validateNonZeroCosts(products, rawMaterials, expenses) {
    const errors = [];
    products.forEach(p => {
        if (p.quantity <= 0) errors.push(`${p.name}: ${__('manufacturing.product_quantity_must_be_positive', 'الكمية يجب أن تكون أكبر من صفر')}`);
        if (p.unit_cost < 0) errors.push(`${p.name}: ${__('manufacturing.product_cost_cannot_be_negative', 'التكلفة لا يمكن أن تكون سالبة')}`);
    });
    rawMaterials.forEach(m => {
        if (m.quantity <= 0) errors.push(`${m.name}: ${__('manufacturing.raw_material_quantity_must_be_positive', 'الكمية يجب أن تكون أكبر من صفر')}`);
        if (m.unit_cost < 0) errors.push(`${m.name}: ${__('manufacturing.raw_material_cost_cannot_be_negative', 'التكلفة لا يمكن أن تكون سالبة')}`);
    });
    const totalCost = rawMaterials.reduce((s, m) => s + (m.total_cost || 0), 0) + 
                      expenses.reduce((s, e) => s + (e.amount || 0), 0);
    if (totalCost <= 0) errors.push(__('manufacturing.total_manufacturing_cost_must_be_positive', 'إجمالي التكلفة يجب أن يكون أكبر من صفر'));
    return showErrors(errors);
}

// Phase 2: Important Validations
export async function validateMOQuantity(orderId, stageId, products) {
    if (!orderId) return true;
    try {
        const data = await fetchAPI('check-mo-quantity', { order_id: orderId, stage_id: stageId || '' });
        if (!data.success) {
            showToast(data.message || 'فشل التحقق من كمية أمر التصنيع', 'error');
            return false;
        }
        const targetProduct = products.find(p => p.id === data.target_item_id);
        if (targetProduct && targetProduct.quantity > data.remaining_quantity) {
            showToast(__('manufacturing.production_exceeds_mo_quantity', {
                production: targetProduct.quantity,
                remaining: data.remaining_quantity
            }), 'error');
            return false;
        }
    } catch (error) {
        console.error('MO quantity check error:', error);
    }
    return true;
}

export async function validateBOMExists(products) {
    for (const product of products) {
        try {
            const data = await fetchAPI('check-bom', { item_id: product.id });
            if (!data.has_bom) {
                showToast(__('manufacturing.no_bom_found', { item: product.name }), 'warning');
            } else if (!data.is_active) {
                showToast(__('manufacturing.bom_not_active', { item: product.name }), 'warning');
            }
        } catch (error) {
            console.error(`BOM check error for ${product.id}:`, error);
        }
    }
    return true;
}

export function validateUnitConversions(products, rawMaterials) {
    const errors = [];
    [...products, ...rawMaterials].forEach(item => {
        if (item.unit_id && item.units?.length) {
            const unit = item.units.find(u => u.id == item.unit_id);
            if (!unit) {
                errors.push(`${item.name}: ${__('manufacturing.invalid_unit', 'الوحدة غير صالحة')}`);
            } else {
                const factor = parseFloat(unit.u_val || 1);
                if (factor <= 0 || isNaN(factor)) {
                    errors.push(`${item.name}: ${__('manufacturing.invalid_unit_conversion', 'معامل التحويل غير صالح')}`);
                }
            }
        }
    });
    return showErrors(errors);
}

export async function validateAccountTypes(acc1, acc2, operatingAccount) {
    try {
        const data = await fetchAPI('validate-accounts', { acc1, acc2, operating: operatingAccount || '' });
        if (!data.valid) {
            showToast(data.message || 'الحسابات غير صالحة', 'error');
            return false;
        }
        if (!data.acc1_is_inventory) {
            showToast(__('manufacturing.products_account_not_inventory', 'حساب المنتجات يجب أن يكون حساب مخزون'), 'error');
            return false;
        }
        if (!data.acc2_is_inventory) {
            showToast(__('manufacturing.raw_materials_account_not_inventory', 'حساب الخامات يجب أن يكون حساب مخزون'), 'error');
            return false;
        }
    } catch (error) {
        console.error('Account validation error:', error);
    }
    return true;
}

// Phase 3: Enhancement Validations
export async function validateConsumptionTolerance(products, rawMaterials) {
    const warnings = [], errors = [];
    for (const product of products) {
        try {
            const data = await fetchAPI('get-bom', { item_id: product.id });
            if (!data.has_bom || !data.bom_items) continue;
            const prodQty = parseFloat(product.quantity || 0);
            for (const bomItem of data.bom_items) {
                const expected = parseFloat(bomItem.quantity || 0) * prodQty;
                const actualMaterial = rawMaterials.find(m => m.id === bomItem.item_id);
                if (!actualMaterial) {
                    warnings.push(__('manufacturing.bom_item_missing', { item: bomItem.item_name, product: product.name }));
                    continue;
                }
                const actual = getBaseQuantity(actualMaterial.quantity, actualMaterial.unit_id, actualMaterial.units);
                const variance = Math.abs(actual - expected);
                const variancePct = expected > 0 ? (variance / expected) * 100 : 0;
                const tolerance = 10;
                if (variancePct > tolerance) {
                    const msg = __('manufacturing.consumption_exceeds_tolerance', {
                        item: actualMaterial.name,
                        expected: expected.toFixed(2),
                        actual: actual.toFixed(2),
                        variance: variancePct.toFixed(2)
                    });
                    (variancePct > tolerance * 2 ? errors : warnings).push(msg);
                }
            }
        } catch (error) {
            console.error(`BOM tolerance check error for ${product.id}:`, error);
        }
    }
    showErrors(warnings, 'warning');
    return showErrors(errors);
}

export async function validateAccountingPeriod(invoiceDate) {
    try {
        const data = await fetchAPI('check-accounting-period', { date: invoiceDate });
        if (!data.is_open) {
            showToast(__('manufacturing.accounting_period_closed', { date: invoiceDate }), 'error');
            return false;
        }
    } catch (error) {
        console.error('Accounting period check error:', error);
    }
    return true;
}

// Main validation function
export async function validateBeforeSave(state) {
    console.log('🔍 Starting validation...');
    
    // Basic checks
    if (!state.products?.length) {
        showToast(__('manufacturing.products_required', 'يجب إضافة منتج واحد على الأقل'), 'error');
        return false;
    }
    if (!state.rawMaterials?.length) {
        showToast(__('manufacturing.raw_materials_required', 'يجب إضافة خامة واحدة على الأقل'), 'error');
        return false;
    }
    
    // Get form values
    const proId = document.getElementById('display-invoice-number')?.value;
    const acc1 = document.getElementById('product-account')?.value;
    const acc2 = document.getElementById('raw-material-account')?.value;
    const operatingAccount = document.getElementById('operating-account')?.value;
    const branchId = document.getElementById('branch-id')?.value || window.auth?.user?.current_branch_id;
    const invoiceDate = document.getElementById('display-invoice-date')?.value;
    const { orderId, stageId, invoiceId } = state.config;
    
    // Phase 1: Critical
    if (!validateAccountsExist(acc1, acc2)) return false;
    if (!validateNonZeroCosts(state.products, state.rawMaterials, state.expenses)) return false;
    if (proId && branchId && !invoiceId && !await validateDuplicateInvoice(proId, branchId)) return false;
    if (!await validateStockAvailability(state.rawMaterials, acc2)) return false;
    
    // Phase 2: Important
    if (!validateUnitConversions(state.products, state.rawMaterials)) return false;
    if (!await validateAccountTypes(acc1, acc2, operatingAccount)) return false;
    if (orderId && !await validateMOQuantity(orderId, stageId, state.products)) return false;
    await validateBOMExists(state.products);
    
    // Phase 3: Enhancements
    if (invoiceDate && !await validateAccountingPeriod(invoiceDate)) return false;
    if (!await validateConsumptionTolerance(state.products, state.rawMaterials)) {
        if (!confirm(__('manufacturing.tolerance_exceeded_confirm', 'تم تجاوز حدود التسامح. هل تريد المتابعة؟'))) {
            return false;
        }
    }
    
    console.log('✅ All validations passed');
    return true;
}

// Setup real-time validation
export function setupRealtimeValidation(state) {
    const invoiceNumberInput = document.getElementById('display-invoice-number');
    if (invoiceNumberInput) {
        let timer;
        invoiceNumberInput.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(async () => {
                const branchId = document.getElementById('branch-id')?.value || window.auth?.user?.current_branch_id;
                if (this.value && branchId && !state.config.invoiceId) {
                    await validateDuplicateInvoice(this.value, branchId);
                }
            }, 500);
        });
    }
}

export async function validateRawMaterialStock(material, storeId) {
    if (!material.id || material.quantity <= 0) return true;
    try {
        const baseQty = getBaseQuantity(material.quantity, material.unit_id, material.units);
        const data = await fetchAPI('get-available-stock', { item_id: material.id, store_id: storeId });
        const available = parseFloat(data.available_stock || 0);
        if (available < baseQty) {
            showToast(__('manufacturing.insufficient_stock', {
                item: material.name,
                available: available.toFixed(2),
                required: baseQty.toFixed(2)
            }), 'warning');
            return false;
        }
    } catch (error) {
        console.error('Stock check error:', error);
    }
    return true;
}
