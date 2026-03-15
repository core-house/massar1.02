<?php

namespace Modules\CRM\Enums;

enum TaskStatusEnum: string
{
    case PENDING = 'قيد الانتظار';
    case IN_PROGRESS = 'قيد التنفيذ';
    case COMPLETED = 'مكتملة';
    case CANCELLED = 'ملغاة';

    // 🟡 نرجع الاسم بالعربي
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('crm::crm.pending'),
            self::IN_PROGRESS => __('crm::crm.in_progress'),
            self::COMPLETED => __('crm::crm.completed'),
            self::CANCELLED => __('crm::crm.cancelled'),
        };
    }

    // 🟢 نرجع اللون
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::IN_PROGRESS => 'info',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
