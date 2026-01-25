<style>
    .pos-create-container {
        height: 100vh;
        display: flex;
        flex-direction: column;
        background: #f5f5f5;
        overflow: hidden;
    }
    .product-card {
        cursor: pointer;
        transition: transform 0.2s;
    }
    .product-card:hover {
        transform: scale(1.02);
    }
    .category-btn.active {
        background: #FFD700 !important;
        border-color: #FFD700 !important;
    }
    #onlineStatus {
        transition: all 0.3s ease;
    }
    #onlineStatus.online {
        background-color: #28a745 !important;
        color: white;
    }
    #onlineStatus.offline {
        background-color: #dc3545 !important;
        color: white;
    }

    /* Dark Mode Toggle Switch */
    .dark-mode-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
        cursor: pointer;
    }

    .dark-mode-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .dark-mode-switch .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d1d5db;
        transition: 0.3s;
        border-radius: 28px;
    }

    .dark-mode-switch .slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .dark-mode-switch input:checked + .slider {
        background-color: #374151;
    }

    .dark-mode-switch input:checked + .slider:before {
        transform: translateX(24px);
    }

    .dark-mode-switch:hover .slider {
        background-color: #9ca3af;
    }

    .dark-mode-switch input:checked:hover + .slider {
        background-color: #4b5563;
    }

    /* Dark Mode Styles for Create Page */
    body.dark-mode .pos-create-container {
        background: #111827;
    }

    body.dark-mode .pos-top-nav {
        background: #1f2937 !important;
        border-bottom-color: #374151 !important;
    }

    body.dark-mode .dark-mode-switch .slider {
        background-color: #4b5563;
    }

    body.dark-mode .dark-mode-switch:hover .slider {
        background-color: #6b7280;
    }

    body.dark-mode .form-control {
        background: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }

    body.dark-mode .form-control:focus {
        background: #1f2937;
        border-color: #6b7280;
        color: #f9fafb;
    }

    body.dark-mode .btn-primary {
        background: #374151;
        border-color: #374151;
        color: #f9fafb;
    }

    body.dark-mode .btn-primary:hover {
        background: #4b5563;
        border-color: #4b5563;
    }

    body.dark-mode .btn-link {
        color: #d1d5db !important;
    }

    body.dark-mode .btn-link:hover {
        color: #f9fafb !important;
    }

    body.dark-mode #orderNumber {
        color: #d1d5db !important;
    }
</style>
