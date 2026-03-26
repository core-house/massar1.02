<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatesTable extends Migration
{

    public function up()
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('states');
    }
}
