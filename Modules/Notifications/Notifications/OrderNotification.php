<?php

namespace Modules\Notifications\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OrderNotification extends Notification
{
    public $data;

    public function __construct($data)
    {
        // لو سترينج نحوله لآري فيها message
        if (is_string($data)) {
            $this->data = [
                'id' => null,
                'title' => null,
                'message' => $data,
                'icon' => null,
                'created_at' => now()->toDateTimeString(),
            ];
        } else {
            // لو آري نستخدمها زي ما هي
            $this->data = $data;
        }
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'id' => $this->data['id'] ?? null,
            'title' => $this->data['title'] ?? null,
            'message' => $this->data['message'] ?? null,
            'icon' => $this->data['icon'] ?? null,
            'created_at' => $this->data['created_at'] ?? now()->toDateTimeString(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'notification' => $this->toArray($notifiable) // أضف هذا التغليف
        ]);
    }

    /**
     * Get the type of the notification being broadcast.
     *
     * @return string
     */
    public function broadcastAs()
    {
        // هنا بنحدد اسم الحدث اللي الجافاسكريبت هيستمع ليه
        return 'new-notification';
    }
}
