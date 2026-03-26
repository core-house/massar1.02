# Discount and Additional Handling - User Guide

## Overview

This guide explains how discounts and additional charges affect average cost calculations in the MASSAR system. Understanding these concepts will help you verify invoice calculations and troubleshoot any issues.

## Table of Contents

1. [How Discounts Affect Average Cost](#how-discounts-affect-average-cost)
2. [How to Verify Calculations](#how-to-verify-calculations)
3. [How to Fix Historical Data](#how-to-fix-historical-data)
4. [Troubleshooting Guide](#troubleshooting-guide)

---

## How Discounts Affect Average Cost

### Understanding Detail Value

The **detail_value** is the final value of an invoice item after applying all discounts and additional charges. This value is used to calculate the average cost of inventory items.

### Types of Discounts and Additions

#### 1. Item-Level Discounts and Additions

These are applied directly to individual items:

- **Item Discount**: Reduces the cost of a specific item
- **Item Additional**: Increases the cost of a specific item (e.g., shipping, handling)

**Example:**
```
Item Price: 1,000 EGP
Quantity: 10 units
Item Discount: 100 EGP
Item Additional: 50 EGP

Item Subtotal = (1,000 × 10) - 100 + 50 = 9,950 EGP
```

#### 2. Invoice-Level Discounts and Additions

These are applied to the entire invoice and distributed proportionally across all items:

- **Invoice Discount (fat_disc)**: Fixed amount or percentage discount on the entire invoice
- **Invoice Additional (fat_plus)**: Fixed amount or percentage additional charges on the entire invoice

**Example:**
```
Invoice with 2 items:
- Item A: Subtotal = 10,000 EGP
- Item B: Subtotal = 5,000 EGP
Invoice Subtotal = 15,000 EGP

Invoice Discount = 1,500 EGP (10%)

Distributed to Item A: (10,000 / 15,000) × 1,500 = 1,000 EGP
Distributed to Item B: (5,000 / 15,000) × 1,500 = 500 EGP

Item A Detail Value = 10,000 - 1,000 = 9,000 EGP
Item B Detail Value = 5,000 - 500 = 4,500 EGP
```

### How Average Cost is Calculated

The average cost formula remains unchanged:

```
Average Cost = SUM(detail_value) / SUM(qty_in - qty_out)
```

The key improvement is that **detail_value** now accurately includes all discounts and additions.

### Impact on Different Invoice Types

#### Purchase Invoices (Type 11)
- Detail value includes all discounts and additions
- Average cost is recalculated immediately
- Affects future sales profit calculations

#### Sales Invoices (Type 10)
- Uses current average cost for profit calculation
- Detail value used for revenue calculation
- Does NOT change average cost

#### Purchase Returns (Type 12)
- Detail value is negative (reduces inventory value)
- Average cost is recalculated
- Reverses the effect of the original purchase

#### Sales Returns (Type 13)
- Restores inventory at original sales cost
- Average cost is recalculated
- Reverses the effect of the original sale

---

## How to Verify Calculations

### Manual Verification Steps

#### Step 1: Calculate Item Subtotal

```
Item Subtotal = (Item Price × Quantity) - Item Discount + Item Additional
```

#### Step 2: Calculate Invoice Subtotal

```
Invoice Subtotal = Sum of all Item Subtotals
```

#### Step 3: Calculate Distributed Invoice Discount

**For Fixed Amount:**
```
Item Ratio = Item Subtotal / Invoice Subtotal
Distributed Discount = Invoice Discount × Item Ratio
```

**For Percentage:**
```
Distributed Discount = Item Subtotal × (Discount Percentage / 100)
```

#### Step 4: Calculate Distributed Invoice Additional

**For Fixed Amount:**
```
Item Ratio = Item Subtotal / Invoice Subtotal
Distributed Additional = Invoice Additional × Item Ratio
```

**For Percentage:**
```
Distributed Additional = Item Subtotal × (Additional Percentage / 100)
```

#### Step 5: Calculate Final Detail Value

```
Detail Value = Item Subtotal - Distributed Discount + Distributed Additional
Detail Value = max(0, Detail Value)  // Cannot be negative
```

### Using System Logs

The system logs all calculation details for audit purposes. To view logs:

1. Access the application logs at `storage/logs/laravel.log`
2. Search for "Detail value calculated" entries
3. Review the calculation breakdown for each item

**Example Log Entry:**
```
[2024-01-15 10:30:00] INFO: Detail value calculated for item
{
    "item_id": 123,
    "item_index": 0,
    "original_sub_value": 10000,
    "calculated_detail_value": 9000,
    "breakdown": {
        "item_price": 1000,
        "quantity": 10,
        "item_discount": 0,
        "item_additional": 0,
        "item_subtotal": 10000,
        "distributed_discount": 1000,
        "distributed_additional": 0,
        "detail_value": 9000
    }
}
```

---

## How to Fix Historical Data

If you have invoices created before this feature was implemented, their detail_value may be incorrect. Use the recalculation command to fix them.

### Using the Recalculation Command

#### Basic Usage

**Fix all invoices:**
```bash
php artisan recalculation:fix-detail-values --all
```

**Preview changes without saving (dry run):**
```bash
php artisan recalculation:fix-detail-values --all --dry-run
```

#### Advanced Options

**Fix specific invoice:**
```bash
php artisan recalculation:fix-detail-values --invoice-id=12345
```

**Fix invoices in date range:**
```bash
php artisan recalculation:fix-detail-values --from-date=2024-01-01 --to-date=2024-12-31
```

**Fix specific operation type:**
```bash
php artisan recalculation:fix-detail-values --operation-type=11 --all
```
- Type 11: Purchase invoices
- Type 12: Purchase returns
- Type 10: Sales invoices
- Type 13: Sales returns

**Adjust batch size for performance:**
```bash
php artisan recalculation:fix-detail-values --all --batch-size=50
```

**Skip confirmation prompt:**
```bash
php artisan recalculation:fix-detail-values --all --force
```

### Understanding the Output

The command displays a progress bar and summary:

```
Starting detail_value recalculation...

Found 1000 invoices to process

Processing: 100% [============================] 1000/1000

=== Summary ===
+---------------------------+-------+
| Metric                    | Count |
+---------------------------+-------+
| Invoices Processed        | 1000  |
| Items Fixed               | 3500  |
| Items Skipped (no change) | 1500  |
| Errors                    | 0     |
+---------------------------+-------+

✓ Successfully fixed 3500 items
```

### Best Practices

1. **Always run dry-run first** to preview changes
2. **Backup your database** before running the actual fix
3. **Run during off-peak hours** to minimize impact
4. **Check logs** for any errors after completion
5. **Verify a sample** of fixed invoices manually

---

## Troubleshooting Guide

### Common Issues and Solutions

#### Issue 1: Detail Value is Negative

**Symptom:** Error message "Detail value cannot be negative"

**Cause:** Discounts exceed the item value

**Solution:**
1. Check if invoice discount is too large
2. Verify item prices and quantities are correct
3. Ensure discounts are reasonable (not exceeding 100%)

#### Issue 2: Calculation Mismatch

**Symptom:** Error message "Detail value calculation mismatch"

**Cause:** Rounding errors or data inconsistency

**Solution:**
1. Check if all required fields are filled
2. Verify invoice subtotal is calculated correctly
3. Review logs for detailed calculation breakdown
4. Contact support if issue persists

#### Issue 3: Unreasonable Detail Value

**Symptom:** Error message "Detail value is unreasonable"

**Cause:** Detail value exceeds 10x the base item value

**Solution:**
1. Check if additional charges are too high
2. Verify item price is correct
3. Review invoice-level additions
4. Ensure data entry is accurate

#### Issue 4: Average Cost Not Updating

**Symptom:** Average cost remains unchanged after purchase invoice

**Cause:** Recalculation service may have failed

**Solution:**
1. Check application logs for errors
2. Verify invoice was saved successfully
3. Run manual recalculation command:
   ```bash
   php artisan recalculation:fix-detail-values --invoice-id=YOUR_INVOICE_ID
   ```
4. Contact support if issue persists

#### Issue 5: Recalculation Command Fails

**Symptom:** Command exits with errors

**Possible Causes and Solutions:**

**Invalid date format:**
```bash
# Wrong
php artisan recalculation:fix-detail-values --from-date=01/01/2024

# Correct
php artisan recalculation:fix-detail-values --from-date=2024-01-01
```

**No filter specified:**
```bash
# Wrong
php artisan recalculation:fix-detail-values

# Correct (must specify at least one filter or --all)
php artisan recalculation:fix-detail-values --all
```

**Batch size out of range:**
```bash
# Wrong
php artisan recalculation:fix-detail-values --all --batch-size=5000

# Correct (must be between 1 and 1000)
php artisan recalculation:fix-detail-values --all --batch-size=100
```

### Getting Help

If you encounter issues not covered in this guide:

1. **Check the logs:** `storage/logs/laravel.log`
2. **Review the design document:** `.kiro/specs/discount-additional-handling/design.md`
3. **Contact technical support** with:
   - Invoice ID
   - Error message
   - Relevant log entries
   - Steps to reproduce the issue

---

## Frequently Asked Questions

### Q1: Will this affect existing invoices?

**A:** No, existing invoices are not automatically changed. You must run the recalculation command to fix historical data.

### Q2: How often should I run the recalculation command?

**A:** Only once after upgrading to this version. New invoices will automatically use the correct calculation.

### Q3: Can I undo the recalculation?

**A:** Yes, if you have a database backup. Always backup before running the command.

### Q4: Does this affect sales invoices?

**A:** Sales invoices use the current average cost for profit calculation. The detail_value is used for revenue calculation but does not change the average cost.

### Q5: What happens if I have both fixed amount and percentage discounts?

**A:** Fixed amount takes precedence. If both are specified, only the fixed amount is used.

### Q6: How do I know if my invoices need recalculation?

**A:** Run the command with `--dry-run` flag. It will show how many items would be fixed without making changes.

### Q7: Will this slow down invoice creation?

**A:** No, the performance impact is minimal (<10ms per invoice). The calculation is very efficient.

### Q8: Can I recalculate specific items only?

**A:** No, the command works at the invoice level. All items in selected invoices will be recalculated.

---

## Additional Resources

- **Design Document:** `.kiro/specs/discount-additional-handling/design.md`
- **Requirements Document:** `.kiro/specs/discount-additional-handling/requirements.md`
- **Developer Guide:** `.kiro/specs/discount-additional-handling/DEVELOPER_GUIDE.md`
- **Application Logs:** `storage/logs/laravel.log`

---

**Last Updated:** December 2024  
**Version:** 1.0
