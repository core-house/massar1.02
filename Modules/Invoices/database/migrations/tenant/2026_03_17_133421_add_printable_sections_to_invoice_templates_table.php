<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_templates', function (Blueprint $table) {
            $table->json('printable_sections')->nullable()->after('column_order')
                ->comment('Sections to be printed in invoice (company_info, customer_info, etc.)');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_templates', function (Blueprint $table) {
            $table->dropColumn('printable_sections');
        });
    }
};
