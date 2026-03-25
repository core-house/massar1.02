<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('help_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('help_categories')->cascadeOnDelete();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->longText('content');
            $table->longText('content_en')->nullable();
            $table->string('route_key')->nullable()->index();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_articles');
    }
};
