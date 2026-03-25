<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-id" content="{{ auth()->id() }}">

<style>
    .notification-item {
        transition: all 0.3s ease;
        border-radius: 8px;
        margin: 2px 0;
        position: relative;
        border-left: 3px solid transparent;
    }
    
    .notification-item:hover {
        background-color: rgba(52, 211, 163, 0.05) !important;
        border-left-color: #34d3a3;
        transform: translateX(-3px);
        box-shadow: 0 2px 8px rgba(52, 211, 163, 0.15);
    }
    
    .notification-unread {
        background-color: rgba(52, 211, 163, 0.03);
        border-left-color: #34d3a3;
    }
    
    .notification-unread h6 {
        font-weight: 700 !important;
    }
    
    .notification-unread small {
        font-weight: 600 !important;
    }
    
    .notification-read h6 {
        font-weight: 400 !important;
    }
    
    .notification-read small {
        font-weight: 400 !important;
    }
    
    .dropdown-menu {
        max-height: 400px;
        overflow-y: auto;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
    
    .notification-item:first-child {
        animation: slideInFromTop 0.4s ease-out;
    }
    
    @keyframes slideInFromTop {
        0% {
            opacity: 0;
            transform: translateY(-20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    #markAllRead:hover {
        background-color: rgba(52, 211, 163, 0.1) !important;
        color: #2ab88a !important;
    }
    
    .dropdown-menu {
        animation: fadeInDown 0.3s ease-out;
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .noti-icon-badge {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
    }
</style>

<li class="dropdown notification-list me-3" style="position: relative; z-index: 1050;">
    <a class="nav-link dropdown-toggle arrow-none waves-light waves-effect" data-bs-toggle="dropdown" href="#"
        role="button" aria-haspopup="false" aria-expanded="false" id="notificationDropdown" 
        title="{{ __('Notifications') }}" style="color: #34d3a3; position: relative;">
        <i class="fas fa-bell fa-2x" style="color: #34d3a3;"></i>
        <span class="badge bg-danger rounded-pill noti-icon-badge" id="notificationBadge"
            style="display: none; position: absolute; top: -5px; right: -5px; font-size: 0.65rem; padding: 0.25em 0.5em;">0</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-lg pt-0"
        style="z-index: 2000; overflow: hidden; border-radius: 12px; min-width: 350px;">
        <h6 class="dropdown-item-text font-15 m-0 py-3 border-bottom d-flex justify-content-between align-items-center" 
            style="background: linear-gradient(135deg, #34d3a3 0%, #2ab88a 100%); color: white;">
            <span><i class="fas fa-bell me-2"></i>{{ __('Notifications') }}</span>
            <span class="badge bg-white text-success rounded-pill" id="notificationCount" style="font-weight: 600;">0</span>
        </h6>

        <div class="notification-menu" data-simplebar="init" id="notificationList"
            style="max-height: 400px; overflow-y: auto; overflow-x: hidden;">
            <div class="text-center py-4">
                <i class="fas fa-bell-slash fa-3x text-muted mb-2" style="opacity: 0.3;"></i>
                <p class="text-muted mb-0">{{ __('No notifications') }}</p>
            </div>
        </div>

        <a href="javascript:void(0);" class="dropdown-item text-center border-top py-2" id="markAllRead"
           style="color: #34d3a3; font-weight: 600; transition: all 0.3s ease;">
            <i class="fas fa-check-double me-1"></i> {{ __('Mark all as read') }}
        </a>
    </div>
</li>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadNotifications();
        setInterval(loadNotifications, 30000);
        listenForNotifications();

        document.getElementById('markAllRead').addEventListener('click', function() {
            markAllAsRead();
        });
    });

    function listenForNotifications() {
        const metaEl = document.querySelector('meta[name="user-id"]');
        const userId = metaEl ? metaEl.getAttribute('content') : null;

        if (!userId) {
            console.error('User ID meta tag not found.');
            return;
        }
        if (!window.Echo) {
            console.error('Laravel Echo is not initialized.');
            return;
        }

        window.Echo.private(`App.Models.User.${userId}`)
            .listen('.new-notification', (e) => {
                addSingleNotification(e.notification);
            });
    }

    function loadNotifications() {
        fetch('/notifications')
            .then(response => response.json())
            .then(data => {
                updateNotificationUI(data.notifications, data.unread_count);
            })
            .catch(error => console.error('خطأ في تحميل الإشعارات:', error));
    }

    function updateNotificationUI(notifications, unreadCount) {
        const badge = document.getElementById('notificationBadge');
        const count = document.getElementById('notificationCount');
        const list = document.getElementById('notificationList');

        if (unreadCount > 0) {
            badge.style.display = 'inline';
            badge.textContent = unreadCount;
        } else {
            badge.style.display = 'none';
        }

        count.textContent = unreadCount;

        if (notifications.length === 0) {
            list.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-2" style="opacity: 0.3;"></i>
                    <p class="text-muted mb-0">${'{{ __("No notifications") }}'}</p>
                </div>
            `;
            return;
        }

        let notificationHTML = '';
        notifications.forEach(notification => {
            const data = notification.data;
            const timeAgo = getTimeAgo(notification.created_at);
            const isRead = notification.read_at !== null;
            const readClass = isRead ? 'notification-read' : 'notification-unread';
            const url = data.url ? data.url : 'javascript:void(0)'; 

            notificationHTML += `
                <a href="${url}" class="dropdown-item py-3 notification-item ${readClass}"
                   data-notification-id="${notification.id}" data-url="${data.url || ''}"
                   style="text-decoration: none; display: block;">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center" 
                                 style="width: 45px; height: 45px; background: linear-gradient(135deg, #34d3a3 0%, #2ab88a 100%);">
                                ${getNotificationIcon(data.icon)}
                            </div>
                        </div>
                        <div class="flex-grow-1" style="min-width: 0;">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="mb-0 text-dark" style="font-size: 0.9rem;">${data.title}</h6>
                                <small class="text-muted ms-2" style="white-space: nowrap; font-size: 0.75rem;">${timeAgo}</small>
                            </div>
                            <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.4;">${data.message}</p>
                        </div>
                    </div>
                </a>
            `;
        });

        list.innerHTML = notificationHTML;

        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function(e) {
                const url = this.dataset.url;
                if (url && url !== 'undefined' && url !== '') {
                    e.preventDefault(); 
                }
                
                const notificationId = this.dataset.notificationId;
                markAsRead(notificationId, url);
            });
        });
    }

    function addSingleNotification(notification) {
        const list = document.getElementById('notificationList');
        const timeAgo = getTimeAgo(notification.created_at);
        const url = notification.url ? notification.url : 'javascript:void(0)';

        const notificationHTML = `
            <a href="${url}" class="dropdown-item py-3 notification-item notification-unread"
               data-notification-id="${notification.id}" data-url="${notification.url || ''}"
               style="text-decoration: none; display: block;">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="avatar-md rounded-circle d-flex align-items-center justify-content-center" 
                             style="width: 45px; height: 45px; background: linear-gradient(135deg, #34d3a3 0%, #2ab88a 100%);">
                            ${getNotificationIcon(notification.icon)}
                        </div>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <h6 class="mb-0 text-dark" style="font-size: 0.9rem;">${notification.title}</h6>
                            <small class="text-muted ms-2" style="white-space: nowrap; font-size: 0.75rem;">${timeAgo}</small>
                        </div>
                        <p class="mb-0 text-muted" style="font-size: 0.85rem; line-height: 1.4;">${notification.message}</p>
                    </div>
                </div>
            </a>
        `;

        if (list.querySelector('.text-center')) {
             list.innerHTML = notificationHTML;
        } else {
             list.innerHTML = notificationHTML + list.innerHTML;
        }

        const badge = document.getElementById('notificationBadge');
        const count = document.getElementById('notificationCount');
        let currentCount = parseInt(count.textContent) + 1;
        count.textContent = currentCount;
        badge.style.display = 'inline';
        badge.textContent = currentCount;

        const newItem = list.querySelector('.notification-item:first-child');
        newItem.addEventListener('click', function(e) {
            const url = this.dataset.url;
             if (url && url !== 'undefined' && url !== '') {
                e.preventDefault(); 
            }
            const notificationId = this.dataset.notificationId;
            markAsRead(notificationId, url);
        });
    }

    function getNotificationIcon(iconType) {
        if (iconType && (iconType.includes('las ') || iconType.includes('fa ') || iconType.includes('fas ') || iconType.includes('far '))) {
             return `<i class="${iconType}" style="font-size: 1.5rem; color: white;"></i>`;
        }

        const icons = {
            'shopping-cart': '<i class="fas fa-shopping-cart" style="font-size: 1.5rem; color: white;"></i>',
            'users': '<i class="fas fa-users" style="font-size: 1.5rem; color: white;"></i>',
            'check-circle': '<i class="fas fa-check-circle" style="font-size: 1.5rem; color: white;"></i>',
            'bell': '<i class="fas fa-bell" style="font-size: 1.5rem; color: white;"></i>',
            'info': '<i class="fas fa-info-circle" style="font-size: 1.5rem; color: white;"></i>',
            'warning': '<i class="fas fa-exclamation-triangle" style="font-size: 1.5rem; color: white;"></i>',
            'success': '<i class="fas fa-check" style="font-size: 1.5rem; color: white;"></i>',
            'danger': '<i class="fas fa-times-circle" style="font-size: 1.5rem; color: white;"></i>'
        };

        return icons[iconType] || icons['bell'];
    }

    function getTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / 60000);

        if (diffInMinutes < 1) return 'الآن';
        if (diffInMinutes < 60) return `منذ ${diffInMinutes} دقيقة`;
        if (diffInMinutes < 1440) return `منذ ${Math.floor(diffInMinutes / 60)} ساعة`;
        return `منذ ${Math.floor(diffInMinutes / 1440)} يوم`;
    }

    function markAsRead(notificationId, redirectUrl = null) {
        fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (redirectUrl && redirectUrl !== 'undefined' && redirectUrl !== '') {
                        window.location.href = redirectUrl;
                    } else {
                        loadNotifications();
                    }
                }
            })
            .catch(error => {
                console.error('خطأ في تحديد الإشعار كمقروء:', error);
                if (redirectUrl && redirectUrl !== 'undefined' && redirectUrl !== '') {
                    window.location.href = redirectUrl;
                }
            });
    }

    function markAllAsRead() {
        fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadNotifications();
                }
            })
            .catch(error => console.error('خطأ في تحديد الكل كمقروء:', error));
    }
</script>
