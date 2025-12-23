# Requirements Document: Discount and Additional Handling in Average Cost Calculation

## Introduction

This document outlines the requirements for properly handling discounts and additional charges at both item level and invoice level when calculating average costs for inventory items.

## Glossary

- **Item_Discount**: Discount applied to a specific item in an invoice
- **Additional**: Additional charges applied to a specific item in an invoice
- **Fat_Disc**: Total discount applied at the invoice level
- **Fat_Plus**: Total additional charges applied at the invoice level
- **Detail_Value**: Final value of an item after all discounts and additions
- **Average_Cost**: Weighted average cost calculated as SUM(detail_value) / SUM(qty_in - qty_out)
- **Purchase_Invoice**: Invoice type 11 (increases inventory)
- **Sales_Invoice**: Invoice type 12 (decreases inventory)
- **Purchase_Return**: Return of purchased items
- **Sales_Return**: Return of sold items

## Requirements

### Requirement 1: Item-Level Discount and Additional Handling

**User Story:** As a system user, I want item-level discounts and additional charges to be properly reflected in the average cost calculation, so that inventory costs are accurate.

#### Acceptance Criteria

1. WHEN an item has an item_discount value, THE System SHALL subtract this discount from the item's value before calculating detail_value
2. WHEN an item has an additional value, THE System SHALL add this additional charge to the item's value before calculating detail_value
3. THE System SHALL calculate detail_value as: (item_price * quantity) - item_discount + additional
4. WHEN calculating average cost, THE System SHALL use the detail_value which includes item-level discounts and additions
5. THE System SHALL apply item-level discounts and additions for all operation types (purchase, sales, returns)

### Requirement 2: Invoice-Level Discount Distribution

**User Story:** As a system user, I want invoice-level discounts to be distributed proportionally across all items, so that each item's cost reflects its share of the invoice discount.

#### Acceptance Criteria

1. WHEN an invoice has fat_disc value, THE System SHALL distribute this discount proportionally across all items based on their value
2. THE System SHALL calculate each item's discount share as: (item_value / invoice_total) * fat_disc
3. WHEN an invoice has fat_disc_per percentage, THE System SHALL apply this percentage to each item's value
4. THE System SHALL include the distributed invoice discount in the item's detail_value
5. THE System SHALL log the distributed discount amount for audit purposes

### Requirement 3: Invoice-Level Additional Charges Distribution

**User Story:** As a system user, I want invoice-level additional charges to be distributed proportionally across all items, so that each item's cost reflects its share of the additional charges.

#### Acceptance Criteria

1. WHEN an invoice has fat_plus value, THE System SHALL distribute this additional charge proportionally across all items based on their value
2. THE System SHALL calculate each item's additional share as: (item_value / invoice_total) * fat_plus
3. WHEN an invoice has fat_plus_per percentage, THE System SHALL apply this percentage to each item's value
4. THE System SHALL include the distributed additional charge in the item's detail_value
5. THE System SHALL log the distributed additional amount for audit purposes

### Requirement 4: Purchase Invoice Cost Calculation

**User Story:** As a system user, I want purchase invoices to calculate item costs including all discounts and additions, so that inventory valuation is accurate.

#### Acceptance Criteria

1. WHEN a purchase invoice is created, THE System SHALL calculate detail_value including item-level and invoice-level discounts and additions
2. THE System SHALL use the formula: detail_value = (item_price * qty_in) - item_discount + additional - distributed_invoice_discount + distributed_invoice_additional
3. WHEN calculating average cost for purchases, THE System SHALL use detail_value as the cost basis
4. THE System SHALL ensure detail_value is positive (cannot be negative)
5. THE System SHALL recalculate average cost immediately after purchase invoice is saved

### Requirement 5: Sales Invoice Cost Calculation

**User Story:** As a system user, I want sales invoices to use the current average cost for profit calculation, while properly handling discounts and additions for revenue calculation.

#### Acceptance Criteria

1. WHEN a sales invoice is created, THE System SHALL use the current average_cost from items table for cost calculation
2. THE System SHALL calculate detail_value for revenue including all discounts and additions
3. THE System SHALL calculate profit as: (detail_value - (average_cost * qty_out))
4. THE System SHALL NOT recalculate average cost for sales invoices (only uses existing average cost)
5. THE System SHALL update profit values in operation_items table

### Requirement 6: Purchase Return Cost Calculation

**User Story:** As a system user, I want purchase returns to reduce inventory cost properly, so that average cost reflects the returned items.

#### Acceptance Criteria

