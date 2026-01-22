<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acc_groups', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('aname', 40)->unique();
            $table->tinyInteger('acc_type');
            $table->tinyInteger('parent_id')->nullable();
            $table->timestamp('crtime')->useCurrent();
            $table->timestamp('mdtime')->useCurrent()->useCurrentOnUpdate();
            $table->string('code', 30)->nullable();
            $table->boolean('isdeleted')->default(0);
            $table->integer('tenant')->default(0);
            $table->integer('branch')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acc_groups');
    }
};
