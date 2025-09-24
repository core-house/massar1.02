`<?php

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
            Schema::create('inquiry_work_condition', function (Blueprint $table) {
                $table->primary(['inquiry_id', 'work_condition_id']);
                $table->foreignId('inquiry_id')->constrained('inquiry_data')->onDelete('cascade');
                $table->foreignId('work_condition_id')->constrained('work_conditions')->onDelete('cascade');
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('inquiry_work_condition_pivot');
        }
    };
