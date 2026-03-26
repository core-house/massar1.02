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
            $table->text('preamble_text')->nullable()->after('printable_sections');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_templates', function (Blueprint $table) {
            $table->dropColumn('preamble_text');
        });
    }
};
