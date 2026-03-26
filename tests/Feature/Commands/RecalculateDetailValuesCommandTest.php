<?php

declare(strict_types=1);

namespace Tests\Feature\Commands;

use App\Models\Item;
use App\Models\OperationItems;
use App\Models\OperHead;
use App\Models\ProType;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * RecalculateDetailValuesCommandTest
 *
 * Tests for the recalculation:fix-detail-values command.
 * Covers invoice filtering, dry-run mode, actual recalculation,
 * error handling, and batch processing.
 */
class RecalculateDetailValuesCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a branch (required for foreign keys)
        $branch = \Modules\Branches\Models\Branch::create([
            'name' => 'Test Branch',
            'code' => 'TB001',
        ]);

        // Create necessary ProTypes
        ProType::create(['id' => 11, 'pname' => 'Purchase Invoice', 'branch_id' => $branch->id]);
        ProType::create(['id' => 12, 'pname' => 'Sales Invoice', 'branch_id' => $branch->id]);

        // Create a unit
        Unit::create([
            'id' => 1,
            'unit_name' => 'Piece',
            'code' => 1,
            'name' => 'Piece',
            'branch_id' => $branch->id,
        ]);

        // Create test items
        Item::create([
            'id' => 1,
            'item_name' => 'Test Item 1',
            'name' => 'Test Item 1',
            'item_type' => 1,
            'average_cost' => 100,
            'branch_id' => $branch->id,
        ]);

        Item::create([
            'id' => 2,
            'item_name' => 'Test Item 2',
            'name' => 'Test Item 2',
            'item_type' => 1,
            'average_cost' => 200,
            'branch_id' => $branch->id,
        ]);
    }

    /**
     * Test invoice filtering by invoice_id
     */
    public function test_filters_by_invoice_id(): void
    {
        // Create multiple invoices
        $invoice1 = $this->createInvoiceWithItems(11, '2024-01-01', 100, 10);
        $invoice2 = $this->createInvoiceWithItems(11, '2024-01-02', 200, 20);

        // Run command for specific invoice
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--invoice-id' => $invoice1->id,
            '--dry-run' => true,
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Found 1 invoices to process', $output);
    }

    /**
     * Test invoice filtering by date range
     */
    public function test_filters_by_date_range(): void
    {
        // Create invoices on different dates
        $this->createInvoiceWithItems(11, '2024-01-01', 100, 10);
        $this->createInvoiceWithItems(11, '2024-01-15', 200, 20);
        $this->createInvoiceWithItems(11, '2024-02-01', 300, 30);

        // Run command for January only
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--from-date' => '2024-01-01',
            '--to-date' => '2024-01-31',
            '--dry-run' => true,
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Found 2 invoices to process', $output);
    }

    /**
     * Test invoice filtering by operation type
     */
    public function test_filters_by_operation_type(): void
    {
        // Create invoices of different types
        $this->createInvoiceWithItems(11, '2024-01-01', 100, 10); // Purchase
        $this->createInvoiceWithItems(12, '2024-01-02', 200, 20); // Sales

        // Run command for purchase invoices only
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--operation-type' => 11,
            '--dry-run' => true,
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Found 1 invoices to process', $output);
    }

    /**
     * Test dry-run mode doesn't modify data
     */
    public function test_dry_run_does_not_modify_data(): void
    {
        // Create invoice with incorrect detail_value
        $invoice = $this->createInvoiceWithItems(11, '2024-01-01', 100, 10, 5);

        $item = $invoice->operationItems->first();
        $originalDetailValue = $item->detail_value;

        // Run in dry-run mode
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--invoice-id' => $invoice->id,
            '--dry-run' => true,
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('DRY RUN', $output);
        $this->assertStringContainsString('No changes were saved', $output);

        // Verify data wasn't changed
        $item->refresh();
        $this->assertEquals($originalDetailValue, $item->detail_value);
    }

    /**
     * Test actual recalculation modifies data correctly
     */
    public function test_actual_recalculation_modifies_data(): void
    {
        // Create invoice with items
        // Item 1: price=100, qty=10, discount=50 -> subtotal=950
        // Invoice discount: 95 (10% of 950)
        // Expected detail_value: 950 - 95 = 855
        $invoice = OperHead::create([
            'pro_type' => 11,
            'date' => '2024-01-01',
            'fat_disc' => 95,
            'fat_disc_per' => 0,
            'fat_plus' => 0,
            'fat_plus_per' => 0,
            'branch_id' => 1,
        ]);

        // Create item with incorrect detail_value
        OperationItems::create([
            'pro_id' => $invoice->id,
            'item_id' => 1,
            'unit_id' => 1,
            'item_price' => 100,
            'qty_in' => 10,
            'qty_out' => 0,
            'item_discount' => 50,
            'additional' => 0,
            'detail_value' => 999, // Incorrect value
            'branch_id' => 1,
        ]);

        // Run actual recalculation
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--invoice-id' => $invoice->id,
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Successfully fixed', $output);

        // Verify data was changed correctly
        $item = $invoice->operationItems->first();
        $item->refresh();
        $this->assertEquals(855.00, $item->detail_value);
    }

    /**
     * Test error handling for invalid invoice
     */
    public function test_handles_errors_gracefully(): void
    {
        // Create invoice with no items (will cause error)
        $invoice = OperHead::create([
            'pro_type' => 11,
            'date' => '2024-01-01',
            'fat_disc' => 0,
            'branch_id' => 1,
        ]);

        // Run command
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--invoice-id' => $invoice->id,
            '--force' => true,
        ]);

        // Should complete successfully even with errors
        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Summary', $output);
    }

    /**
     * Test batch processing
     */
    public function test_batch_processing(): void
    {
        // Create multiple invoices
        for ($i = 1; $i <= 5; $i++) {
            $this->createInvoiceWithItems(11, "2024-01-0{$i}", 100 * $i, 10);
        }

        // Run with small batch size
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--from-date' => '2024-01-01',
            '--to-date' => '2024-01-31',
            '--batch-size' => 2,
            '--dry-run' => true,
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Found 5 invoices to process', $output);
    }

    /**
     * Test validation rejects missing options
     */
    public function test_validation_rejects_missing_options(): void
    {
        $exitCode = Artisan::call('recalculation:fix-detail-values');

        $this->assertEquals(1, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Please specify at least one filter option', $output);
    }

    /**
     * Test validation rejects invalid date format
     */
    public function test_validation_rejects_invalid_date_format(): void
    {
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--from-date' => 'invalid-date',
        ]);

        $this->assertEquals(1, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Invalid --from-date format', $output);
    }

    /**
     * Test validation rejects invalid date range
     */
    public function test_validation_rejects_invalid_date_range(): void
    {
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--from-date' => '2024-12-31',
            '--to-date' => '2024-01-01',
        ]);

        $this->assertEquals(1, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('cannot be after', $output);
    }

    /**
     * Test skips items with no change needed
     */
    public function test_skips_items_with_no_change(): void
    {
        // Create invoice with correct detail_value
        $invoice = OperHead::create([
            'pro_type' => 11,
            'date' => '2024-01-01',
            'fat_disc' => 0,
            'fat_disc_per' => 0,
            'fat_plus' => 0,
            'fat_plus_per' => 0,
            'branch_id' => 1,
        ]);

        // Create item with correct detail_value
        OperationItems::create([
            'pro_id' => $invoice->id,
            'item_id' => 1,
            'unit_id' => 1,
            'item_price' => 100,
            'qty_in' => 10,
            'qty_out' => 0,
            'item_discount' => 0,
            'additional' => 0,
            'detail_value' => 1000.00, // Correct value
            'branch_id' => 1,
        ]);

        // Run recalculation
        $exitCode = Artisan::call('recalculation:fix-detail-values', [
            '--invoice-id' => $invoice->id,
            '--force' => true,
        ]);

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Items Skipped', $output);
    }

    /**
     * Helper method to create an invoice with items
     */
    private function createInvoiceWithItems(
        int $proType,
        string $date,
        float $itemPrice,
        float $quantity,
        float $invoiceDiscount = 0
    ): OperHead {
        $invoice = OperHead::create([
            'pro_type' => $proType,
            'date' => $date,
            'fat_disc' => $invoiceDiscount,
            'fat_disc_per' => 0,
            'fat_plus' => 0,
            'fat_plus_per' => 0,
            'branch_id' => 1,
        ]);

        OperationItems::create([
            'pro_id' => $invoice->id,
            'item_id' => 1,
            'unit_id' => 1,
            'item_price' => $itemPrice,
            'qty_in' => $quantity,
            'qty_out' => 0,
            'item_discount' => 0,
            'additional' => 0,
            'detail_value' => $itemPrice * $quantity,
            'branch_id' => 1,
        ]);

        return $invoice->fresh(['operationItems']);
    }
}
