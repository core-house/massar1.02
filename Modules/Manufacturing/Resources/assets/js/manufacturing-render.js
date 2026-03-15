/**
 * Manufacturing Render
 * DOM manipulation and rendering functions - Tailwind CSS
 */

import { formatNumber } from './manufacturing-utilities.js';

// Helper to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper to format currency
function formatCurrency(value) {
    return formatNumber(value) + ' ' + (window.__('EGP') || 'EGP');
}

// Render products table
export function renderProductsTable(products, callbacks = {}) {
    const tbody = document.getElementById('products-table-body');
    const emptyState = document.getElementById('products-empty-state');
    const tableContainer = document.getElementById('products-table-container');
    
    if (!tbody) return;

    if (products.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (tableContainer) tableContainer.classList.add('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    if (tableContainer) tableContainer.classList.remove('hidden');

    tbody.innerHTML = products.map((product, index) => {
        const unitsList = product.unitsList || product.units || [];
        console.log(`🔍 Product ${index} units:`, {
            name: product.name,
            unitsList: unitsList,
            unitsCount: unitsList.length
        });
        
        return `
        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.ManufacturingApp.showItemDetails(${index}, 'product')">
            <td class="px-2 py-1.5">
                <input type="text" value="${escapeHtml(product.name || '')}" 
                    class="w-full px-2 py-1 text-xs bg-gray-50 border-0 rounded" readonly>
            </td>
            <td class="px-2 py-1.5">
                <select 
                    class="product-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary" 
                    data-index="${index}" 
                    data-field="unit_id">
                    ${unitsList.length > 0 ? unitsList.map(unit => `
                        <option value="${unit.id}" ${unit.id == product.unit_id ? 'selected' : ''}>
                            ${unit.name} (${formatNumber(unit.u_val || 1)} ${window.__('pieces') || 'قطعة'})
                        </option>
                    `).join('') : '<option value="">لا توجد وحدات</option>'}
                </select>
            </td>
            <td class="px-2 py-1.5">
                <input type="number" 
                    class="product-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center" 
                    data-index="${index}" 
                    data-field="quantity" 
                    value="${product.quantity || 0}" 
                    min="0.01" 
                    step="0.01" 
                    placeholder="${window.__('Quantity') || 'Quantity'}">
            </td>
            <td class="px-2 py-1.5">
                <input type="number" 
                    class="product-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center" 
                    data-index="${index}" 
                    data-field="unit_cost" 
                    value="${product.unit_cost || product.average_cost || 0}" 
                    min="0" 
                    step="0.01" 
                    placeholder="${window.__('Unit Cost') || 'Unit Cost'}">
            </td>
            <td class="px-2 py-1.5">
                <input type="number" 
                    class="product-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center" 
                    data-index="${index}" 
                    data-field="cost_percentage" 
                    value="${product.cost_percentage || 0}" 
                    min="0" 
                    max="100" 
                    step="0.01" 
                    placeholder="${window.__('Cost Percentage') || '%'}">
            </td>
            <td class="px-2 py-1.5">
                <input type="text" 
                    value="${formatCurrency(product.total_cost || 0)}" 
                    class="w-full px-2 py-1 text-xs bg-emerald-50 border-0 rounded font-bold text-emerald-600 text-center" 
                    readonly>
            </td>
            <td class="px-2 py-1.5 text-center">
                <button type="button" class="remove-product-btn text-red-500 hover:text-red-700 transition-colors" 
                    data-index="${index}" 
                    title="${window.__('Delete') || 'Delete'}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" 
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </button>
            </td>
        </tr>
    `}).join('');
    
    // Attach event listeners for inputs
    if (callbacks.onUpdate) {
        tbody.querySelectorAll('.product-input').forEach(input => {
            console.log('🔗 Attaching listener to product input:', {
                type: input.type,
                field: input.dataset.field,
                index: input.dataset.index,
                value: input.value
            });
            
            input.addEventListener('input', (e) => {
                const index = parseInt(e.target.dataset.index);
                const field = e.target.dataset.field;
                const value = e.target.value;
                console.log('⌨️ Product input event:', { index, field, value });
                callbacks.onUpdate(index, field, value);
            });
            
            input.addEventListener('change', (e) => {
                const index = parseInt(e.target.dataset.index);
                const field = e.target.dataset.field;
                const value = e.target.value;
                console.log('🔄 Product change event:', { index, field, value });
                callbacks.onUpdate(index, field, value);
            });
        });
    }
    
    // Attach event listeners for remove buttons
    if (callbacks.onRemove) {
        const removeButtons = tbody.querySelectorAll('.remove-product-btn');
        console.log(`🗑️ Attaching ${removeButtons.length} remove button listeners for products`);
        
        removeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const index = parseInt(e.currentTarget.dataset.index);
                console.log('🗑️ Remove product button clicked, index:', index);
                callbacks.onRemove(index);
            });
        });
    } else {
        console.warn('⚠️ No onRemove callback provided for products');
    }
}

