<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة indexes لجدول items لتحسين البحث (تخطي الموجود)
        // Schema::table('items', function (Blueprint $table) {
        //     $table->index('name', 'idx_items_name'); // موجود بالفعل
        //     $table->index('code', 'idx_items_code'); // موجود بالفعل
        //     $table->index('branch_id', 'idx_items_branch');
        // });

        // إضافة indexes لجدول operation_items لتحسين استعلامات المخزون (تخطي الموجود)
        // Schema::table('operation_items', function (Blueprint $table) {
        //     $table->index(['item_id', 'detail_store'], 'idx_operation_items_item_store'); // موجود بالفعل
        //     $table->index(['pro_tybe', 'created_at'], 'idx_operation_items_dates'); // موجود بالفعل
        //     $table->index(['item_id', 'is_stock'], 'idx_operation_items_item_stock'); // موجود بالفعل
        // });

        // إضافة indexes لجدول journal_details لتحسين استعلامات الرصيد (تخطي الموجود)
        // Schema::table('journal_details', function (Blueprint $table) {
        //     $table->index(['account_id', 'isdeleted'], 'idx_journal_details_account'); // موجود بالفعل
        //     $table->index(['account_id', 'crtime'], 'idx_journal_details_account_date');
        // });

        // إضافة indexes لجدول barcodes لتحسين البحث بالباركود (تخطي الموجود)
        // Schema::table('barcodes', function (Blueprint $table) {
        //     $table->index(['item_id', 'barcode'], 'idx_barcodes_item_barcode'); // موجود بالفعل
        // });

        // إضافة indexes لجدول acc_head لتحسين استعلامات الحسابات
        Schema::table('acc_head', function (Blueprint $table) {
            $table->index(['code', 'isdeleted'], 'idx_acc_head_code');
            $table->index(['is_basic', 'is_fund'], 'idx_acc_head_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف indexes من جدول items (تخطي الموجود)
        // Schema::table('items', function (Blueprint $table) {
        //     $table->dropIndex('idx_items_name');
        //     $table->dropIndex('idx_items_code');
        //     $table->dropIndex('idx_items_branch');
        // });

        // حذف indexes من جدول operation_items (تخطي الموجود)
        // Schema::table('operation_items', function (Blueprint $table) {
        //     $table->dropIndex('idx_operation_items_item_store');
        //     $table->dropIndex('idx_operation_items_dates');
        //     $table->dropIndex('idx_operation_items_item_stock');
        // });

        // حذف indexes من جدول journal_details (تخطي الموجود)
        // Schema::table('journal_details', function (Blueprint $table) {
        //     $table->dropIndex('idx_journal_details_account');
        //     $table->dropIndex('idx_journal_details_account_date');
        // });

        // حذف indexes من جدول barcodes (تخطي الموجود)
        // Schema::table('barcodes', function (Blueprint $table) {
        //     $table->dropIndex('idx_barcodes_item_barcode');
        // });

        // حذف indexes من جدول acc_head
        Schema::table('acc_head', function (Blueprint $table) {
            $table->dropIndex('idx_acc_head_code');
            $table->dropIndex('idx_acc_head_type');
        });
    }
};
