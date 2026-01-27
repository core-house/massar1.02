<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id(); // العمود id تلقائي
            $table->string('cname', 200); // اسم مركز التكلفة
            $table->unsignedBigInteger('parent_id')->nullable(); // دعم الهرمية
            $table->string('info')->nullable();
            $table->integer('tenant')->default(0);
            $table->integer('branch')->default(0);
            $table->tinyInteger('deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
    }
};
