<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProTypesSeeder extends Seeder
{
    public function run(): void
    {
        $operations = [
            // 🧾 السندات
            ['id' => 1, 'pname' => 'receipt', 'ptext' => 'سند قبض', 'ptype' => 'سند'],
            ['id' => 2, 'pname' => 'payment', 'ptext' => 'سند دفع', 'ptype' => 'سند'],
            ['id' => 3, 'pname' => 'cash_to_cash', 'ptext' => 'تحويل نقدية من صندوق لصندوق', 'ptype' => 'تحويل'],
            ['id' => 4, 'pname' => 'cash_to_bank', 'ptext' => 'تحويل نقدية من صندوق لبنك', 'ptype' => 'تحويل'],
            ['id' => 5, 'pname' => 'bank_to_cash', 'ptext' => 'تحويل نقدية من بنك لصندوق', 'ptype' => 'تحويل'],
            ['id' => 6, 'pname' => 'bank_to_bank', 'ptext' => 'تحويل نقدية من بنك لبنك', 'ptype' => 'تحويل'],
            ['id' => 7, 'pname' => 'daily_entry', 'ptext' => 'قيد يومية', 'ptype' => 'قيد'],
            ['id' => 8, 'pname' => 'multi_entry', 'ptext' => 'قيد متعدد', 'ptype' => 'قيد'],
            ['id' => 10, 'pname' => 'sales_invoice', 'ptext' => 'فاتورة مبيعات', 'ptype' => 'فاتورة'],
            ['id' => 11, 'pname' => 'purchase_invoice', 'ptext' => 'فاتورة مشتريات', 'ptype' => 'فاتورة'],
            ['id' => 12, 'pname' => 'sales_return', 'ptext' => 'مردود مبيعات', 'ptype' => 'فاتورة'],
            ['id' => 13, 'pname' => 'purchase_return', 'ptext' => 'مردود مشتريات', 'ptype' => 'فاتورة'],
            ['id' => 14, 'pname' => 'sale_order', 'ptext' => 'امر بيع', 'ptype' => 'أمر بيع'],
            ['id' => 15, 'pname' => 'purchase_order', 'ptext' => 'امر شراء', 'ptype' => 'أمر شراء'],
            ['id' => 16, 'pname' => 'quotation_customer', 'ptext' => 'عرض سعر لعميل', 'ptype' => 'عرض سعر'],
            ['id' => 17, 'pname' => 'quotation_supplier', 'ptext' => 'عرض سعر من مورد', 'ptype' => 'عرض سعر'],
            ['id' => 18, 'pname' => 'damage_invoice', 'ptext' => 'فاتورة توالف', 'ptype' => 'فاتورة'],
            ['id' => 19, 'pname' => 'withdraw_order', 'ptext' => 'امر صرف', 'ptype' => 'أمر مخزني'],
            ['id' => 20, 'pname' => 'add_order', 'ptext' => 'امر اضافة', 'ptype' => 'أمر مخزني'],
            ['id' => 21, 'pname' => 'inventory_transfer', 'ptext' => 'تحويل من مخزن لمخزن', 'ptype' => 'تحويل'],
            ['id' => 22, 'pname' => 'reservation_order', 'ptext' => 'امر حجز', 'ptype' => 'أمر بيع'],
            ['id' => 23, 'pname' => 'branch_transfer', 'ptext' => 'تحويل بين فروع', 'ptype' => 'تحويل'],

            ['id' => 24, 'pname' => 'service_invoice', 'ptext' => 'فاتورة خدمه', 'ptype' => 'فاتورة'],
            ['id' => 25, 'pname' => 'request_order', 'ptext' => 'طلب احياج', 'ptype' => 'فاتورة'],
            ['id' => 26, 'pname' => 'pricing_agreement', 'ptext' => 'اتفاقية تسعير', 'ptype' => 'فاتورة'],

            ['id' => 30, 'pname' => 'allowed_discount', 'ptext' => 'خصم مسموح به', 'ptype' => 'سند'],
            ['id' => 31, 'pname' => 'earned_discount', 'ptext' => 'خصم مكتسب', 'ptype' => 'سند'],
            ['id' => 32, 'pname' => 'multi_receipt', 'ptext' => 'سند قبض متعدد', 'ptype' => 'سند'],
            ['id' => 33, 'pname' => 'multi_payment', 'ptext' => 'سند دفع متعدد', 'ptype' => 'سند'],
            ['id' => 34, 'pname' => 'petty_cash_settlement', 'ptext' => 'تسوية عهدة', 'ptype' => 'سند'],
            ['id' => 35, 'pname' => 'stock_damage', 'ptext' => 'سند إتلاف مخزون', 'ptype' => 'سند'],
            ['id' => 36, 'pname' => 'provision_entry', 'ptext' => 'مخصصات', 'ptype' => 'سند'],
            ['id' => 37, 'pname' => 'personal_loan', 'ptext' => 'سلفة شخصية', 'ptype' => 'سند'],
            ['id' => 38, 'pname' => 'currency_conversion', 'ptext' => 'تحويل بين عملات', 'ptype' => 'تحويل'],
            ['id' => 40, 'pname' => 'salary_calculation', 'ptext' => 'احتساب رواتب الموظفين', 'ptype' => 'موارد بشرية'],
            ['id' => 41, 'pname' => 'extra_calc', 'ptext' => 'احتساب اضافي للموظفين', 'ptype' => 'موارد بشرية'],
            ['id' => 42, 'pname' => 'discount_calc', 'ptext' => 'احتساب خصم للموظفين', 'ptype' => 'موارد بشرية'],
            ['id' => 43, 'pname' => 'insurance_calc', 'ptext' => 'احتساب تأمينات اجتماعية', 'ptype' => 'موارد بشرية'],
            ['id' => 44, 'pname' => 'tax_calc', 'ptext' => 'احتساب ضريبه دخل', 'ptype' => 'موارد بشرية'],
            ['id' => 45, 'pname' => 'contract', 'ptext' => 'اتفاقية خدمة', 'ptype' => 'عقد'],
            ['id' => 46, 'pname' => 'accured_expense', 'ptext' => 'مصروفات مستحقة', 'ptype' => 'مستحقات'],
            ['id' => 47, 'pname' => 'accured_income', 'ptext' => 'ايرادات مستحقة', 'ptype' => 'مستحقات'],
            ['id' => 48, 'pname' => 'bank_commission', 'ptext' => 'احتساب عمولة بنكية', 'ptype' => 'مصروفات'],
            ['id' => 49, 'pname' => 'sales_contract', 'ptext' => 'عقد بيع', 'ptype' => 'عقد'],

            ['id' => 50, 'pname' => 'depreciation', 'ptext' => 'اهلاك الاصل', 'ptype' => 'أصل'],
            ['id' => 51, 'pname' => 'sell_asset', 'ptext' => 'بيع اصل', 'ptype' => 'أصل'],
            ['id' => 52, 'pname' => 'buy_asset', 'ptext' => 'شراء اصل', 'ptype' => 'أصل'],
            ['id' => 53, 'pname' => 'increase_asset_value', 'ptext' => 'زيادة ف قيمة الاصل', 'ptype' => 'أصل'],
            ['id' => 54, 'pname' => 'decrease_asset_value', 'ptext' => 'نقص في قيمة الاصل', 'ptype' => 'أصل'],
            ['id' => 55, 'pname' => 'partner_profit_sharing', 'ptext' => 'توزيع الارباح علي الشركاء', 'ptype' => 'مالية'],
            ['id' => 56, 'pname' => 'production_model', 'ptext' => 'نموزج تصنيع', 'ptype' => 'تصنيع'],
            ['id' => 57, 'pname' => 'job_order', 'ptext' => 'امر تشغيل', 'ptype' => 'تصنيع'],
            ['id' => 58, 'pname' => 'standard_manufacturing', 'ptext' => 'تصنيع معياري', 'ptype' => 'تصنيع'],
            ['id' => 59, 'pname' => 'free_manufacturing', 'ptext' => 'تصنيع حر', 'ptype' => 'تصنيع'],
            ['id' => 60, 'pname' => 'inventory_start_balance', 'ptext' => 'تسجيل الارصده الافتتاحيه للمخازن', 'ptype' => 'ارصده افتتاحيه'],
            ['id' => 61, 'pname' => 'accounts_start_balance', 'ptext' => 'تسجيل الارصده الافتتاحيه للحسابات', 'ptype' => 'ارصده افتتاحيه للحسابات'],
            ['id' => 62, 'pname' => 'rental', 'ptext' => 'مستند تأجير معدة', 'ptype' => 'تأجير'],
            ['id' => 63, 'pname' => 'facturing_example', 'ptext' => 'نموذج تصنيع', 'ptype' => 'تصنيع'],

            ['id' => 64, 'pname' => 'rentals', 'ptext' => 'تأجير وحدات', 'ptype' => 'تأجير'],

            // 📝 الشيكات والأوراق المالية
            ['id' => 65, 'pname' => 'incoming_check', 'ptext' => 'إضافة ورقة قبض', 'ptype' => 'ورقة قبض'],
            ['id' => 66, 'pname' => 'outgoing_check', 'ptext' => 'إضافة ورقة دفع', 'ptype' => 'ورقة دفع'],
            ['id' => 67, 'pname' => 'check_collection', 'ptext' => 'تحصيل ورقة قبض', 'ptype' => 'ورقة قبض'],
            ['id' => 68, 'pname' => 'check_endorsement', 'ptext' => 'تظهير ورقة', 'ptype' => 'ورقة قبض'],
            ['id' => 69, 'pname' => 'bounced_check', 'ptext' => 'ورقة مرتدة', 'ptype' => 'ورقة قبض'],
            ['id' => 70, 'pname' => 'cancel_check', 'ptext' => 'إلغاء ورقة', 'ptype' => 'ورقة مالية'],
            ['id' => 71, 'pname' => 'check_reversal', 'ptext' => 'قيد عكسي لورقة', 'ptype' => 'ورقة مالية'],
            ['id' => 72, 'pname' => 'check_transfer', 'ptext' => 'صرف ورقة دفع', 'ptype' => 'ورقة دفع'],

            ['id' => 73, 'pname' => 'auto_depreciation', 'ptext' => 'اهلاك تلقائي', 'ptype' => 'أصل'],
            ['id' => 74, 'pname' => 'Salary entitlement', 'ptext' => 'إستحقاق رواتب', 'ptype' => 'رواتب'],
            ['id' => 75, 'pname' => 'employee_deduction', 'ptext' => 'خصومات وجزاءات الموظفين', 'ptype' => 'موارد بشرية'],
            ['id' => 76, 'pname' => 'employee_reward', 'ptext' => 'مكافآت وحوافز الموظفين', 'ptype' => 'موارد بشرية'],
            ['id' => 77, 'pname' => 'flexible_salary', 'ptext' => 'راتب مرن (ثابت + ساعات)', 'ptype' => 'موارد بشرية'],
            ['id' => 78, 'pname' => 'employee_advance', 'ptext' => 'سلف الموظفين', 'ptype' => 'موارد بشرية'],
            ['id' => 79, 'pname' => 'advance_deduction', 'ptext' => 'استقطاع سلف من الراتب', 'ptype' => 'موارد بشرية'],
            
            ['id' => 80, 'pname' => 'currency_buy', 'ptext' => 'شراء عملة', 'ptype' => 'تبادل عملات'],
            ['id' => 81, 'pname' => 'currency_sell', 'ptext' => 'بيع عملة', 'ptype' => 'تبادل عملات'],
            
            ['id' => 101, 'pname' => 'expense_voucher', 'ptext' => 'سند دفع مصروفات', 'ptype' => 'فاتورة'],
            ['id' => 102, 'pname' => 'cashier_invoice', 'ptext' => 'فاتورة كاشير', 'ptype' => 'فاتورة'],
            ['id' => 103, 'pname' => 'resturant_invoice', 'ptext' => 'فاتورة مطعم', 'ptype' => 'فاتورة'],
            ['id' => 104, 'pname' => 're_cashier_invoice', 'ptext' => 'فاتورة كاشير مرتجع', 'ptype' => 'فاتورة'],

        ];

        // Use updateOrInsert to handle existing records
        foreach ($operations as $operation) {
            DB::table('pro_types')->updateOrInsert(
                ['id' => $operation['id']],
                $operation
            );
        }
    }
}
