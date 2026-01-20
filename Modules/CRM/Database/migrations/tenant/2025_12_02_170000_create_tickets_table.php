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
        Schema::create('crm_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->string('subject');
            $table->text('description');
            $table->enum('ticket_type', ['product_quality', 'delivery_delay', 'quantity_issue', 'invoice_error', 'technical_inquiry', 'visit_training'])->nullable();
            $table->string('ticket_reference')->nullable();
            $table->date('opened_date')->nullable();
            $table->date('response_deadline')->nullable();

            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->string('status_title')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');

            $table->foreignId('branch_id')->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_tickets');
    }
};
