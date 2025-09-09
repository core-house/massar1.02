<?php

namespace App\Helpers;

use App\Enums\OperationTypeEnum;

class OperationTypeHelper
{
    /**
     * Get the edit route name for an operation type
     */
    public static function getEditRoute(int $operationType): string
    {
        $enum = OperationTypeEnum::fromValue($operationType);
        return $enum?->getEditRoute() ?? 'journals.edit';
    }

    /**
     * Get the edit URL for an operation
     */
    public static function getEditUrl(int $operationType, int $operationId): string
    {
        $route = self::getEditRoute($operationType);
        return route($route, $operationId);
    }

    /**
     * Check if an operation type is an invoice
     */
    public static function isInvoice(int $operationType): bool
    {
        $enum = OperationTypeEnum::fromValue($operationType);
        return $enum?->isInvoice() ?? false;
    }

    /**
     * Check if an operation type is a voucher
     */
    public static function isVoucher(int $operationType): bool
    {
        $enum = OperationTypeEnum::fromValue($operationType);
        return $enum?->isVoucher() ?? false;
    }

    /**
     * Check if an operation type is a journal entry
     */
    public static function isJournal(int $operationType): bool
    {
        $enum = OperationTypeEnum::fromValue($operationType);
        return $enum?->isJournal() ?? false;
    }

    /**
     * Check if an operation type is a transfer
     */
    public static function isTransfer(int $operationType): bool
    {
        $enum = OperationTypeEnum::fromValue($operationType);
        return $enum?->isTransfer() ?? false;
    }

    /**
     * Get Arabic name for operation type
     */
    public static function getArabicName(int $operationType): string
    {
        $enum = OperationTypeEnum::fromValue($operationType);
        return $enum?->getArabicName() ?? 'عملية غير محددة';
    }

    /**
     * Get all invoice types
     */
    public static function getInvoiceTypes(): array
    {
        return [
            OperationTypeEnum::SALES_INVOICE->value,
            OperationTypeEnum::PURCHASE_INVOICE->value,
            OperationTypeEnum::SALES_RETURN->value,
            OperationTypeEnum::PURCHASE_RETURN->value,
            OperationTypeEnum::DAMAGE_INVOICE->value,
            OperationTypeEnum::SALE_ORDER->value,
            OperationTypeEnum::PURCHASE_ORDER->value,
            OperationTypeEnum::QUOTATION_CUSTOMER->value,
            OperationTypeEnum::QUOTATION_SUPPLIER->value,
            OperationTypeEnum::WITHDRAW_ORDER->value,
            OperationTypeEnum::ADD_ORDER->value,
            OperationTypeEnum::INVENTORY_TRANSFER->value,
            OperationTypeEnum::RESERVATION_ORDER->value,
        ];
    }

    /**
     * Get all voucher types
     */
    public static function getVoucherTypes(): array
    {
        return [
            OperationTypeEnum::RECEIPT->value,
            OperationTypeEnum::PAYMENT->value,
            OperationTypeEnum::MULTI_RECEIPT->value,
            OperationTypeEnum::MULTI_PAYMENT->value,
            OperationTypeEnum::ALLOWED_DISCOUNT->value,
            OperationTypeEnum::EARNED_DISCOUNT->value,
            OperationTypeEnum::PETTY_CASH_SETTLEMENT->value,
            OperationTypeEnum::STOCK_DAMAGE->value,
            OperationTypeEnum::PROVISION_ENTRY->value,
            OperationTypeEnum::PERSONAL_LOAN->value,
        ];
    }

    /**
     * Get all journal types
     */
    public static function getJournalTypes(): array
    {
        return [
            OperationTypeEnum::DAILY_ENTRY->value,
            OperationTypeEnum::MULTI_ENTRY->value,
        ];
    }

    /**
     * Get all transfer types
     */
    public static function getTransferTypes(): array
    {
        return [
            OperationTypeEnum::CASH_TO_CASH->value,
            OperationTypeEnum::CASH_TO_BANK->value,
            OperationTypeEnum::BANK_TO_CASH->value,
            OperationTypeEnum::BANK_TO_BANK->value,
            OperationTypeEnum::BRANCH_TRANSFER->value,
            OperationTypeEnum::CURRENCY_CONVERSION->value,
        ];
    }

    /**
     * Get all manufacturing types
     */
    public static function getManufacturingTypes(): array
    {
        return [
            OperationTypeEnum::PRODUCTION_MODEL->value,
            OperationTypeEnum::JOB_ORDER->value,
            OperationTypeEnum::STANDARD_MANUFACTURING->value,
            OperationTypeEnum::FREE_MANUFACTURING->value,
            OperationTypeEnum::MANUFACTURING_EXAMPLE->value,
        ];
    }
}