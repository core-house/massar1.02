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
        Schema::create('journal_details', function (Blueprint $table) {
            $table->id();
            $table->integer('journal_id');
            $table->integer('account_id');
            $table->integer('debit')->default(0);
            $table->integer('credit')->default(0);
            $table->tinyInteger('type');
            $table->string('info', 150)->nullable();
            $table->timestamp('crtime')->useCurrent();
            $table->integer('op2')->default(0);
            $table->integer('op_id')->default(0);
            $table->tinyInteger('isdeleted')->default(0);
            $table->timestamp('mdtime')->useCurrent()->useCurrentOnUpdate();
            $table->integer('tenant')->default(0);
            $table->integer('branch')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_details');
    }
};
