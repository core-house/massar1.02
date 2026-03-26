<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('acc_head', function (Blueprint $table) {
            $table->string('address2', 255)->nullable()->after('address');
            $table->string('address3', 255)->nullable()->after('address2');
        });
    }

    public function down(): void
    {
        Schema::table('acc_head', function (Blueprint $table) {
            $table->dropColumn(['address2', 'address3']);
        });
    }
};
