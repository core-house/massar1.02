# MASSAR ERP System

## Table of Contents

1. [Discount and Additional Handling](#discount-and-additional-handling)
2. [Average Cost Recalculation System](#average-cost-recalculation-system)

---

## Discount and Additional Handling

### Overview

The Discount and Additional Handling feature ensures accurate calculation of inventory costs by properly accounting for all discounts and additional charges at both item and invoice levels. This feature calculates the `detail_value` used in average cost calculations.

### Key Features

- **Item-Level Discounts**: Apply discounts directly to individual items
- **Item-Level Additions**: Add charges (shipping, handling) to specific items
- **Invoice-Level Discounts**: Distribute invoice discounts proportionally across all items
- **Invoice-Level Additions**: Distribute invoice additional charges proportionally across all items
- **Automatic Calculation**: Server-side calculation ensures accuracy
- **Validation**: Comprehensive validation prevents invalid data
- **Historical Data Fix**: Command to recalculate existing invoices
- **Audit Trail**: Complete logging of all calculations

### How It Works

#### Detail Value Calculation

The `detail_value` is the final value of an invoice item after applying all discounts and additions:

```
detail_value = (item_price × quantity) - item_discount + item_additional
               - distributed_invoice_discount + distributed_invoice_additional
```

#### Distribution Formula

Invoice-level discounts and additions are distributed proportionally:

```
item_ratio = item_subtotal / invoice_subtotal
distributed_amount = invoice_amount × item_ratio
```

### Usage Examples

#### Creating an Invoice with Discounts

```php
// Invoice data
$invoiceData = [
    'fat_disc' => 1500,      // Invoice discount: 1,500 EGP
    'fat_disc_per' => 0,     // Or use percentage
    'fat_plus' => 200,       // Invoice additional: 200 EGP
    'fat_plus_per' => 0,     // Or use percentage
];

// Items
$items = [
    [
        'item_price' => 1000,
        'quantity' => 10,
        'item_discount' => 100,  // Item discount: 100 EGP
        'additional' => 50,      // Item additional: 50 EGP
    ],
    // ... more items
];

// SaveInvoiceService automatically calculates detail_value
$service = new SaveInvoiceService(
    new DetailValueCalculator(),
    new DetailValueValidator()
);

$operationId = $service->saveInvoice($component);
```

#### Verifying Calculations

```php
use App\Services\Invoice\DetailValueCalculator;

$calculator = new DetailValueCalculator();

// Calculate invoice subtotal
$invoiceSubtotal = $calculator->calculateInvoiceSubtotal($items);

// Calculate detail_value for each item
foreach ($items as $item) {
    $result = $calculator->calculate($item, $invoiceData, $invoiceSubtotal);
    
    echo "Detail Value: " . $result['detail_value'] . "\n";
    echo "Distributed Discount: " . $result['distributed_discount'] . "\n";
    echo "Distributed Additional: " . $result['distributed_additional'] . "\n";
}
```

### Artisan Commands

#### Fix Historical Data

Recalculate detail_value for existing invoices:

```bash
# Preview changes (dry run)
php artisan recalculation:fix-detail-values --all --dry-run

# Fix all invoices
php artisan recalculation:fix-detail-values --all

# Fix specific invoice
php artisan recalculation:fix-detail-values --invoice-id=12345

# Fix date range
php artisan recalculation:fix-detail-values --from-date=2024-01-01 --to-date=2024-12-31

# Fix specific operation type
php artisan recalculation:fix-detail-values --operation-type=11 --all

# Adjust batch size
php artisan recalculation:fix-detail-values --all --batch-size=50
```

**Command Options:**
- `--invoice-id`: Fix specific invoice
- `--from-date`: Fix invoices from date (YYYY-MM-DD)
- `--to-date`: Fix invoices until date (YYYY-MM-DD)
- `--operation-type`: Fix specific type (11=purchase, 12=sales, etc.)
- `--all`: Fix all invoices
- `--dry-run`: Preview changes without saving
- `--batch-size`: Number of invoices per batch (default: 100)
- `--force`: Skip confirmation prompt

### Impact on Invoice Types

#### Purchase Invoices (Type 11)
- Detail value includes all discounts and additions
- Average cost is recalculated immediately
- Affects future sales profit calculations

**Example:**
```
Item Price: 1,000 EGP × 10 units = 10,000 EGP
Item Discount: -100 EGP
Invoice Discount (distributed): -1,000 EGP
Detail Value: 8,900 EGP
Average Cost: Updated based on detail_value
```

#### Sales Invoices (Type 10)
- Uses current average cost for profit calculation
- Detail value used for revenue calculation
- Does NOT change average cost

**Example:**
```
Item Price: 1,500 EGP × 10 units = 15,000 EGP
Item Discount: -200 EGP
Invoice Discount (distributed): -1,500 EGP
Detail Value: 13,300 EGP (revenue)
Cost: 10 × average_cost (from inventory)
Profit: 13,300 - (10 × average_cost)
```

#### Purchase Returns (Type 12)
- Detail value is negative (reduces inventory value)
- Average cost is recalculated
- Reverses the effect of the original purchase

#### Sales Returns (Type 13)
- Restores inventory at original sales cost
- Average cost is recalculated
- Reverses the effect of the original sale

### Validation Rules

The system validates all calculations to ensure data integrity:

1. **Non-negativity**: Detail value cannot be negative
2. **Reasonableness**: Detail value must be within reasonable bounds (0 to 10x base value)
3. **Accuracy**: Calculation must match formula within 0.01 tolerance

**Example Validation Error:**
```
Detail value cannot be negative. Got: -100.00
```

### Logging and Audit Trail

All calculations are logged for audit purposes:

```php
// Example log entry
[2024-12-23 10:30:00] INFO: Detail value calculated for item
{
    "operation_id": 12345,
    "operation_type": 11,
    "item_id": 123,
    "item_index": 0,
    "quantity": 10,
    "original_sub_value": 10000,
    "calculated_detail_value": 8900,
    "item_discount": 100,
    "distributed_invoice_discount": 1000,
    "distributed_invoice_additional": 0
}
```

### Performance

- **Invoice Creation**: +5-10ms per invoice (negligible impact)
- **Historical Data Fix**: ~1000 invoices per minute
- **Memory Usage**: Minimal increase (<10MB)

### Documentation

For detailed information, see:

- **User Guide**: `.kiro/specs/discount-additional-handling/USER_GUIDE.md`
  - How discounts affect average cost
  - How to verify calculations
  - How to fix historical data
  - Troubleshooting guide

- **Developer Guide**: `.kiro/specs/discount-additional-handling/DEVELOPER_GUIDE.md`
  - API documentation
  - Calculation formulas
  - Testing guide
  - Deployment guide

- **Requirements**: `.kiro/specs/discount-additional-handling/requirements.md`
- **Design**: `.kiro/specs/discount-additional-handling/design.md`

### FAQ

**Q: Will this affect existing invoices?**  
A: No, existing invoices are not automatically changed. Run the recalculation command to fix historical data.

**Q: How do I know if my invoices need recalculation?**  
A: Run the command with `--dry-run` flag to preview changes without saving.

**Q: What happens if I have both fixed amount and percentage discounts?**  
A: Fixed amount takes precedence. If both are specified, only the fixed amount is used.

**Q: Does this slow down invoice creation?**  
A: No, the performance impact is minimal (<10ms per invoice).

**Q: Can I recalculate specific items only?**  
A: No, the command works at the invoice level. All items in selected invoices will be recalculated.

### API Reference

See PHPDoc blocks in the following classes for detailed API documentation:

- `App\Services\Invoice\DetailValueCalculator` - Calculate detail_value with discounts/additions
- `App\Services\Invoice\DetailValueValidator` - Validate calculated detail_value
- `App\Services\SaveInvoiceService` - Save invoices with accurate calculations
- `App\Console\Commands\RecalculateDetailValuesCommand` - Fix historical data

---

## Average Cost Recalculation System

### Overview

The Average Cost Recalculation System is a high-performance, hybrid solution for calculating and maintaining accurate average costs for inventory items. It automatically selects the optimal strategy based on data size and configuration.

### Features

- **Automatic Strategy Selection**: Chooses between Queue Jobs, Stored Procedures, or PHP Services based on data size
- **Performance Monitoring**: Tracks execution time, memory usage, and operation metrics
- **Input Validation**: Comprehensive validation of all inputs before processing
- **Manufacturing Chain Support**: Handles cascading recalculation for manufacturing invoices
- **Consistency Checking**: Verifies and fixes data integrity issues
- **Configurable Thresholds**: Tune the system for different environments
- **Error Handling**: Robust error handling with detailed logging

### Architecture

```
RecalculationServiceHelper (Unified Interface)
    ├── Queue Jobs (>5000 items)
    ├── Stored Procedures (>1000 items, if enabled)
    └── PHP Optimized Service (<1000 items)
```

### Installation

The system is already integrated into the MASSAR ERP application. No additional installation is required.

### Configuration

Configuration is managed through `config/recalculation.php`:

```php
return [
    // Batch processing
    'batch_size' => 100,
    'chunk_size' => 500,
    
    // Strategy selection thresholds
    'stored_procedure_threshold' => 1000,
    'queue_threshold' => 5000,
    
    // Feature flags
    'use_stored_procedures' => false,
    'use_queue' => true,
    
    // Performance monitoring
    'performance_warning_threshold' => 30.0,
    
    // Manufacturing chain
    'manufacturing_chain_enabled' => true,
    'manufacturing_cost_allocation' => 'proportional',
    
    // Consistency checking
    'consistency_check_enabled' => true,
    'consistency_tolerance' => 0.01,
];
```

### Usage Examples

#### Basic Recalculation

```php
use App\Services\RecalculationServiceHelper;

// Recalculate average cost for specific items from a date
RecalculationServiceHelper::recalculateAverageCost(
    [1, 2, 3],           // Item IDs
    '2024-01-01',        // From date
    false,               // Force queue
    false                // Is delete
);
```

#### Invoice Creation/Modification

```php
// When creating or modifying an invoice
RecalculationServiceHelper::recalculateAll(
    $affectedItemIds,
    $invoiceDate,
    false,               // Force queue
    $currentInvoiceId,
    $currentInvoiceCreatedAt
);
```

#### Invoice Deletion

```php
// When deleting an invoice, recalculate from ALL operations
RecalculationServiceHelper::recalculateAverageCost(
    $affectedItemIds,
    null,                // From date (ignored for delete)
    false,               // Force queue
    true                 // Is delete (recalculates from all operations)
);
```

#### Manufacturing Chain Recalculation

```php
// When purchase invoice affecting raw materials is modified/deleted
RecalculationServiceHelper::recalculateManufacturingChain(
    $rawMaterialItemIds,
    $fromDate
);
```

#### Force Queue Processing

```php
// Force use of queue job for large datasets
RecalculationServiceHelper::recalculateAverageCost(
    $itemIds,
    $fromDate,
    true,                // Force queue
    false
);
```

### Calculation Formula

```
average_cost = SUM(detail_value) / SUM(qty_in - qty_out)
```

Where:
- `detail_value`: Total value of the operation
- `qty_in`: Quantity received (purchases, manufacturing output)
- `qty_out`: Quantity issued (sales, manufacturing input)

### Filters Applied

The calculation includes only:
- Stock operations (`is_stock = 1`)
- Specific operation types: 11 (Purchase), 12 (Sales), 20 (Opening Balance), 59 (Manufacturing)
- Non-deleted operations (`isdeleted = 0`)
- Operations on or after the specified date (when not a delete operation)

### Artisan Commands

#### Check Consistency

```bash
# Check specific items
php artisan recalculation:check-consistency --items=1,2,3

# Check all items
php artisan recalculation:check-consistency --all

# Check with custom tolerance
php artisan recalculation:check-consistency --all --tolerance=0.05
```

#### Fix Inconsistencies

```bash
# Dry run (preview changes)
php artisan recalculation:fix-inconsistencies --items=1,2,3 --dry-run

# Fix specific items
php artisan recalculation:fix-inconsistencies --items=1,2,3

# Fix all inconsistent items
php artisan recalculation:fix-inconsistencies --all
```

### Performance Optimization

#### Strategy Selection

The system automatically selects the optimal strategy:

1. **Queue Jobs** (>5000 items or >500,000 operations)
   - Processes in background
   - Prevents blocking main application
   - Automatic retry on failure

2. **Stored Procedures** (>1000 items, if enabled)
   - Database-level processing
   - Fastest for large datasets
   - Requires stored procedures to be installed

3. **PHP Optimized Service** (<1000 items)
   - Single SQL query per item
   - Batch processing (100 items per batch)
   - Best for small/medium datasets

#### Batch Processing

Items are processed in batches to optimize performance:

```php
// Batch size is configurable
'batch_size' => 100,  // Process 100 items at a time
```

### Monitoring

#### Performance Metrics

The system tracks:
- Execution time
- Memory usage
- Items processed
- Strategy selected
- Errors and warnings

#### Logging

All operations are logged with structured data:

```php
Log::info('Recalculation operation completed', [
    'operation_id' => $operationId,
    'operation_type' => 'batch_recalculation',
    'duration_seconds' => 12.5,
    'memory_used_mb' => 45.2,
    'items_processed' => 150,
    'strategy' => 'php_optimized',
]);
```

### Error Handling

#### Validation Errors

```php
try {
    RecalculationServiceHelper::recalculateAverageCost($itemIds, $fromDate);
} catch (InvalidArgumentException $e) {
    // Handle validation error
    Log::error('Invalid parameters', ['error' => $e->getMessage()]);
}
```

#### Runtime Errors

```php
try {
    RecalculationServiceHelper::recalculateAverageCost($itemIds, $fromDate);
} catch (RuntimeException $e) {
    // Handle runtime error (database, calculation, etc.)
    Log::error('Recalculation failed', ['error' => $e->getMessage()]);
}
```

### Troubleshooting

#### Issue: Slow Recalculation

**Symptoms**: Recalculation takes longer than expected

**Solutions**:
1. Check if stored procedures are enabled and installed
2. Verify database indexes are in place
3. Consider using queue for large datasets
4. Review performance logs for bottlenecks

```bash
# Check performance logs
tail -f storage/logs/laravel.log | grep "Recalculation operation"
```

#### Issue: Inconsistent Average Costs

**Symptoms**: Stored average_cost doesn't match calculated value

**Solutions**:
1. Run consistency check to identify issues
2. Fix inconsistencies using artisan command
3. Verify operation data integrity

```bash
# Check and fix
php artisan recalculation:check-consistency --all
php artisan recalculation:fix-inconsistencies --all
```

#### Issue: Queue Jobs Not Processing

**Symptoms**: Queue jobs are dispatched but not executed

**Solutions**:
1. Verify queue worker is running
2. Check queue configuration
3. Review queue logs for errors

```bash
# Start queue worker
php artisan queue:work --queue=recalculation,recalculation-large

# Check failed jobs
php artisan queue:failed
```

#### Issue: Manufacturing Chain Not Updating

**Symptoms**: Product costs not updated when raw materials change

**Solutions**:
1. Verify manufacturing chain is enabled in config
2. Check manufacturing operation types configuration
3. Review logs for manufacturing chain operations

```php
// In config/recalculation.php
'manufacturing_chain_enabled' => true,
'manufacturing_operation_types' => [59],
```

### Testing

#### Unit Tests

```bash
# Run all recalculation tests
php artisan test --filter=Recalculation

# Run specific test class
php artisan test tests/Unit/Services/RecalculationServiceHelperTest.php
```

#### Property-Based Tests

```bash
# Run property tests
php artisan test --filter=Property
```

#### Integration Tests

```bash
# Run integration tests
php artisan test tests/Feature/ManufacturingChainIntegrationTest.php
```

### Best Practices

1. **Always validate inputs** before calling recalculation methods
2. **Use appropriate strategy** based on data size
3. **Monitor performance** regularly using logs
4. **Run consistency checks** periodically
5. **Test thoroughly** before deploying changes
6. **Use queue for large datasets** to avoid timeouts
7. **Enable stored procedures** for production environments with large data

### API Reference

See PHPDoc blocks in the following classes for detailed API documentation:

- `App\Services\RecalculationServiceHelper` - Main entry point
- `App\Services\RecalculationServiceFactory` - Strategy selection
- `App\Services\AverageCostRecalculationServiceOptimized` - PHP implementation
- `App\Services\Manufacturing\ManufacturingChainHandler` - Manufacturing chain
- `App\Services\Consistency\AverageCostConsistencyChecker` - Consistency checking
- `App\Services\Config\RecalculationConfigManager` - Configuration management
- `App\Services\Monitoring\RecalculationPerformanceMonitor` - Performance monitoring
- `App\Services\Validation\RecalculationInputValidator` - Input validation

### Support

For issues or questions, please:
1. Check the troubleshooting section above
2. Review the logs in `storage/logs/laravel.log`
3. Contact the development team

### License

This system is part of the MASSAR ERP application and is proprietary software.
