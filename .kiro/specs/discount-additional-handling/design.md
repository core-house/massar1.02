# Design Document: Discount and Additional Handling in Average Cost Calculation

## Overview

This document describes the design for properly handling discounts and additional charges at both item level and invoice level when calculating average costs for inventory items. The system will ensure that `detail_value` (used in average cost calculation) accurately reflects all discounts and additions.

## Architecture

### Current System Flow

```
Invoice Creation (Frontend)
    ↓
SaveInvoiceService
    ↓
detail_value = $invoiceItem['sub_value'] (from frontend)
    ↓
OperationItems::create(['detail_value' => $subValue])
    ↓
RecalculationServiceHelper::recalculateAverageCost()
    ↓
average_cost = SUM(detail_value) / SUM(qty_in - qty_out)
```

### Improved System Flow

```
Invoice Creation (Frontend)
    ↓
SaveInvoiceService
    ↓
DetailValueCalculator::calculate()
    ├─ Calculate item subtotal
    ├─ Apply item-level discount
    ├─ Apply item-level additional
    ├─ Distribute invoice-level discount
    ├─ Distribute invoice-level additional
    └─ Return accurate detail_value
    ↓
DetailValueValidator::validate()
    ↓
OperationItems::create(['detail_value' => $calculatedValue])
    ↓
RecalculationServiceHelper::recalculateAverageCost()
    ↓
average_cost = SUM(detail_value) / SUM(qty_in - qty_out)
```

## Components and Interfaces

### 1. DetailValueCalculator (NEW)

**Purpose**: Calculate accurate `detail_value` including all discounts and additions.

**Interface**:
```php
class DetailValueCalculator
{
    /**
     * Calculate detail_value for an item with distributed invoice discounts/additions.
     *
     * @param array $itemData Item data (price, quantity, discount, additional)
     * @param array $invoiceData Invoice data (fat_disc, fat_disc_per, fat_plus, fat_plus_per)
     * @param float $invoiceSubtotal Total invoice value before invoice-level discount/additional
     * @return array Calculation results with detail_value and breakdown
     * @throws InvalidArgumentException if data is invalid
     */
    public function calculate(array $itemData, array $invoiceData, float $invoiceSubtotal): array;

    /**
     * Calculate invoice subtotal from items (before invoice-level discount/additional).
     *
     * @param array $items Array of items with price, quantity, discount, additional
     * @return float Invoice subtotal
     */
    public function calculateInvoiceSubtotal(array $items): float;

    /**
     * Distribute invoice-level discount across items proportionally.
     *
     * @param float $itemSubtotal Item's subtotal value
     * @param float $invoiceSubtotal Total invoice subtotal
     * @param array $invoiceData Invoice discount data
     * @return float Distributed discount amount
     */
    private function distributeInvoiceDiscount(
        float $itemSubtotal, 
        float $invoiceSubtotal, 
        array $invoiceData
    ): float;

    /**
     * Distribute invoice-level additional charges across items proportionally.
     *
     * @param float $itemSubtotal Item's subtotal value
     * @param float $invoiceSubtotal Total invoice subtotal
     * @param array $invoiceData Invoice additional data
     * @return float Distributed additional amount
     */
    private function distributeInvoiceAdditional(
        float $itemSubtotal, 
        float $invoiceSubtotal, 
        array $invoiceData
    ): float;
}
```

### 2. DetailValueValidator (NEW)

**Purpose**: Validate calculated `detail_value` for correctness and reasonableness.

**Interface**:
```php
class DetailValueValidator
{
    /**
     * Validate calculated detail_value.
     *
     * @param float $detailValue Calculated detail value
     * @param array $itemData Original item data
     * @param array $calculation Calculation breakdown
     * @throws InvalidArgumentException if validation fails
     * @return void
     */
    public function validate(float $detailValue, array $itemData, array $calculation): void;

    /**
     * Check if detail_value is within reasonable bounds.
     *
     * @param float $detailValue Calculated value
     * @param float $itemPrice Item price
     * @param float $quantity Item quantity
     * @return bool True if reasonable
     */
    private function isReasonable(float $detailValue, float $itemPrice, float $quantity): bool;

    /**
     * Verify calculation accuracy.
     *
     * @param float $detailValue Calculated value
     * @param array $calculation Calculation breakdown
     * @return bool True if accurate
     */
    private function verifyCalculation(float $detailValue, array $calculation): bool;
}
```

