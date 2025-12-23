# حل مقترح: معالجة الخصومات والإضافات في حساب متوسط التكلفة

## تحليل الوضع الحالي

### البنية الحالية

#### جدول `operation_items`
```sql
- item_price: سعر الوحدة
- qty_in: الكمية الداخلة (مشتريات)
- qty_out: الكمية الخارجة (مبيعات)
- item_discount: خصم على مستوى الصنف
- additional: إضافات على مستوى الصنف
- detail_value: القيمة النهائية (تستخدم في حساب متوسط التكلفة)
```

#### جدول `operhead`
```sql
- fat_total: إجمالي الفاتورة
- fat_disc: خصم على مستوى الفاتورة (قيمة)
- fat_disc_per: خصم على مستوى الفاتورة (نسبة مئوية)
- fat_plus: إضافات على مستوى الفاتورة (قيمة)
- fat_plus_per: إضافات على مستوى الفاتورة (نسبة مئوية)
- fat_net: صافي الفاتورة
```

### المشكلة الحالية

1. **`detail_value` يأتي من الواجهة الأمامية** (`$invoiceItem['sub_value']`)
2. **لا يوجد توزيع واضح** للخصومات والإضافات على مستوى الفاتورة على الأصناف
3. **عدم التأكد** من أن `detail_value` يشمل جميع الخصومات والإضافات بشكل صحيح

## الحل المقترح

### المرحلة 1: إنشاء خدمة لحساب `detail_value`

إنشاء `DetailValueCalculator` service لحساب القيمة النهائية للصنف بشكل صحيح:

```php
class DetailValueCalculator
{
    /**
     * حساب detail_value للصنف مع توزيع خصومات وإضافات الفاتورة
     *
     * @param array $itemData بيانات الصنف
     * @param array $invoiceData بيانات الفاتورة
     * @param float $invoiceSubtotal إجمالي الفاتورة قبل الخصم والإضافات
     * @return array ['detail_value', 'distributed_discount', 'distributed_additional']
     */
    public function calculate(array $itemData, array $invoiceData, float $invoiceSubtotal): array
    {
        // 1. حساب قيمة الصنف الأساسية
        $itemPrice = $itemData['price'];
        $quantity = $itemData['quantity'];
        $itemSubtotal = $itemPrice * $quantity;
        
        // 2. تطبيق خصم الصنف
        $itemDiscount = $itemData['discount'] ?? 0;
        $itemAfterDiscount = $itemSubtotal - $itemDiscount;
        
        // 3. تطبيق إضافات الصنف
        $itemAdditional = $itemData['additional'] ?? 0;
        $itemAfterAdditional = $itemAfterDiscount + $itemAdditional;
        
        // 4. حساب نسبة الصنف من إجمالي الفاتورة
        $itemRatio = $invoiceSubtotal > 0 ? ($itemSubtotal / $invoiceSubtotal) : 0;
        
        // 5. توزيع خصم الفاتورة
        $distributedDiscount = 0;
        if (!empty($invoiceData['fat_disc'])) {
            $distributedDiscount = $invoiceData['fat_disc'] * $itemRatio;
        } elseif (!empty($invoiceData['fat_disc_per'])) {
            $distributedDiscount = $itemSubtotal * ($invoiceData['fat_disc_per'] / 100);
        }
        
        // 6. توزيع إضافات الفاتورة
        $distributedAdditional = 0;
        if (!empty($invoiceData['fat_plus'])) {
            $distributedAdditional = $invoiceData['fat_plus'] * $itemRatio;
        } elseif (!empty($invoiceData['fat_plus_per'])) {
            $distributedAdditional = $itemSubtotal * ($invoiceData['fat_plus_per'] / 100);
        }
        
        // 7. حساب القيمة النهائية
        $detailValue = $itemAfterAdditional - $distributedDiscount + $distributedAdditional;
        
        // 8. التأكد من أن القيمة ليست سالبة
        $detailValue = max(0, $detailValue);
        
        return [
            'detail_value' => round($detailValue, 2),
            'distributed_discount' => round($distributedDiscount, 2),
            'distributed_additional' => round($distributedAdditional, 2),
            'item_subtotal' => round($itemSubtotal, 2),
            'item_after_discount' => round($itemAfterDiscount, 2),
            'item_after_additional' => round($itemAfterAdditional, 2),
        ];
    }
    
    /**
     * حساب إجمالي الفاتورة قبل الخصم والإضافات على مستوى الفاتورة
     *
     * @param array $items مصفوفة الأصناف
     * @return float
     */
    public function calculateInvoiceSubtotal(array $items): float
    {
        $subtotal = 0;
        
        foreach ($items as $item) {
            $itemPrice = $item['price'];
            $quantity = $item['quantity'];
            $itemDiscount = $item['discount'] ?? 0;
            $itemAdditional = $item['additional'] ?? 0;
            
            $itemSubtotal = ($itemPrice * $quantity) - $itemDiscount + $itemAdditional;
            $subtotal += $itemSubtotal;
        }
        
        return $subtotal;
    }
}
```

