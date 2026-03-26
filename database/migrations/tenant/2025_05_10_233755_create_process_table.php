<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessTable extends Migration
{
    public function up()
    {
        Schema::create('process', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->string('type');
            $table->timestamp('crtime')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('process');
    }
}
