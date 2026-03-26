<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('quotation_units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('quotation_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('inquiry_quotation_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inquiry_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_type_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_unit_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiry_quotation_info');
        Schema::dropIfExists('quotation_units');
        Schema::dropIfExists('quotation_types');
    }
};