### المرحلة 2: تحديث `SaveInvoiceService`

تحديث `SaveInvoiceService` لاستخدام `DetailValueCalculator`:

```php
class SaveInvoiceService
{
    private DetailValueCalculator $detailValueCalculator;
    
    public function __construct(DetailValueCalculator $detailValueCalculator)
    {
        $this->detailValueCalculator = $detailValueCalculator;
    }
    
    public function saveInvoice($component, $isEdit = false)
    {
        // ... existing code ...
        
        // حساب إجمالي الفاتورة قبل الخصم والإضافات
        $invoiceSubtotal = $this->detailValueCalculator->calculateInvoiceSubtotal(
            $component->invoiceItems
        );
        
        // بيانات الفاتورة
        $invoiceData = [
            'fat_disc' => $component->fat_disc ?? 0,
            'fat_disc_per' => $component->fat_disc_per ?? 0,
            'fat_plus' => $component->fat_plus ?? 0,
            'fat_plus_per' => $component->fat_plus_per ?? 0,
        ];
        
        foreach ($component->invoiceItems as $invoiceItem) {
            // حساب detail_value بشكل صحيح
            $calculation = $this->detailValueCalculator->calculate(
                $invoiceItem,
                $invoiceData,
                $invoiceSubtotal
            );
            
            $detailValue = $calculation['detail_value'];
            
            // Log للتدقيق
            Log::info('Detail value calculation', [
                'item_id' => $invoiceItem['item_id'],
                'item_subtotal' => $calculation['item_subtotal'],
                'item_discount' => $invoiceItem['discount'] ?? 0,
                'item_additional' => $invoiceItem['additional'] ?? 0,
                'distributed_discount' => $calculation['distributed_discount'],
                'distributed_additional' => $calculation['distributed_additional'],
                'final_detail_value' => $detailValue,
            ]);
            
            // حفظ الصنف مع detail_value المحسوب
            OperationItems::create([
                // ... existing fields ...
                'detail_value' => $detailValue,
                'item_discount' => $invoiceItem['discount'] ?? 0,
                'additional' => $invoiceItem['additional'] ?? 0,
                // ... other fields ...
            ]);
        }
        
        // ... rest of the code ...
    }
}
```

### المرحلة 3: إضافة Validation

إضافة validation للتأكد من صحة الحسابات:

```php
class DetailValueValidator
{
    /**
     * التحقق من صحة detail_value
     *
     * @param float $detailValue القيمة المحسوبة
     * @param array $itemData بيانات الصنف
     * @param array $calculation تفاصيل الحساب
     * @throws InvalidArgumentException
     */
    public function validate(float $detailValue, array $itemData, array $calculation): void
    {
        // 1. التحقق من أن القيمة ليست سالبة
        if ($detailValue < 0) {
            throw new InvalidArgumentException(
                "Detail value cannot be negative. Item ID: {$itemData['item_id']}, Calculated: {$detailValue}"
            );
        }
        
        // 2. التحقق من أن القيمة منطقية
        $itemPrice = $itemData['price'];
        $quantity = $itemData['quantity'];
        $maxPossibleValue = ($itemPrice * $quantity) * 2; // ضعف القيمة الأصلية كحد أقصى
        
        if ($detailValue > $maxPossibleValue) {
            Log::warning('Detail value seems unusually high', [
                'item_id' => $itemData['item_id'],
                'detail_value' => $detailValue,
                'max_expected' => $maxPossibleValue,
                'calculation' => $calculation,
            ]);
        }
        
        // 3. التحقق من أن الحساب صحيح
        $expectedValue = $calculation['item_subtotal'] 
            - ($itemData['discount'] ?? 0)
            + ($itemData['additional'] ?? 0)
            - $calculation['distributed_discount']
            + $calculation['distributed_additional'];
            
        $difference = abs($detailValue - $expectedValue);
        
        if ($difference > 0.01) { // tolerance of 0.01
            throw new InvalidArgumentException(
                "Detail value calculation mismatch. Expected: {$expectedValue}, Got: {$detailValue}"
            );
        }
    }
}
```

