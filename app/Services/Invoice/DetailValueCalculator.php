<?php

declare(strict_types=1);

namespace App\Services\Invoice;

use InvalidArgumentException;

/**
 * DetailValueCalculator Service
 *
 * Calculates accurate detail_value for invoice items including:
 * - Item-level discounts and additional charges
 * - Proportionally distributed invoice-level discounts
 * - Proportionally distributed invoice-level additional charges
 *
 * The detail_value is used in average cost calculations:
 * average_cost = SUM(detail_value) / SUM(qty_in - qty_out)
 *
 * @package App\Services\Invoice
 */
class DetailValueCalculator
{
    /**
     * Rounding tolerance for floating point comparisons
     */
    private const TOLERANCE = 0.01;

    /**
     * Calculate detail_value for an item with distributed invoice discounts/additions.
     *
     * This method calculates the final value of an invoice item after applying:
     * 1. Item-level discount
     * 2. Item-level additional charges
     * 3. Proportionally distributed invoice-level discount
     * 4. Proportionally distributed invoice-level additional charges
     *
     * Formula:
     * detail_value = (item_price × quantity) - item_discount + item_additional
     *                - distributed_invoice_discount + distributed_invoice_additional
     *
     * @param array $itemData Item data containing:
     *                        - item_price: float - Unit price of the item
     *                        - quantity: float - Quantity (qty_in or qty_out)
     *                        - item_discount: float - Item-level discount (default: 0)
     *                        - additional: float - Item-level additional charges (default: 0)
     * @param array $invoiceData Invoice data containing:
     *                           - fat_disc: float - Invoice discount amount (default: 0)
     *                           - fat_disc_per: float - Invoice discount percentage (default: 0)
     *                           - fat_plus: float - Invoice additional amount (default: 0)
     *                           - fat_plus_per: float - Invoice additional percentage (default: 0)
     * @param float $invoiceSubtotal Total invoice value before invoice-level discount/additional
     *
     * @return array Calculation results with:
     *               - detail_value: float - Final calculated value
     *               - item_subtotal: float - Item value before invoice-level adjustments
     *               - distributed_discount: float - Invoice discount allocated to this item
     *               - distributed_additional: float - Invoice additional allocated to this item
     *               - breakdown: array - Detailed calculation breakdown for audit
     *
     * @throws InvalidArgumentException if data is invalid or missing required fields
     */
    public function calculate(array $itemData, array $invoiceData, float $invoiceSubtotal): array
    {
        // Validate required item fields
        if (!isset($itemData['item_price']) || !isset($itemData['quantity'])) {
            throw new InvalidArgumentException('Item data must contain item_price and quantity');
        }

        // Extract and validate item data
        $itemPrice = (float) $itemData['item_price'];
        $quantity = (float) $itemData['quantity'];
        $itemDiscount = (float) ($itemData['item_discount'] ?? 0);
        $itemAdditional = (float) ($itemData['additional'] ?? 0);

        // Validate numeric values
        if ($itemPrice < 0) {
            throw new InvalidArgumentException('Item price cannot be negative');
        }

        if ($quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be greater than zero');
        }

        if ($itemDiscount < 0) {
            throw new InvalidArgumentException('Item discount cannot be negative');
        }

        if ($itemAdditional < 0) {
            throw new InvalidArgumentException('Item additional cannot be negative');
        }

        // Calculate item subtotal (before invoice-level adjustments)
        $itemSubtotal = ($itemPrice * $quantity) - $itemDiscount + $itemAdditional;

        // Validate invoice subtotal
        if ($invoiceSubtotal <= 0) {
            throw new InvalidArgumentException('Invoice subtotal must be greater than zero');
        }

        // Calculate distributed invoice discount
        $distributedDiscount = $this->distributeInvoiceDiscount(
            $itemSubtotal,
            $invoiceSubtotal,
            $invoiceData
        );

        // Calculate distributed invoice additional
        $distributedAdditional = $this->distributeInvoiceAdditional(
            $itemSubtotal,
            $invoiceSubtotal,
            $invoiceData
        );

        // Calculate final detail_value
        $detailValue = $itemSubtotal - $distributedDiscount + $distributedAdditional;

        // Ensure detail_value is not negative
        $detailValue = max(0, $detailValue);

        // Return calculation results with breakdown
        return [
            'detail_value' => round($detailValue, 2),
            'item_subtotal' => round($itemSubtotal, 2),
            'distributed_discount' => round($distributedDiscount, 2),
            'distributed_additional' => round($distributedAdditional, 2),
            'breakdown' => [
                'item_price' => $itemPrice,
                'quantity' => $quantity,
                'item_discount' => $itemDiscount,
                'item_additional' => $itemAdditional,
                'item_subtotal' => round($itemSubtotal, 2),
                'distributed_discount' => round($distributedDiscount, 2),
                'distributed_additional' => round($distributedAdditional, 2),
                'detail_value' => round($detailValue, 2),
            ],
        ];
    }