1. WHEN a purchase return is processed, THE System SHALL use the original purchase cost including discounts and additions
2. THE System SHALL calculate detail_value as negative value: -(original_detail_value)
3. THE System SHALL recalculate average cost after purchase return
4. THE System SHALL ensure the return reduces total inventory value correctly
5. THE System SHALL handle partial returns proportionally

### Requirement 7: Sales Return Cost Calculation

**User Story:** As a system user, I want sales returns to restore inventory at the original sales cost, so that inventory valuation is consistent.

#### Acceptance Criteria

1. WHEN a sales return is processed, THE System SHALL restore inventory using the original sales cost
2. THE System SHALL calculate detail_value based on the original sales invoice
3. THE System SHALL recalculate average cost after sales return
4. THE System SHALL update profit values to reflect the return
5. THE System SHALL handle partial returns proportionally

### Requirement 8: Detail Value Validation

**User Story:** As a developer, I want detail_value to be validated before saving, so that data integrity is maintained.

#### Acceptance Criteria

1. THE System SHALL validate that detail_value is calculated correctly before saving operation_items
2. THE System SHALL ensure detail_value matches: (item_price * quantity) - item_discount + additional ± distributed_amounts
3. WHEN detail_value validation fails, THE System SHALL throw a descriptive exception
4. THE System SHALL log validation errors with full context
5. THE System SHALL prevent saving invalid detail_value

### Requirement 9: Discount and Additional Audit Trail

**User Story:** As an auditor, I want to track all discounts and additions applied to items, so that I can verify cost calculations.

#### Acceptance Criteria

1. THE System SHALL log all item-level discounts and additions when saving invoices
2. THE System SHALL log all invoice-level discount and additional distributions
3. THE System SHALL include original values, discount amounts, and final values in logs
4. THE System SHALL provide a report showing discount and additional breakdown by invoice
5. THE System SHALL maintain audit trail for all cost adjustments

### Requirement 10: Backward Compatibility

**User Story:** As a system administrator, I want the new discount handling to work with existing data, so that migration is smooth.

#### Acceptance Criteria

1. THE System SHALL handle invoices with NULL discount and additional values (treat as zero)
2. THE System SHALL recalculate existing invoices if detail_value is incorrect
3. THE System SHALL provide a command to fix historical detail_value calculations
4. THE System SHALL maintain compatibility with existing average cost calculation logic
5. THE System SHALL not break existing invoice workflows

### Requirement 11: Tax Handling

**User Story:** As a system user, I want tax to be handled separately from discounts and additions, so that tax calculations are accurate.

#### Acceptance Criteria

1. THE System SHALL calculate tax after applying all discounts and additions
2. THE System SHALL NOT include tax in detail_value for cost calculation
3. THE System SHALL store tax separately in fat_tax field
4. THE System SHALL calculate fat_net as: fat_total - fat_disc + fat_plus + fat_tax
5. THE System SHALL use detail_value (excluding tax) for average cost calculation

### Requirement 12: Multi-Currency Support

**User Story:** As a system user, I want discounts and additions to work correctly with multi-currency invoices, so that costs are accurate in all currencies.

#### Acceptance Criteria

1. WHEN an invoice uses a foreign currency, THE System SHALL apply discounts and additions in the invoice currency
2. THE System SHALL convert detail_value to base currency using currency_rate before calculating average cost
3. THE System SHALL store both original currency detail_value and converted value
4. THE System SHALL handle currency rate changes correctly
5. THE System SHALL log currency conversions for audit purposes

## Calculation Formulas

### Item Detail Value Calculation

```
detail_value = (item_price * quantity) - item_discount + additional - distributed_invoice_discount + distributed_invoice_additional
```

### Distributed Invoice Discount

```
distributed_invoice_discount = (item_subtotal / invoice_subtotal) * fat_disc
```

OR

```
distributed_invoice_discount = item_subtotal * (fat_disc_per / 100)
```

### Distributed Invoice Additional

```
distributed_invoice_additional = (item_subtotal / invoice_subtotal) * fat_plus
```

OR

```
distributed_invoice_additional = item_subtotal * (fat_plus_per / 100)
```

### Average Cost Formula (Unchanged)

```
average_cost = SUM(detail_value) / SUM(qty_in - qty_out)
```

Where detail_value now properly includes all discounts and additions.

## Notes

- The current system already stores `detail_value` in `operation_items` table
- The issue is ensuring `detail_value` is calculated correctly to include all discounts and additions
- The average cost calculation formula remains unchanged - it uses `detail_value`
- The focus is on ensuring `detail_value` is accurate when invoices are created/modified/deleted