### المرحلة 4: إضافة Artisan Command لإصلاح البيانات القديمة

```php
class RecalculateDetailValuesCommand extends Command
{
    protected $signature = 'recalculation:fix-detail-values 
                            {--invoice-id= : Specific invoice ID to fix}
                            {--from-date= : Fix invoices from this date}
                            {--dry-run : Preview changes without saving}';
    
    protected $description = 'Recalculate detail_value for operation items to include all discounts and additions';
    
    public function handle(DetailValueCalculator $calculator): int
    {
        $dryRun = $this->option('dry-run');
        $invoiceId = $this->option('invoice-id');
        $fromDate = $this->option('from-date');
        
        $this->info('Starting detail_value recalculation...');
        
        // Get invoices to process
        $query = OperHead::with('items');
        
        if ($invoiceId) {
            $query->where('id', $invoiceId);
        }
        
        if ($fromDate) {
            $query->where('pro_date', '>=', $fromDate);
        }
        
        $invoices = $query->get();
        
        $this->info("Found {$invoices->count()} invoices to process");
        
        $fixed = 0;
        $errors = 0;
        
        foreach ($invoices as $invoice) {
            try {
                $result = $this->recalculateInvoice($invoice, $calculator, $dryRun);
                $fixed += $result['fixed'];
                
                $this->info("Invoice {$invoice->id}: Fixed {$result['fixed']} items");
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error processing invoice {$invoice->id}: {$e->getMessage()}");
            }
        }
        
        $this->info("\nSummary:");
        $this->info("- Invoices processed: {$invoices->count()}");
        $this->info("- Items fixed: {$fixed}");
        $this->info("- Errors: {$errors}");
        
        if ($dryRun) {
            $this->warn('DRY RUN - No changes were saved');
        }
        
        return 0;
    }
    
    private function recalculateInvoice($invoice, $calculator, $dryRun): array
    {
        $items = $invoice->items;
        
        // Calculate invoice subtotal
        $invoiceSubtotal = 0;
        foreach ($items as $item) {
            $itemSubtotal = ($item->item_price * ($item->qty_in + $item->qty_out))
                - $item->item_discount
                + $item->additional;
            $invoiceSubtotal += $itemSubtotal;
        }
        
        $invoiceData = [
            'fat_disc' => $invoice->fat_disc ?? 0,
            'fat_disc_per' => $invoice->fat_disc_per ?? 0,
            'fat_plus' => $invoice->fat_plus ?? 0,
            'fat_plus_per' => $invoice->fat_plus_per ?? 0,
        ];
        
        $fixed = 0;
        
        foreach ($items as $item) {
            $itemData = [
                'price' => $item->item_price,
                'quantity' => $item->qty_in + $item->qty_out,
                'discount' => $item->item_discount,
                'additional' => $item->additional,
            ];
            
            $calculation = $calculator->calculate($itemData, $invoiceData, $invoiceSubtotal);
            $newDetailValue = $calculation['detail_value'];
            
            // Check if needs update
            if (abs($item->detail_value - $newDetailValue) > 0.01) {
                $this->line("  Item {$item->id}: {$item->detail_value} -> {$newDetailValue}");
                
                if (!$dryRun) {
                    $item->detail_value = $newDetailValue;
                    $item->save();
                }
                
                $fixed++;
            }
        }
        
        return ['fixed' => $fixed];
    }
}
```

### المرحلة 5: إضافة Tests