// Render raw materials table
export function renderRawMaterialsTable(materials, callbacks = {}) {
    const tbody = document.getElementById('raw-materials-table-body');
    const emptyState = document.getElementById('raw-materials-empty-state');
    const tableContainer = document.getElementById('raw-materials-table-container');
    
    if (!tbody) return;

    if (materials.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (tableContainer) tableContainer.classList.add('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    if (tableContainer) tableContainer.classList.remove('hidden');

    tbody.innerHTML = materials.map((material, index) => {
        const unitsList = material.unitsList || material.units || [];
        console.log(`🔍 Rendering material ${index}:`, {
            name: material.name,
            unit_id: material.unit_id,
            unit_cost: material.unit_cost,
            unitsList: unitsList,
            unitsCount: unitsList.length
        });
        
        return `
        <tr class="hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.ManufacturingApp.showItemDetails(${index}, 'material')">
            <td class="px-2 py-1.5">
                <input type="text" value="${escapeHtml(material.name || '')}" 
                    class="w-full px-2 py-1 text-xs bg-gray-50 border-0 rounded" readonly>
            </td>
            <td class="px-2 py-1.5">
                <select 
                    class="material-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary" 
                    data-index="${index}" 
                    data-field="unit_id">
                    ${unitsList.length > 0 ? unitsList.map(unit => `
                        <option value="${unit.id}" ${unit.id == material.unit_id ? 'selected' : ''}>
                            ${unit.name} (${formatNumber(unit.u_val || 1)} ${window.__('pieces') || 'قطعة'})
                        </option>
                    `).join('') : '<option value="">لا توجد وحدات</option>'}
                </select>
            </td>
            <td class="px-2 py-1.5">
                <input type="number" 
                    class="material-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center" 
                    data-index="${index}" 
                    data-field="quantity" 
                    value="${material.quantity || 0}" 
                    min="0.01" 
                    step="0.01" 
                    placeholder="${window.__('Quantity') || 'Quantity'}">
            </td>
            <td class="px-2 py-1.5">
                <input type="text" 
                    value="${formatNumber(material.unit_cost || 0)}" 
                    class="material-unit-cost w-full px-2 py-1 text-xs bg-gray-50 border-0 rounded text-center font-medium" 
                    data-index="${index}"
                    readonly>
            </td>
            <td class="px-2 py-1.5">
                <input type="text" 
                    value="${formatCurrency(material.total_cost || 0)}" 
                    class="w-full px-2 py-1 text-xs bg-amber-50 border-0 rounded font-bold text-amber-600 text-center" 
                    readonly>
            </td>
            <td class="px-2 py-1.5 text-center">
                <button type="button" class="remove-material-btn text-red-500 hover:text-red-700 transition-colors" 
                    data-index="${index}" 
                    title="${window.__('Delete') || 'Delete'}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" 
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </button>
            </td>
        </tr>
    `}).join('');
    
    // Attach event listeners for inputs
    if (callbacks.onUpdate) {
        tbody.querySelectorAll('.material-input').forEach(input => {
            console.log('🔗 Attaching listener to material input:', {
                type: input.type,
                field: input.dataset.field,
                index: input.dataset.index,
                value: input.value
            });
            
            input.addEventListener('input', (e) => {
                const index = parseInt(e.target.dataset.index);
                const field = e.target.dataset.field;
                const value = e.target.value;
                console.log('⌨️ Material input event:', { index, field, value });
                callbacks.onUpdate(index, field, value);
            });
            
            input.addEventListener('change', (e) => {
                const index = parseInt(e.target.dataset.index);
                const field = e.target.dataset.field;
                const value = e.target.value;
                console.log('🔄 Material change event:', { index, field, value });
                callbacks.onUpdate(index, field, value);
            });
        });
    }
    
    // Attach event listeners for remove buttons
    if (callbacks.onRemove) {
        const removeButtons = tbody.querySelectorAll('.remove-material-btn');
        console.log(`🗑️ Attaching ${removeButtons.length} remove button listeners for materials`);
        
        removeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const index = parseInt(e.currentTarget.dataset.index);
                console.log('🗑️ Remove material button clicked, index:', index);
                callbacks.onRemove(index);
            });
        });
    } else {
        console.warn('⚠️ No onRemove callback provided for materials');
    }
}

