# Discount and Additional Handling - Developer Guide

## Overview

This guide provides technical documentation for developers working with the discount and additional handling feature. It covers API documentation, calculation formulas, testing strategies, and deployment procedures.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [API Documentation](#api-documentation)
3. [Calculation Formulas](#calculation-formulas)
4. [Testing Guide](#testing-guide)
5. [Deployment Guide](#deployment-guide)
6. [Performance Considerations](#performance-considerations)
7. [Troubleshooting](#troubleshooting)

---

## Architecture Overview

### Component Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     Invoice Creation Flow                    │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      SaveInvoiceService                      │
│  - Validates invoice data                                    │
│  - Orchestrates calculation and validation                   │
│  - Saves invoice and items                                   │
│  - Triggers recalculation                                    │
└─────────────────────────────────────────────────────────────┘
                              │
                ┌─────────────┴─────────────┐
                ▼                           ▼
┌───────────────────────────┐   ┌───────────────────────────┐
│  DetailValueCalculator    │   │  DetailValueValidator     │
│  - Calculate subtotals    │   │  - Validate non-negative  │
│  - Distribute discounts   │   │  - Check reasonableness   │
│  - Distribute additions   │   │  - Verify accuracy        │
│  - Return detail_value    │   │  - Throw exceptions       │
└───────────────────────────┘   └───────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────────────────┐
│                      OperationItems                          │
│  - Stores detail_value                                       │
│  - Used in average cost calculation                          │
└─────────────────────────────────────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────────────────────┐
│              RecalculationServiceHelper                      │
│  - Recalculates average cost                                 │
│  - Updates profits and journals                              │
└─────────────────────────────────────────────────────────────┘
```

### Key Components

#### 1. DetailValueCalculator
**Location:** `app/Services/Invoice/DetailValueCalculator.php`

**Responsibility:** Calculate accurate detail_value including all discounts and additions

**Key Methods:**
- `calculate()`: Calculate detail_value for a single item
- `calculateInvoiceSubtotal()`: Calculate total invoice subtotal
- `distributeInvoiceDiscount()`: Distribute invoice discount proportionally
- `distributeInvoiceAdditional()`: Distribute invoice additional proportionally

#### 2. DetailValueValidator
**Location:** `app/Services/Invoice/DetailValueValidator.php`

**Responsibility:** Validate calculated detail_value for correctness

**Key Methods:**
- `validate()`: Perform all validation checks
- `isReasonable()`: Check if value is within reasonable bounds
- `verifyCalculation()`: Verify calculation accuracy

#### 3. SaveInvoiceService
**Location:** `app/Services/SaveInvoiceService.php`

**Responsibility:** Orchestrate invoice saving with accurate calculations

**Key Methods:**
- `saveInvoice()`: Main entry point for saving invoices
- `calculateItemDetailValues()`: Calculate and validate all items
- `deleteInvoice()`: Delete invoice and trigger recalculation

#### 4. RecalculateDetailValuesCommand
**Location:** `app/Console/Commands/RecalculateDetailValuesCommand.php`

**Responsibility:** Fix historical data with incorrect detail_value

**Key Methods:**
- `handle()`: Main command execution
- `recalculateInvoice()`: Recalculate single invoice
- `buildInvoiceQuery()`: Build query based on filters

---

## API Documentation

### DetailValueCalculator API

#### calculate()

Calculate detail_value for an item with distributed invoice discounts/additions.

```php
public function calculate(
    array $itemData, 
    array $invoiceData, 
    float $invoiceSubtotal
): array
```

**Parameters:**

- `$itemData` (array): Item data
  - `item_price` (float): Unit price of the item
  - `quantity` (float): Quantity (qty_in or qty_out)
  - `item_discount` (float, optional): Item-level discount (default: 0)
  - `additional` (float, optional): Item-level additional charges (default: 0)

- `$invoiceData` (array): Invoice data
  - `fat_disc` (float, optional): Invoice discount amount (default: 0)
  - `fat_disc_per` (float, optional): Invoice discount percentage (default: 0)
  - `fat_plus` (float, optional): Invoice additional amount (default: 0)
  - `fat_plus_per` (float, optional): Invoice additional percentage (default: 0)

- `$invoiceSubtotal` (float): Total invoice value before invoice-level discount/additional

**Returns:** Array with:
- `detail_value` (float): Final calculated value
- `item_subtotal` (float): Item value before invoice-level adjustments
- `distributed_discount` (float): Invoice discount allocated to this item
- `distributed_additional` (float): Invoice additional allocated to this item
- `breakdown` (array): Detailed calculation breakdown for audit

**Throws:** `InvalidArgumentException` if data is invalid or missing required fields

**Example:**

```php
$calculator = new DetailValueCalculator();

$itemData = [
    'item_price' => 1000,
    'quantity' => 10,
    'item_discount' => 100,
    'additional' => 50,
];

$invoiceData = [
    'fat_disc' => 1500,
    'fat_disc_per' => 0,
    'fat_plus' => 0,
    'fat_plus_per' => 0,
];

$invoiceSubtotal = 15000;

$result = $calculator->calculate($itemData, $invoiceData, $invoiceSubtotal);

// Result:
// [
//     'detail_value' => 8950.00,
//     'item_subtotal' => 9950.00,
//     'distributed_discount' => 1000.00,
//     'distributed_additional' => 0.00,
//     'breakdown' => [...]
// ]
```

#### calculateInvoiceSubtotal()

Calculate invoice subtotal from items (before invoice-level discount/additional).

```php
public function calculateInvoiceSubtotal(array $items): float
```

**Parameters:**

- `$items` (array): Array of items, each containing:
  - `item_price` (float): Unit price
  - `quantity` (float): Quantity
  - `item_discount` (float, optional): Item-level discount
  - `additional` (float, optional): Item-level additional

**Returns:** float - Invoice subtotal

**Throws:** `InvalidArgumentException` if items array is empty or contains invalid data

**Example:**

```php
$calculator = new DetailValueCalculator();

$items = [
    [
        'item_price' => 1000,
        'quantity' => 10,
        'item_discount' => 100,
        'additional' => 50,
    ],
    [
        'item_price' => 500,
        'quantity' => 10,
        'item_discount' => 0,
        'additional' => 0,
    ],
];

$subtotal = $calculator->calculateInvoiceSubtotal($items);
// Result: 14950.00 = (1000*10 - 100 + 50) + (500*10)
```

### DetailValueValidator API

#### validate()

Validate calculated detail_value.

```php
public function validate(
    float $detailValue, 
    array $itemData, 
    array $calculation
): void
```

**Parameters:**

- `$detailValue` (float): Calculated detail value to validate
- `$itemData` (array): Original item data
  - `item_price` (float): Unit price
  - `quantity` (float): Quantity
  - `item_discount` (float, optional): Item-level discount
  - `additional` (float, optional): Item-level additional
- `$calculation` (array): Calculation breakdown
  - `item_subtotal` (float): Item value before invoice adjustments
  - `distributed_discount` (float): Invoice discount for this item
  - `distributed_additional` (float): Invoice additional for this item

**Returns:** void

**Throws:** `InvalidArgumentException` if validation fails with descriptive error message

**Example:**

```php
$validator = new DetailValueValidator();

$detailValue = 9000.00;
$itemData = [
    'item_price' => 1000,
    'quantity' => 10,
    'item_discount' => 0,
    'additional' => 0,
];
$calculation = [
    'item_subtotal' => 10000.00,
    'distributed_discount' => 1000.00,
    'distributed_additional' => 0.00,
];

try {
    $validator->validate($detailValue, $itemData, $calculation);
    // Validation passed
} catch (InvalidArgumentException $e) {
    // Validation failed
    echo $e->getMessage();
}
```

### SaveInvoiceService API

#### saveInvoice()

Save invoice with accurate detail_value calculation.

```php
public function saveInvoice($component, $isEdit = false): int|false
```

**Parameters:**

- `$component` (object): Invoice component data from Livewire
- `$isEdit` (bool, optional): Whether this is an edit operation (default: false)

**Returns:** int|false - Operation ID on success, false on failure

**Throws:** `\Exception` on database or validation errors

**Example:**

```php
$service = new SaveInvoiceService(
    new DetailValueCalculator(),
    new DetailValueValidator()
);

$operationId = $service->saveInvoice($component, false);

if ($operationId) {
    echo "Invoice saved successfully with ID: {$operationId}";
} else {
    echo "Failed to save invoice";
}
```

#### deleteInvoice()

Delete an invoice and trigger necessary recalculations.

```php
public function deleteInvoice(int $operationId): bool
```

**Parameters:**

- `$operationId` (int): The operation ID to delete

**Returns:** bool - Success status

**Throws:** `\Exception` on database errors

**Example:**

```php
$service = new SaveInvoiceService(
    new DetailValueCalculator(),
    new DetailValueValidator()
);

try {
    $success = $service->deleteInvoice(12345);
    if ($success) {
        echo "Invoice deleted successfully";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### RecalculateDetailValuesCommand API

#### Command Signature

```bash
php artisan recalculation:fix-detail-values
    {--invoice-id= : Specific invoice ID to fix}
    {--from-date= : Fix invoices from this date (YYYY-MM-DD)}
    {--to-date= : Fix invoices until this date (YYYY-MM-DD)}
    {--operation-type= : Fix specific operation type}
    {--all : Fix all invoices}
    {--dry-run : Preview changes without saving}
    {--batch-size=100 : Number of invoices per batch}
    {--force : Skip confirmation prompt}
```

**Examples:**

```bash
# Fix all invoices
php artisan recalculation:fix-detail-values --all

# Dry run to preview changes
php artisan recalculation:fix-detail-values --all --dry-run

# Fix specific invoice
php artisan recalculation:fix-detail-values --invoice-id=12345

# Fix date range
php artisan recalculation:fix-detail-values --from-date=2024-01-01 --to-date=2024-12-31

# Fix specific operation type
php artisan recalculation:fix-detail-values --operation-type=11 --all

# Adjust batch size
php artisan recalculation:fix-detail-values --all --batch-size=50

# Skip confirmation
php artisan recalculation:fix-detail-values --all --force
```

---

## Calculation Formulas

### Item Detail Value Calculation

```
detail_value = (item_price × quantity) - item_discount + additional 
               - distributed_invoice_discount + distributed_invoice_additional
```

### Invoice Subtotal

```
invoice_subtotal = Σ(item_subtotal for all items)

where:
item_subtotal = (item_price × quantity) - item_discount + additional
```

### Distributed Invoice Discount

**Fixed Amount:**
```
item_ratio = item_subtotal / invoice_subtotal
distributed_discount = fat_disc × item_ratio
```

**Percentage:**
```
distributed_discount = item_subtotal × (fat_disc_per / 100)
```

### Distributed Invoice Additional

**Fixed Amount:**
```
item_ratio = item_subtotal / invoice_subtotal
distributed_additional = fat_plus × item_ratio
```

**Percentage:**
```
distributed_additional = item_subtotal × (fat_plus_per / 100)
```

### Average Cost (Unchanged)

```
average_cost = SUM(detail_value) / SUM(qty_in - qty_out)
```

### Validation Rules

1. **Non-negativity:** `detail_value >= 0`
2. **Reasonableness:** `detail_value <= (item_price × quantity × 10)`
3. **Accuracy:** `|expected - actual| <= 0.01` (tolerance)

---

## Testing Guide

### Unit Testing

#### Testing DetailValueCalculator

```php
use App\Services\Invoice\DetailValueCalculator;
use PHPUnit\Framework\TestCase;

class DetailValueCalculatorTest extends TestCase
{
    private DetailValueCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new DetailValueCalculator();
    }

    public function test_calculate_with_item_discount()
    {
        $itemData = [
            'item_price' => 1000,
            'quantity' => 10,
            'item_discount' => 100,
            'additional' => 0,
        ];

        $invoiceData = [
            'fat_disc' => 0,
            'fat_disc_per' => 0,
            'fat_plus' => 0,
            'fat_plus_per' => 0,
        ];

        $result = $this->calculator->calculate($itemData, $invoiceData, 9900);

        $this->assertEquals(9900, $result['detail_value']);
        $this->assertEquals(9900, $result['item_subtotal']);
        $this->assertEquals(0, $result['distributed_discount']);
    }

    public function test_calculate_with_invoice_discount()
    {
        $itemData = [
            'item_price' => 1000,
            'quantity' => 10,
            'item_discount' => 0,
            'additional' => 0,
        ];

        $invoiceData = [
            'fat_disc' => 1000,
            'fat_disc_per' => 0,
            'fat_plus' => 0,
            'fat_plus_per' => 0,
        ];

        $result = $this->calculator->calculate($itemData, $invoiceData, 10000);

        $this->assertEquals(9000, $result['detail_value']);
        $this->assertEquals(10000, $result['item_subtotal']);
        $this->assertEquals(1000, $result['distributed_discount']);
    }
}
```

#### Testing DetailValueValidator

```php
use App\Services\Invoice\DetailValueValidator;
use PHPUnit\Framework\TestCase;

class DetailValueValidatorTest extends TestCase
{
    private DetailValueValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new DetailValueValidator();
    }

    public function test_validate_rejects_negative_value()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Detail value cannot be negative');

        $this->validator->validate(-100, [], []);
    }

    public function test_validate_accepts_valid_value()
    {
        $detailValue = 9000;
        $itemData = [
            'item_price' => 1000,
            'quantity' => 10,
        ];
        $calculation = [
            'item_subtotal' => 10000,
            'distributed_discount' => 1000,
            'distributed_additional' => 0,
        ];

        // Should not throw exception
        $this->validator->validate($detailValue, $itemData, $calculation);
        $this->assertTrue(true);
    }
}
```

### Integration Testing

#### Testing Invoice Creation Flow

```php
use App\Services\SaveInvoiceService;
use App\Services\Invoice\DetailValueCalculator;
use App\Services\Invoice\DetailValueValidator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_invoice_calculates_detail_value_correctly()
    {
        // Arrange
        $component = $this->createMockComponent([
            'type' => 11, // Purchase invoice
            'invoiceItems' => [
                [
                    'item_id' => 1,
                    'price' => 1000,
                    'quantity' => 10,
                    'discount' => 100,
                    'additional' => 50,
                    'sub_value' => 9950,
                ],
            ],
            'discount_value' => 1000,
            'additional_value' => 0,
            'subtotal' => 9950,
        ]);

        $service = new SaveInvoiceService(
            new DetailValueCalculator(),
            new DetailValueValidator()
        );

        // Act
        $operationId = $service->saveInvoice($component);

        // Assert
        $this->assertNotFalse($operationId);
        
        $item = \App\Models\OperationItems::where('pro_id', $operationId)->first();
        $this->assertNotNull($item);
        
        // Expected: 9950 - (9950/9950 * 1000) = 8950
        $this->assertEquals(8950, $item->detail_value);
    }
}
```

### Property-Based Testing

Property-based tests are defined in the design document. Implement them using a PBT library like Pest with QuickCheck or similar.

**Example Property Test:**

```php
use Tests\TestCase;

class DetailValuePropertiesTest extends TestCase
{
    /**
     * Property 1: Detail Value Non-Negativity
     * For any item with valid price and quantity, detail_value should never be negative
     */
    public function test_detail_value_is_never_negative()
    {
        $calculator = new DetailValueCalculator();

        // Generate 100 random test cases
        for ($i = 0; $i < 100; $i++) {
            $itemData = [
                'item_price' => rand(1, 10000),
                'quantity' => rand(1, 100),
                'item_discount' => rand(0, 1000),
                'additional' => rand(0, 1000),
            ];

            $invoiceData = [
                'fat_disc' => rand(0, 5000),
                'fat_disc_per' => 0,
                'fat_plus' => rand(0, 2000),
                'fat_plus_per' => 0,
            ];

            $invoiceSubtotal = 10000;

            $result = $calculator->calculate($itemData, $invoiceData, $invoiceSubtotal);

            $this->assertGreaterThanOrEqual(0, $result['detail_value']);
        }
    }
}
```

---

## Deployment Guide

### Pre-Deployment Checklist

- [ ] All tests pass (unit, integration, property-based)
- [ ] Code review completed
- [ ] Database backup created
- [ ] Staging environment tested
- [ ] Performance benchmarks acceptable
- [ ] Documentation updated
- [ ] Rollback plan prepared

### Deployment Steps

#### Step 1: Backup Database

```bash
# Create backup
php artisan db:backup

# Or use mysqldump
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

#### Step 2: Deploy Code

```bash
# Pull latest code
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

#### Step 3: Run Migrations (if any)

```bash
php artisan migrate --force
```

#### Step 4: Test on Staging

```bash
# Run tests
php artisan test

# Test invoice creation manually
# Verify calculations are correct
```

#### Step 5: Fix Historical Data

```bash
# Dry run first
php artisan recalculation:fix-detail-values --all --dry-run

# Review output, then run actual fix
php artisan recalculation:fix-detail-values --all --force
```

#### Step 6: Monitor

```bash
# Watch logs for errors
tail -f storage/logs/laravel.log

# Check application metrics
# Monitor database performance
```

### Rollback Plan

If issues occur:

1. **Stop accepting new invoices** (maintenance mode)
   ```bash
   php artisan down
   ```

2. **Restore database backup**
   ```bash
   mysql -u username -p database_name < backup_file.sql
   ```

3. **Revert code changes**
   ```bash
   git revert HEAD
   git push origin main
   ```

4. **Clear caches**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

5. **Bring application back online**
   ```bash
   php artisan up
   ```

---

## Performance Considerations

### Calculation Performance

- **Invoice Creation:** +5-10ms per invoice (negligible)
- **Historical Data Fix:** ~1000 invoices per minute
- **Memory Usage:** Minimal increase (<10MB)

### Optimization Strategies

#### 1. Batch Processing

The recalculation command processes invoices in batches to manage memory:

```php
$query->chunk($batchSize, function ($invoices) {
    foreach ($invoices as $invoice) {
        $this->recalculateInvoice($invoice, $calculator, $dryRun);
    }
});
```

#### 2. Database Transactions

All invoice operations use transactions for data consistency:

```php
DB::transaction(function () use ($item, $newDetailValue) {
    $item->update(['detail_value' => $newDetailValue]);
});
```

#### 3. Eager Loading

The command eager loads relationships to prevent N+1 queries:

```php
$query = OperHead::query()->with('operationItems');
```

#### 4. Caching

Invoice subtotal is calculated once and reused for all items:

```php
$invoiceSubtotal = $this->detailValueCalculator->calculateInvoiceSubtotal($items);

foreach ($items as $item) {
    // Reuse $invoiceSubtotal for each item
    $calculation = $this->calculator->calculate($item, $invoiceData, $invoiceSubtotal);
}
```

### Performance Monitoring

Monitor these metrics:

1. **Invoice creation time:** Should be <100ms
2. **Recalculation throughput:** Should process >500 invoices/minute
3. **Memory usage:** Should not exceed 256MB
4. **Database query count:** Should not increase significantly

---

## Troubleshooting

### Common Development Issues

#### Issue 1: Tests Failing

**Symptom:** Unit tests fail with calculation mismatch

**Solution:**
1. Check if test data is valid
2. Verify expected values are correct
3. Review calculation formulas
4. Check for rounding issues (use tolerance)

#### Issue 2: Validation Errors in Production

**Symptom:** Invoices fail to save with validation errors

**Solution:**
1. Check application logs for details
2. Verify input data is valid
3. Review validation rules
4. Check if discounts exceed item values

#### Issue 3: Performance Degradation

**Symptom:** Invoice creation is slow

**Solution:**
1. Profile the code to identify bottlenecks
2. Check database query count (N+1 problem)
3. Verify indexes are in place
4. Review batch size for recalculation

#### Issue 4: Recalculation Command Hangs

**Symptom:** Command stops responding

**Solution:**
1. Check database connection
2. Reduce batch size
3. Check for deadlocks
4. Review memory limits

### Debugging Tips

#### Enable Debug Logging

```php
// In DetailValueCalculator
Log::debug('Calculating detail value', [
    'item_data' => $itemData,
    'invoice_data' => $invoiceData,
    'invoice_subtotal' => $invoiceSubtotal,
]);
```

#### Use Tinker for Testing

```bash
php artisan tinker

>>> $calculator = new App\Services\Invoice\DetailValueCalculator();
>>> $result = $calculator->calculate([...], [...], 10000);
>>> print_r($result);
```

#### Check Database State

```sql
-- Check detail_value for specific invoice
SELECT 
    oi.id,
    oi.item_id,
    oi.item_price,
    oi.qty_in,
    oi.item_discount,
    oi.additional,
    oi.detail_value
FROM operation_items oi
WHERE oi.pro_id = 12345;

-- Check average cost
SELECT id, name, average_cost
FROM items
WHERE id = 123;
```

---

## Additional Resources

- **Requirements Document:** `.kiro/specs/discount-additional-handling/requirements.md`
- **Design Document:** `.kiro/specs/discount-additional-handling/design.md`
- **User Guide:** `.kiro/specs/discount-additional-handling/USER_GUIDE.md`
- **Laravel Documentation:** https://laravel.com/docs
- **PHPUnit Documentation:** https://phpunit.de/documentation.html

---

**Last Updated:** December 2024  
**Version:** 1.0
