<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {

            $table->string('id')->primary();
            $table->string('name')->nullable();
            $table->string('domain')->unique();

            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_size')->nullable();
            $table->string('admin_email')->unique()->nullable();
            $table->string('user_position')->nullable();

            $table->string('referral_code')->nullable();
            $table->foreignId('plan_id')->constrained('plans');

            $table->timestamp('subscription_start_at')->nullable();
            $table->timestamp('subscription_end_at')->nullable();

            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->json('data')->nullable();
            $table->json('enabled_modules')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
