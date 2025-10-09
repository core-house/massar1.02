<?php

return [
    'name' => 'Checks',
    'description' => 'Checks management module for handling check operations',
    
    // Default pagination settings
    'pagination' => [
        'per_page' => 15,
        'max_per_page' => 100,
    ],
    
    // File upload settings
    'attachments' => [
        'max_size' => 5120, // 5MB in KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        'storage_disk' => 'public',
        'storage_path' => 'checks/attachments',
    ],
    
    // Export settings
    'export' => [
        'max_records' => 10000,
        'timeout' => 300, // 5 minutes
    ],
    
    // Notification settings
    'notifications' => [
        'overdue_reminder_days' => 3,
        'due_soon_days' => 7,
    ],
    
    // Status colors for UI
    'status_colors' => [
        'pending' => 'warning',
        'cleared' => 'success',
        'bounced' => 'danger',
        'cancelled' => 'secondary',
    ],
    
    // Type colors for UI
    'type_colors' => [
        'incoming' => 'success',
        'outgoing' => 'info',
    ],
];