    /**
     * Calculate invoice subtotal from items (before invoice-level discount/additional).
     *
     * The invoice subtotal is the sum of all item subtotals, where each item subtotal is:
     * item_subtotal = (item_price × quantity) - item_discount + item_additional
     *
     * This value is used as the basis for proportional distribution of invoice-level
     * discounts and additional charges.
     *
     * @param array $items Array of items, each containing:
     *                     - item_price: float - Unit price
     *                     - quantity: float - Quantity
     *                     - item_discount: float - Item-level discount (optional)
     *                     - additional: float - Item-level additional (optional)
     *
     * @return float Invoice subtotal
     *
     * @throws InvalidArgumentException if items array is empty or contains invalid data
     */
    public function calculateInvoiceSubtotal(array $items): float
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Items array cannot be empty');
        }

        $subtotal = 0.0;

        foreach ($items as $index => $item) {
            // Validate required fields
            if (!isset($item['item_price']) || !isset($item['quantity'])) {
                throw new InvalidArgumentException(
                    "Item at index {$index} must contain item_price and quantity"
                );
            }

            $itemPrice = (float) $item['item_price'];
            $quantity = (float) $item['quantity'];
            $itemDiscount = (float) ($item['item_discount'] ?? 0);
            $itemAdditional = (float) ($item['additional'] ?? 0);

            // Validate values
            if ($itemPrice < 0) {
                throw new InvalidArgumentException(
                    "Item price at index {$index} cannot be negative"
                );
            }

            if ($quantity <= 0) {
                throw new InvalidArgumentException(
                    "Quantity at index {$index} must be greater than zero"
                );
            }

            // Calculate item subtotal
            $itemSubtotal = ($itemPrice * $quantity) - $itemDiscount + $itemAdditional;
            $subtotal += $itemSubtotal;
        }

        return round($subtotal, 2);
    }

    /**
     * Distribute invoice-level discount across items proportionally.
     *
     * Supports two distribution methods:
     * 1. Fixed Amount: Distributes fat_disc proportionally based on item value
     *    Formula: distributed_discount = (item_subtotal / invoice_subtotal) × fat_disc
     *
     * 2. Percentage: Applies fat_disc_per percentage to item value
     *    Formula: distributed_discount = item_subtotal × (fat_disc_per / 100)
     *
     * If both fat_disc and fat_disc_per are provided, fat_disc takes precedence.
     *
     * @param float $itemSubtotal Item's subtotal value (before invoice-level adjustments)
     * @param float $invoiceSubtotal Total invoice subtotal (sum of all items)
     * @param array $invoiceData Invoice discount data containing:
     *                           - fat_disc: float - Fixed discount amount (optional)
     *                           - fat_disc_per: float - Discount percentage (optional)
     *
     * @return float Distributed discount amount for this item
     */
    private function distributeInvoiceDiscount(
        float $itemSubtotal,
        float $invoiceSubtotal,
        array $invoiceData
    ): float {
        $fatDisc = (float) ($invoiceData['fat_disc'] ?? 0);
        $fatDiscPer = (float) ($invoiceData['fat_disc_per'] ?? 0);

        // No discount to distribute
        if ($fatDisc <= 0 && $fatDiscPer <= 0) {
            return 0.0;
        }

        // Fixed amount distribution (takes precedence)
        if ($fatDisc > 0) {
            $itemRatio = $itemSubtotal / $invoiceSubtotal;
            return $fatDisc * $itemRatio;
        }

        // Percentage distribution
        if ($fatDiscPer > 0) {
            return $itemSubtotal * ($fatDiscPer / 100);
        }

        return 0.0;
    }

    /**
     * Distribute invoice-level additional charges across items proportionally.
     *
     * Supports two distribution methods:
     * 1. Fixed Amount: Distributes fat_plus proportionally based on item value
     *    Formula: distributed_additional = (item_subtotal / invoice_subtotal) × fat_plus
     *
     * 2. Percentage: Applies fat_plus_per percentage to item value
     *    Formula: distributed_additional = item_subtotal × (fat_plus_per / 100)
     *
     * If both fat_plus and fat_plus_per are provided, fat_plus takes precedence.
     *
     * @param float $itemSubtotal Item's subtotal value (before invoice-level adjustments)
     * @param float $invoiceSubtotal Total invoice subtotal (sum of all items)
     * @param array $invoiceData Invoice additional data containing:
     *                           - fat_plus: float - Fixed additional amount (optional)
     *                           - fat_plus_per: float - Additional percentage (optional)
     *
     * @return float Distributed additional amount for this item
     */
    private function distributeInvoiceAdditional(
        float $itemSubtotal,
        float $invoiceSubtotal,
        array $invoiceData
    ): float {
        $fatPlus = (float) ($invoiceData['fat_plus'] ?? 0);
        $fatPlusPer = (float) ($invoiceData['fat_plus_per'] ?? 0);

        // No additional to distribute
        if ($fatPlus <= 0 && $fatPlusPer <= 0) {
            return 0.0;
        }

        // Fixed amount distribution (takes precedence)
        if ($fatPlus > 0) {
            $itemRatio = $itemSubtotal / $invoiceSubtotal;
            return $fatPlus * $itemRatio;
        }

        // Percentage distribution
        if ($fatPlusPer > 0) {
            return $itemSubtotal * ($fatPlusPer / 100);
        }

        return 0.0;
    }
}
