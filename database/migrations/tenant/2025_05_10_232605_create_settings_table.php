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
        Schema::create('settings', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('company_name', 200)->nullable();
            $table->string('company_add', 200)->nullable();
            $table->string('company_email', 50)->nullable();
            $table->string('company_tel', 200)->nullable();
            $table->string('edit_pass', 50)->nullable();
            $table->string('lic', 250)->nullable();
            $table->text('updateline')->nullable();
            $table->integer('acc_rent')->default(0);
            $table->date('startdate')->nullable();
            $table->date('enddate')->nullable();
            $table->string('lang', 20)->default('ar');
            $table->string('bodycolor', 50)->nullable();
            $table->boolean('showhr')->default(1);
            $table->boolean('showclinc')->default(1);
            $table->boolean('showatt')->default(1);
            $table->boolean('showpayroll')->default(1);
            $table->boolean('showrent')->default(1);
            $table->boolean('showpay')->default(1);
            $table->boolean('showtsk')->default(1);
            $table->integer('def_pos_client')->nullable();
            $table->integer('def_pos_store')->nullable();
            $table->integer('def_pos_employee')->nullable();
            $table->integer('def_pos_fund')->nullable();
            $table->boolean('isdeleted')->default(0);
            $table->integer('tenant')->default(0);
            $table->integer('branch')->default(0);
            $table->boolean('show_all_tasks')->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
