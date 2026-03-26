<?php

declare(strict_types=1);

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
        Schema::table('acc_head', function (Blueprint $table) {
            if (!Schema::hasColumn('acc_head', 'zatca_name')) {
                $table->string('zatca_name', 100)->nullable()->after('aname');
            }
            if (!Schema::hasColumn('acc_head', 'vat_number')) {
                $table->string('vat_number', 50)->nullable()->after('zatca_name');
            }
            if (!Schema::hasColumn('acc_head', 'national_id')) {
                $table->string('national_id', 50)->nullable()->after('vat_number');
            }
            if (!Schema::hasColumn('acc_head', 'zatca_address')) {
                $table->string('zatca_address', 250)->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('acc_head', 'company_type')) {
                $table->string('company_type', 50)->nullable()->after('zatca_address');
            }
            if (!Schema::hasColumn('acc_head', 'nationality')) {
                $table->string('nationality', 50)->nullable()->after('company_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acc_head', function (Blueprint $table) {
            $table->dropColumn([
                'zatca_name',
                'vat_number',
                'national_id',
                'zatca_address',
                'company_type',
                'nationality',
            ]);
        });
    }
};
