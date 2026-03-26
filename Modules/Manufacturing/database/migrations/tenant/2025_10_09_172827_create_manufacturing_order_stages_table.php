<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Manufacturing\Enums\ManufacturingStageStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manufacturing_order_stage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manufacturing_order_id')->constrained('manufacturing_orders')->onDelete('cascade');
            $table->foreignId('manufacturing_stage_id')->constrained('manufacturing_stages')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->decimal('estimated_duration', 8, 2)->default(0.00);
            $table->decimal('actual_duration', 8, 2)->nullable();
            $table->enum('status', array_column(ManufacturingStageStatus::cases(), 'value'))
                ->default(ManufacturingStageStatus::PENDING->value);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();

            $table->index(['manufacturing_order_id', 'order']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manufacturing_order_stage');
    }
};
