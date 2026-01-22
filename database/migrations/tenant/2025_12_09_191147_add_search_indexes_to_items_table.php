<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // ✅ إضافة فهرس للبحث السريع بالاسم
            if (! $this->hasIndex('items', 'items_name_index')) {
                $table->index('name', 'items_name_index');
            }

            // ✅ إضافة فهرس للبحث السريع بالكود
            if (! $this->hasIndex('items', 'items_code_index')) {
                $table->index('code', 'items_code_index');
            }

            // ✅ إضافة فهرس مركب للبحث بالنوع والاسم
            if (! $this->hasIndex('items', 'items_type_name_index')) {
                $table->index(['type', 'name'], 'items_type_name_index');
            }

            // ✅ إضافة فهرس مركب للبحث بالنوع والكود
            if (! $this->hasIndex('items', 'items_type_code_index')) {
                $table->index(['type', 'code'], 'items_type_code_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropIndex('items_name_index');
            $table->dropIndex('items_code_index');
            $table->dropIndex('items_type_name_index');
            $table->dropIndex('items_type_code_index');
        });
    }

    /**
     * Check if index exists
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        try {
            if (DB::getDriverName() === 'mysql') {
                $indexes = DB::select("SHOW INDEXES FROM {$table}");
                foreach ($indexes as $index) {
                    if ($index->Key_name === $indexName) {
                        return true;
                    }
                }
            } elseif (DB::getDriverName() === 'sqlite') {
                $indexes = DB::select("PRAGMA index_list({$table})");
                foreach ($indexes as $index) {
                    if ($index->name === $indexName) {
                        return true;
                    }
                }
            } elseif (DB::getDriverName() === 'pgsql') {
                $indexes = DB::select('
                    SELECT indexname 
                    FROM pg_indexes 
                    WHERE tablename = ? AND indexname = ?
                ', [$table, $indexName]);

                return count($indexes) > 0;
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }
};