### 3. Enhanced SaveInvoiceService

**Improvements**:
- Inject `DetailValueCalculator` and `DetailValueValidator`
- Calculate `detail_value` server-side instead of using frontend value
- Add comprehensive logging for audit trail
- Validate all calculations before saving

**Updated Interface**:
```php
class SaveInvoiceService
{
    private DetailValueCalculator $detailValueCalculator;
    private DetailValueValidator $detailValueValidator;

    public function __construct(
        DetailValueCalculator $detailValueCalculator,
        DetailValueValidator $detailValueValidator
    ) {
        $this->detailValueCalculator = $detailValueCalculator;
        $this->detailValueValidator = $detailValueValidator;
    }

    /**
     * Save invoice with accurate detail_value calculation.
     *
     * @param object $component Invoice component data
     * @param bool $isEdit Whether this is an edit operation
     * @return void
     * @throws InvalidArgumentException if validation fails
     * @throws RuntimeException if save fails
     */
    public function saveInvoice($component, $isEdit = false): void;

    /**
     * Calculate and validate detail_value for invoice items.
     *
     * @param array $items Invoice items
     * @param array $invoiceData Invoice-level data
     * @return array Items with calculated detail_value
     */
    private function calculateItemDetailValues(array $items, array $invoiceData): array;
}
```

### 4. RecalculateDetailValuesCommand (NEW)

**Purpose**: Fix historical data with incorrect `detail_value`.

**Interface**:
```php
class RecalculateDetailValuesCommand extends Command
{
    protected $signature = 'recalculation:fix-detail-values 
                            {--invoice-id= : Specific invoice ID to fix}
                            {--from-date= : Fix invoices from this date}
                            {--to-date= : Fix invoices until this date}
                            {--operation-type= : Fix specific operation type}
                            {--dry-run : Preview changes without saving}
                            {--batch-size=100 : Number of invoices per batch}';

    protected $description = 'Recalculate detail_value for operation items';

    /**
     * Execute the command.
     *
     * @param DetailValueCalculator $calculator
     * @return int Exit code
     */
    public function handle(DetailValueCalculator $calculator): int;

    /**
     * Recalculate detail_value for a single invoice.
     *
     * @param OperHead $invoice
     * @param DetailValueCalculator $calculator
     * @param bool $dryRun
     * @return array Results with fixed count
     */
    private function recalculateInvoice(
        OperHead $invoice, 
        DetailValueCalculator $calculator, 
        bool $dryRun
    ): array;
}
```

## Data Models

### Existing Models (No Schema Changes)

The system uses existing database schema:

#### operation_items table
```sql
- item_price: decimal(10,2) - Unit price
- qty_in: decimal(10,2) - Quantity in (purchases)
- qty_out: decimal(10,2) - Quantity out (sales)
- item_discount: decimal(10,2) - Item-level discount
- additional: decimal(10,2) - Item-level additional
- detail_value: decimal(10,2) - Final value (WILL BE RECALCULATED)
```

#### operhead table
```sql
- fat_total: decimal(15,2) - Invoice total
- fat_disc: decimal(15,2) - Invoice discount (value)
- fat_disc_per: decimal(5,2) - Invoice discount (percentage)
- fat_plus: decimal(15,2) - Invoice additional (value)
- fat_plus_per: decimal(5,2) - Invoice additional (percentage)
- fat_net: decimal(15,2) - Net invoice value
```

**Note**: No database schema changes required. We only change how `detail_value` is calculated.

## Calculation Formulas

### 1. Item Subtotal (Before Invoice-Level Adjustments)

```
item_subtotal = (item_price × quantity) - item_discount + item_additional
```

### 2. Invoice Subtotal (Sum of All Items)

```
invoice_subtotal = Σ(item_subtotal for all items)
```

### 3. Item Ratio (For Distribution)

```
item_ratio = item_subtotal / invoice_subtotal
```

### 4. Distributed Invoice Discount

**Option A: Fixed Amount**
```
distributed_discount = fat_disc × item_ratio
```

**Option B: Percentage**
```
distributed_discount = item_subtotal × (fat_disc_per / 100)
```

### 5. Distributed Invoice Additional

**Option A: Fixed Amount**
```
distributed_additional = fat_plus × item_ratio
```

**Option B: Percentage**
```
distributed_additional = item_subtotal × (fat_plus_per / 100)
```

