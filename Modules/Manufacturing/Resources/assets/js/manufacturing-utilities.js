/**
 * Manufacturing Utilities
 * Helper functions for manufacturing invoice form
 */

// Format number to 2 decimal places
export function formatNumber(num) {
    return parseFloat(num || 0).toFixed(2);
}

// Parse float safely
export function parseFloatSafe(value) {
    const parsed = parseFloat(value);
    return isNaN(parsed) ? 0 : parsed;
}

// Show loading overlay
export function showLoading(message = 'Loading...') {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        const messageEl = overlay.querySelector('.loading-message');
        if (messageEl) messageEl.textContent = message;
        overlay.classList.remove('d-none');
    }
}

// Hide loading overlay
export function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.add('d-none');
    }
}

// Show/hide element
export function toggleElement(elementId, show) {
    const element = document.getElementById(elementId);
    if (element) {
        if (show) {
            element.classList.remove('d-none');
        } else {
            element.classList.add('d-none');
        }
    }
}

// Debounce function
export function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Generate unique ID
export function generateId() {
    return Date.now() + Math.random().toString(36).substr(2, 9);
}

// Show toast notification
export function showToast(message, type = 'success') {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    } else {
        alert(message);
    }
}

// Confirm dialog
export function confirmDialog(title, text, confirmText = 'Yes', cancelText = 'Cancel') {
    if (typeof Swal !== 'undefined') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText
        });
    } else {
        return Promise.resolve({ isConfirmed: confirm(text) });
    }
}

// AJAX helper
export async function fetchJSON(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            },
            ...options
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}