// Render expenses table
export function renderExpensesTable(expenses, expenseAccounts, callbacks = {}) {
    const tbody = document.getElementById('expenses-table-body');
    const emptyState = document.getElementById('expenses-empty-state');
    const tableContainer = document.getElementById('expenses-table-container');
    
    if (!tbody) return;

    if (expenses.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        if (tableContainer) tableContainer.classList.add('hidden');
        return;
    }

    if (emptyState) emptyState.classList.add('hidden');
    if (tableContainer) tableContainer.classList.remove('hidden');

    tbody.innerHTML = expenses.map((expense, index) => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-2 py-1.5">
                <input type="number" 
                    class="expense-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary text-center" 
                    data-index="${index}" 
                    data-field="amount" 
                    value="${expense.amount || 0}" 
                    min="0" 
                    step="0.01" 
                    placeholder="0.00">
            </td>
            <td class="px-2 py-1.5">
                <select 
                    class="expense-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary" 
                    data-index="${index}" 
                    data-field="account_id">
                    ${Object.entries(expenseAccounts).map(([id, account]) => {
                        const accountId = typeof account === 'object' ? account.id : id;
                        const accountName = typeof account === 'object' ? account.aname : account;
                        return `<option value="${accountId}" ${accountId == expense.account_id ? 'selected' : ''}>${accountName}</option>`;
                    }).join('')}
                </select>
            </td>
            <td class="px-2 py-1.5">
                <input type="text" 
                    class="expense-input w-full px-2 py-1 text-xs border-gray-200 rounded focus:ring-primary focus:border-primary" 
                    data-index="${index}" 
                    data-field="description" 
                    value="${escapeHtml(expense.description || '')}" 
                    placeholder="${window.__('Expense Description') || 'Description'}">
            </td>
            <td class="px-2 py-1.5 text-center">
                <button type="button" class="remove-expense-btn text-red-500 hover:text-red-700 transition-colors" 
                    data-index="${index}" 
                    title="${window.__('Delete') || 'Delete'}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" 
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                    </svg>
                </button>
            </td>
        </tr>
    `).join('');
    
    // Attach event listeners for inputs
    if (callbacks.onUpdate) {
        tbody.querySelectorAll('.expense-input').forEach(input => {
            input.addEventListener('input', (e) => {
                const index = parseInt(e.target.dataset.index);
                const field = e.target.dataset.field;
                const value = e.target.value;
                callbacks.onUpdate(index, field, value);
            });
            
            input.addEventListener('change', (e) => {
                const index = parseInt(e.target.dataset.index);
                const field = e.target.dataset.field;
                const value = e.target.value;
                callbacks.onUpdate(index, field, value);
            });
        });
    }
    
    // Attach event listeners for remove buttons
    if (callbacks.onRemove) {
        const removeButtons = tbody.querySelectorAll('.remove-expense-btn');
        console.log(`🗑️ Attaching ${removeButtons.length} remove button listeners for expenses`);
        
        removeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const index = parseInt(e.currentTarget.dataset.index);
                console.log('🗑️ Remove expense button clicked, index:', index);
                callbacks.onRemove(index);
            });
        });
    } else {
        console.warn('⚠️ No onRemove callback provided for expenses');
    }
}

// Render search results
export function renderSearchResults(containerId, results, onSelect) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (!results || results.length === 0) {
        container.classList.add('hidden');
        container.innerHTML = '';
        return;
    }

    container.classList.remove('hidden');
    container.innerHTML = `
        <ul class="py-1">
            ${results.map((item, index) => `
                <li class="px-4 py-2 hover:bg-gray-50 cursor-pointer transition-colors ${index === 0 ? 'bg-primary text-white' : ''}" 
                    data-item-id="${item.id}">
                    ${escapeHtml(item.name)}
                    ${item.code ? `<span class="text-xs opacity-75 block">${escapeHtml(item.code)}</span>` : ''}
                </li>
            `).join('')}
        </ul>
    `;

    // Add click handlers
    container.querySelectorAll('li[data-item-id]').forEach(item => {
        item.addEventListener('click', () => {
            const id = parseInt(item.dataset.itemId);
            onSelect(id);
            container.classList.add('hidden');
            container.innerHTML = '';
        });
    });
}
