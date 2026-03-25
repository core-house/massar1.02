<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->enum('type', ['person', 'company']);
            $table->string('address_1')->nullable();
            $table->string('address_2')->nullable();
            $table->string('tax_number')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('inquiries_roles')->nullOnDelete();
            $table->foreign('parent_id')->references('id')->on('contacts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
