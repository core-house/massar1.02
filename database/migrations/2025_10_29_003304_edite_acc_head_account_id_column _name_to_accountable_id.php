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
        Schema::table('acc_head', function (Blueprint $table) {
            $table->renameColumn('account_id', 'accountable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('acc_head', function (Blueprint $table) {
            $table->renameColumn('accountable_id', 'account_id');
        });
    }
};