```php
class DetailValueCalculatorTest extends TestCase
{
    private DetailValueCalculator $calculator;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new DetailValueCalculator();
    }
    
    /** @test */
    public function it_calculates_detail_value_with_item_discount()
    {
        $itemData = [
            'price' => 100,
            'quantity' => 10,
            'discount' => 50,
            'additional' => 0,
        ];
        
        $invoiceData = [
            'fat_disc' => 0,
            'fat_disc_per' => 0,
            'fat_plus' => 0,
            'fat_plus_per' => 0,
        ];
        
        $result = $this->calculator->calculate($itemData, $invoiceData, 1000);
        
        // (100 * 10) - 50 = 950
        $this->assertEquals(950, $result['detail_value']);
    }
    
    /** @test */
    public function it_distributes_invoice_discount_proportionally()
    {
        $itemData = [
            'price' => 100,
            'quantity' => 10,
            'discount' => 0,
            'additional' => 0,
        ];
        
        $invoiceData = [
            'fat_disc' => 100, // 100 discount on invoice
            'fat_disc_per' => 0,
            'fat_plus' => 0,
            'fat_plus_per' => 0,
        ];
        
        // Item is 1000 out of 2000 total (50%)
        $result = $this->calculator->calculate($itemData, $invoiceData, 2000);
        
        // Item gets 50% of invoice discount = 50
        $this->assertEquals(50, $result['distributed_discount']);
        // (100 * 10) - 50 = 950
        $this->assertEquals(950, $result['detail_value']);
    }
    
    /** @test */
    public function it_handles_percentage_based_invoice_discount()
    {
        $itemData = [
            'price' => 100,
            'quantity' => 10,
            'discount' => 0,
            'additional' => 0,
        ];
        
        $invoiceData = [
            'fat_disc' => 0,
            'fat_disc_per' => 10, // 10% discount
            'fat_plus' => 0,
            'fat_plus_per' => 0,
        ];
        
        $result = $this->calculator->calculate($itemData, $invoiceData, 2000);
        
        // 10% of 1000 = 100
        $this->assertEquals(100, $result['distributed_discount']);
        // (100 * 10) - 100 = 900
        $this->assertEquals(900, $result['detail_value']);
    }
    
    /** @test */
    public function it_handles_combined_discounts_and_additions()
    {
        $itemData = [
            'price' => 100,
            'quantity' => 10,
            'discount' => 50,      // Item discount
            'additional' => 30,    // Item additional
        ];
        
        $invoiceData = [
            'fat_disc' => 100,     // Invoice discount
            'fat_disc_per' => 0,
            'fat_plus' => 50,      // Invoice additional
            'fat_plus_per' => 0,
        ];
        
        // Invoice subtotal = (100*10) - 50 + 30 = 980
        $invoiceSubtotal = 980;
        
        $result = $this->calculator->calculate($itemData, $invoiceData, $invoiceSubtotal);
        
        // Item ratio = 980 / 980 = 100%
        // Distributed discount = 100 * 1.0 = 100
        // Distributed additional = 50 * 1.0 = 50
        // Final = 980 - 100 + 50 = 930
        $this->assertEquals(930, $result['detail_value']);
    }
}
```

## خطة التنفيذ

### المرحلة 1: التحضير (يوم واحد)
1. ✅ إنشاء متطلبات النظام
2. ✅ مراجعة الكود الحالي
3. ✅ تصميم الحل

### المرحلة 2: التطوير (3-4 أيام)
1. إنشاء `DetailValueCalculator` service
2. إنشاء `DetailValueValidator` service
3. تحديث `SaveInvoiceService`
4. إضافة logging و audit trail
5. كتابة unit tests

### المرحلة 3: إصلاح البيانات القديمة (يوم واحد)
1. إنشاء artisan command
2. اختبار على بيانات تجريبية
3. تشغيل على البيانات الفعلية

### المرحلة 4: الاختبار (2-3 أيام)
1. اختبار وحدات (unit tests)
2. اختبار تكامل (integration tests)
3. اختبار يدوي لسيناريوهات مختلفة
4. مراجعة الأداء

### المرحلة 5: النشر (يوم واحد)
1. مراجعة نهائية
2. نشر على بيئة الإنتاج
3. مراقبة الأداء
4. توثيق التغييرات

## الفوائد المتوقعة

1. **دقة أعلى**: حساب متوسط التكلفة بشكل دقيق يشمل جميع الخصومات والإضافات
2. **شفافية**: توزيع واضح للخصومات والإضافات على مستوى الفاتورة
3. **قابلية التدقيق**: سجل كامل لجميع الحسابات
4. **سهولة الصيانة**: كود منظم وقابل للاختبار
5. **توافق خلفي**: يعمل مع البيانات الموجودة

## المخاطر والتحديات

1. **البيانات القديمة**: قد تحتوي على قيم غير صحيحة
   - **الحل**: artisan command لإصلاح البيانات

2. **الأداء**: حسابات إضافية قد تؤثر على الأداء
   - **الحل**: caching و optimization

3. **التوافق**: قد يؤثر على الكود الموجود
   - **الحل**: اختبار شامل قبل النشر

4. **التعقيد**: منطق حساب معقد
   - **الحل**: توثيق جيد و unit tests شاملة
