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
        Schema::table('work_items', function (Blueprint $table) {
            if (! Schema::hasColumn('work_items', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('description')->constrained('work_item_categories')->nullOnDelete();
            } else {
                // Column exists, just add the foreign key constraint if it doesn't exist
                if (! $this->foreignKeyExists('work_items', 'work_items_category_id_foreign')) {
                    $table->foreign('category_id')->references('id')->on('work_item_categories')->onDelete('set null');
                }
            }
        });
    }

    /**
     * Check if a foreign key constraint exists.
     */
    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();

        $result = $connection->selectOne(
            "SELECT COUNT(*) as count
             FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = ?
             AND TABLE_NAME = ?
             AND CONSTRAINT_NAME = ?
             AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$database, $table, $constraintName]
        );

        return $result->count > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_items', function (Blueprint $table) {
            if ($this->foreignKeyExists('work_items', 'work_items_category_id_foreign')) {
                $table->dropForeign(['category_id']);
            }
            if (Schema::hasColumn('work_items', 'category_id')) {
                $table->dropColumn('category_id');
            }
        });
    }
};
