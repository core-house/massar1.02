<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_organization_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('job_title_in_org')->nullable(); // الوظيفة في هذه المؤسسة
            $table->boolean('is_primary')->default(false); // هل هي المؤسسة الأساسية؟
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_organization_relations');
    }
};
