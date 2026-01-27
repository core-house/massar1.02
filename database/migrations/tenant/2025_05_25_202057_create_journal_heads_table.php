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
        Schema::create('journal_heads', function (Blueprint $table) {
            $table->id();
            $table->integer('journal_id');
            $table->double('total');
            $table->date('date');
            $table->integer('op_id')->nullable();
            $table->integer('pro_type')->nullable();
            $table->string('details', 250)->nullable();
            $table->timestamp('crtime')->useCurrent();
            $table->integer('op2')->default(0);
            $table->tinyInteger('isdeleted')->default(0);
            $table->timestamp('mdtime')->useCurrent()->useCurrentOnUpdate();
            $table->integer('user')->nullable();
            $table->integer('tenant')->default(0);
            $table->integer('branch')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_heads');
    }
};