### 6. Final Detail Value

```
detail_value = item_subtotal - distributed_discount + distributed_additional
detail_value = max(0, detail_value)  // Cannot be negative
```

### 7. Average Cost (Unchanged)

```
average_cost = SUM(detail_value) / SUM(qty_in - qty_out)
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do.*

### Property 1: Detail Value Non-Negativity
*For any* item with valid price and quantity, the calculated detail_value should never be negative.
**Validates: Requirements 4.4, 8.2**

### Property 2: Invoice Discount Distribution Sum
*For any* invoice with fat_disc, the sum of all distributed_discount values across items should equal fat_disc (within rounding tolerance).
**Validates: Requirements 2.1, 2.2**

### Property 3: Invoice Additional Distribution Sum
*For any* invoice with fat_plus, the sum of all distributed_additional values across items should equal fat_plus (within rounding tolerance).
**Validates: Requirements 3.1, 3.2**

### Property 4: Detail Value Calculation Accuracy
*For any* item, the detail_value should equal: (item_price × quantity) - item_discount + item_additional - distributed_discount + distributed_additional (within rounding tolerance).
**Validates: Requirements 1.3, 8.2**

### Property 5: Proportional Distribution
*For any* two items in the same invoice, the ratio of their distributed discounts should equal the ratio of their subtotals.
**Validates: Requirements 2.1, 3.1**

### Property 6: Zero Discount Handling
*For any* invoice with zero discounts and additions, detail_value should equal (item_price × quantity).
**Validates: Requirements 10.1**

### Property 7: Percentage Discount Accuracy
*For any* invoice with fat_disc_per, each item's distributed discount should equal item_subtotal × (fat_disc_per / 100).
**Validates: Requirements 2.3**

### Property 8: Average Cost Consistency
*For any* set of operations, recalculating average cost using the new detail_value should produce consistent results.
**Validates: Requirements 4.5, 10.4**

### Property 9: Validation Rejection
*For any* invalid detail_value (negative or unreasonable), the validator should reject it with a descriptive exception.
**Validates: Requirements 8.1, 8.3**

### Property 10: Audit Trail Completeness
*For any* invoice saved, the system should log all calculation details including original values, discounts, additions, and final detail_value.
**Validates: Requirements 9.1, 9.2, 9.3**

## Error Handling

### Error Categories

1. **Validation Errors** (InvalidArgumentException)
   - Negative detail_value
   - Invalid item data (missing price, quantity)
   - Invalid invoice data (negative discounts)
   - Calculation mismatch

2. **Calculation Errors** (RuntimeException)
   - Division by zero (invoice_subtotal = 0)
   - Overflow in calculations
   - Rounding errors exceeding tolerance

3. **Data Integrity Errors** (RuntimeException)
   - Missing invoice data
   - Missing item data
   - Inconsistent database state

### Error Handling Strategy

```php
try {
    // Calculate invoice subtotal
    $invoiceSubtotal = $this->detailValueCalculator->calculateInvoiceSubtotal($items);
    
    // Calculate detail_value for each item
    foreach ($items as $item) {
        $calculation = $this->detailValueCalculator->calculate(
            $item,
            $invoiceData,
            $invoiceSubtotal
        );
        
        // Validate calculation
        $this->detailValueValidator->validate(
            $calculation['detail_value'],
            $item,
            $calculation
        );
        
        // Log for audit
        Log::info('Detail value calculated', [
            'item_id' => $item['item_id'],
            'calculation' => $calculation,
        ]);
    }
    
} catch (InvalidArgumentException $e) {
    Log::error('Validation error in detail value calculation', [
        'error' => $e->getMessage(),
        'item_data' => $item ?? null,
        'invoice_data' => $invoiceData ?? null,
    ]);
    throw $e;
    
} catch (RuntimeException $e) {
    Log::error('Runtime error in detail value calculation', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    throw $e;
}
```

## Testing Strategy

### Unit Tests

1. **DetailValueCalculator Tests**
   - Test item subtotal calculation
   - Test invoice subtotal calculation
   - Test discount distribution (fixed amount)
   - Test discount distribution (percentage)
   - Test additional distribution (fixed amount)
   - Test additional distribution (percentage)
   - Test combined discounts and additions
   - Test edge cases (zero values, single item)

2. **DetailValueValidator Tests**
   - Test negative value rejection
   - Test unreasonable value detection
   - Test calculation accuracy verification
   - Test tolerance handling

3. **SaveInvoiceService Tests**
   - Test detail_value calculation integration
   - Test validation integration
   - Test logging
   - Test error handling

### Property-Based Tests

1. **Distribution Sum Property** (Property 2, 3)
   - Generate random invoices with multiple items
   - Verify sum of distributed amounts equals invoice amount

2. **Proportional Distribution Property** (Property 5)
   - Generate random items with different values
   - Verify distribution is proportional to item values

3. **Non-Negativity Property** (Property 1)
   - Generate random valid item data
   - Verify detail_value is never negative

4. **Calculation Accuracy Property** (Property 4)
   - Generate random item and invoice data
   - Verify detail_value matches formula

### Integration Tests

1. **Purchase Invoice Flow**
   - Create purchase invoice with discounts
   - Verify detail_value is calculated correctly
   - Verify average cost is recalculated
   - Verify values in database

2. **Sales Invoice Flow**
   - Create sales invoice with discounts
   - Verify detail_value uses correct formula
   - Verify profit calculation
   - Verify average cost is not changed

3. **Invoice Modification Flow**
   - Modify invoice discounts
   - Verify detail_value is recalculated
   - Verify average cost is updated

4. **Historical Data Fix Flow**
   - Run fix command on test data
   - Verify incorrect values are fixed
   - Verify correct values are unchanged
   - Verify dry-run doesn't modify data

## Implementation Notes

### Phase 1: Core Services (2-3 days)
1. Create `DetailValueCalculator` service
2. Create `DetailValueValidator` service
3. Write comprehensive unit tests
4. Write property-based tests

### Phase 2: Integration (2 days)
1. Update `SaveInvoiceService` to use new calculator
2. Add validation calls
3. Add comprehensive logging
4. Write integration tests

### Phase 3: Historical Data Fix (1 day)
1. Create `RecalculateDetailValuesCommand`
2. Test on sample data
3. Create backup before running on production

### Phase 4: Testing & Validation (2 days)
1. Run all tests
2. Manual testing of various scenarios
3. Performance testing
4. Code review

### Phase 5: Deployment (1 day)
1. Deploy to staging
2. Run historical data fix
3. Verify results
4. Deploy to production
5. Monitor logs

## Performance Considerations

### Optimization Strategies

1. **Batch Processing**: Process invoices in batches when fixing historical data
2. **Caching**: Cache invoice subtotal to avoid recalculation
3. **Database Transactions**: Use transactions for data consistency
4. **Logging**: Use structured logging for better performance
5. **Validation**: Validate once per invoice, not per item

### Expected Performance Impact

- **Invoice Creation**: +5-10ms per invoice (negligible)
- **Historical Data Fix**: ~1000 invoices per minute
- **Memory Usage**: Minimal increase (<10MB)

## Backward Compatibility

### Compatibility Measures

1. **No Schema Changes**: Uses existing database structure
2. **Gradual Rollout**: Can be enabled per operation type
3. **Fallback**: Can fall back to frontend calculation if needed
4. **Data Migration**: Command to fix historical data
5. **Testing**: Comprehensive tests ensure no breaking changes

### Migration Strategy

1. Deploy new code (calculator disabled)
2. Test on staging environment
3. Enable calculator for new invoices
4. Run historical data fix command
5. Verify results
6. Monitor for issues

## Security Considerations

1. **Input Validation**: Validate all input data
2. **SQL Injection**: Use parameterized queries
3. **Authorization**: Check user permissions before fixing data
4. **Audit Trail**: Log all calculations and changes
5. **Data Integrity**: Use transactions for consistency

## Monitoring and Alerting

### Metrics to Track

1. **Calculation Errors**: Count of validation failures
2. **Performance**: Average calculation time
3. **Data Quality**: Count of fixed invoices
4. **Audit Trail**: Log volume and completeness

### Alerts

1. **High Error Rate**: Alert if >5% of calculations fail
2. **Performance Degradation**: Alert if calculation time >100ms
3. **Data Inconsistency**: Alert if many invoices need fixing

## Documentation

### User Documentation

1. How discounts and additions affect average cost
2. How to verify detail_value calculations
3. How to fix historical data
4. Troubleshooting guide

### Developer Documentation

1. API documentation for new services
2. Calculation formula documentation
3. Testing guide
4. Deployment guide
