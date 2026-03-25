<?php

declare(strict_types=1);

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
        Schema::create('user_gamification', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->bigInteger('points')->default(0);
            $table->integer('level')->default(1);
            $table->integer('streak')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_points_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points');
            $table->string('event_type'); // login, create, update, delete, report
            $table->string('description')->nullable();
            $table->nullableMorphs('source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_points_history');
        Schema::dropIfExists('user_gamification');
    }
};
