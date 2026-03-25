<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Voucher;

class VoucherHelper
{
    /**
     * Get voucher type badge configuration
     */
    public static function getTypeBadge(int $proType): array
    {
        return match ($proType) {
            1 => ['class' => 'bg-success', 'text' => __('Receipt Voucher')],
            2 => ['class' => 'bg-danger', 'text' => __('Payment Voucher')],
            101 => ['class' => 'bg-warning', 'text' => __('Expense Payment')],
            32 => ['class' => 'bg-primary', 'text' => __('Multi Receipt')],
            33 => ['class' => 'bg-info', 'text' => __('Multi Payment')],
            default => ['class' => 'bg-secondary', 'text' => __('Not Specified')],
        };
    }

    /**
     * Get voucher permissions based on type
     */
    public static function getPermissions(int $proType): array
    {
        return match ($proType) {
            1 => [
                'edit' => 'edit recipt',
                'delete' => 'delete recipt',
                'create' => 'create recipt',
            ],
            2 => [
                'edit' => 'edit payment',
                'delete' => 'delete payment',
                'create' => 'create payment',
            ],
            101 => [
                'edit' => 'edit exp-payment',
                'delete' => 'delete exp-payment',
                'create' => 'create exp-payment',
            ],
            32 => [
                'edit' => 'edit multi-receipt',
                'delete' => 'delete multi-receipt',
                'create' => 'create multi-receipt',
            ],
            33 => [
                'edit' => 'edit multi-payment',
                'delete' => 'delete multi-payment',
                'create' => 'create multi-payment',
            ],
            default => [
                'edit' => null,
                'delete' => null,
                'create' => null,
            ],
        };
    }

    /**
     * Check if user can perform action on voucher
     */
    public static function canPerformAction(Voucher $voucher, string $action): bool
    {
        $permissions = self::getPermissions($voucher->pro_type);
        $permission = $permissions[$action] ?? null;

        return $permission ? auth()->user()->can($permission) : false;
    }

    /**
     * Get all available voucher types with create permissions
     */
    public static function getAvailableTypes(): array
    {
        $types = [
            'receipt' => [
                'permission' => 'create recipt',
                'route' => 'vouchers.create',
                'icon' => 'fa-plus-circle',
                'color' => 'success',
                'label' => __('General Receipt Voucher'),
            ],
            'payment' => [
                'permission' => 'create payment',
                'route' => 'vouchers.create',
                'icon' => 'fa-minus-circle',
                'color' => 'danger',
                'label' => __('General Payment Voucher'),
            ],
            'exp-payment' => [
                'permission' => 'create exp-payment',
                'route' => 'vouchers.create',
                'icon' => 'fa-credit-card',
                'color' => 'warning',
                'label' => __('Expense Payment Voucher'),
            ],
            'multi_payment' => [
                'permission' => 'create multi-payment',
                'route' => 'multi-vouchers.create',
                'icon' => 'fa-list-alt',
                'color' => 'info',
                'label' => __('Multi Payment Voucher'),
            ],
            'multi_receipt' => [
                'permission' => 'create multi-receipt',
                'route' => 'multi-vouchers.create',
                'icon' => 'fa-list-ul',
                'color' => 'primary',
                'label' => __('Multi Receipt Voucher'),
            ],
        ];

        return array_filter($types, fn ($type) => auth()->user()->can($type['permission']));
    }

    /**
     * Get journal entry accounts for voucher
     */
    public static function getJournalAccounts(Voucher $voucher): array
    {
        if (in_array($voucher->pro_type, [1])) {
            // Receipt: Debit Cash/Bank, Credit Customer/Account
            return [
                'debit' => $voucher->account1,
                'credit' => $voucher->account2,
                'debit_amount' => $voucher->pro_value,
                'credit_amount' => $voucher->pro_value,
            ];
        }

        if (in_array($voucher->pro_type, [2, 101])) {
            // Payment: Debit Supplier/Expense, Credit Cash/Bank
            return [
                'debit' => $voucher->account2,
                'credit' => $voucher->account1,
                'debit_amount' => $voucher->pro_value,
                'credit_amount' => $voucher->pro_value,
            ];
        }

        // Multi vouchers - default
        return [
            'debit' => $voucher->account1,
            'credit' => $voucher->account2,
            'debit_amount' => $voucher->pro_value,
            'credit_amount' => $voucher->pro_value,
        ];
    }
}
