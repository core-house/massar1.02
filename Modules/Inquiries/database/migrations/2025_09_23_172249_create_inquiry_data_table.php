<?php

use Modules\Inquiries\Enums\KonTitle;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Inquiries\Enums\StatusForKon;
use Modules\Inquiries\Enums\InquiryStatus;
use Modules\Inquiries\Enums\KonPriorityEnum;
use Modules\Inquiries\Enums\ProjectSizeEnum;
use Illuminate\Database\Migrations\Migration;
use Modules\Inquiries\Enums\ClientPriorityEnum;
use Modules\Inquiries\Enums\QuotationStateEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('inquiry_name');
            $table->string('project_id')->nullable();

            $table->foreignId('work_type_id')->nullable()->constrained('work_types')->onDelete('set null');
            $table->foreignId('inquiry_source_id')->nullable()->constrained('inquiry_sources')->onDelete('set null');

            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('main_contractor_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('consultant_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('owner_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('assigned_engineer_id')->nullable()->constrained('clients')->onDelete('set null');

            $table->foreignId('town_id')->nullable()->constrained('towns')->onDelete('set null');

            $table->date('inquiry_date');
            $table->date('req_submittal_date')->nullable();
            $table->date('project_start_date')->nullable();


            $table->enum('status', array_column(InquiryStatus::cases(), 'value'))->default(InquiryStatus::JOB_IN_HAND->value);
            $table->enum('status_for_kon', array_column(StatusForKon::cases(), 'value'))->nullable()->default(StatusForKon::EXTENSION->value);
            $table->enum('kon_title', array_column(KonTitle::cases(), 'value'))->default(KonTitle::MAIN_PILING_CONTRACTOR->value);

            $table->enum('client_priority', ClientPriorityEnum::values())->nullable();
            $table->enum('kon_priority', KonPriorityEnum::values())->nullable();

            $table->enum('project_size', ProjectSizeEnum::values())->nullable();
            $table->enum('quotation_state', QuotationStateEnum::values())->nullable();


            $table->json('project_documents')->nullable();
            $table->json('submittals')->nullable();
            $table->json('work_conditions')->nullable();

            $table->integer('total_work_conditions_score')->default(0);
            $table->integer('project_difficulty')->default(1);

            $table->string('tender_number')->nullable();
            $table->string('tender_id')->nullable();
            $table->date('estimation_start_date')->nullable();
            $table->date('estimation_finished_date')->nullable();
            $table->date('submitting_date')->nullable();
            $table->decimal('total_project_value', 15, 2)->nullable();

            $table->string('rejection_reason')->nullable();
            $table->string('re_estimation_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry_data');
    }
};
