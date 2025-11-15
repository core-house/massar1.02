<?php

namespace Modules\Inquiries\Enums;

enum InquiryDraftStatus: string
{
    case DRAFT = 'draft';
    case COMPLETED = 'completed';
    case PENDING = 'pending';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => __('Draft'),
            self::COMPLETED => __('Completed'),
            self::PENDING => __('Pending'),
            self::CANCELLED => __('Cancelled'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::COMPLETED => 'success',
            self::PENDING => 'warning',
            self::CANCELLED => 'danger',
        };
    }
}
