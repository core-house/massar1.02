<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftsTable extends Migration {

	public function up()
	{
		Schema::create('shifts', function(Blueprint $table) {
			$table->id();
			$table->time('start_time');
			$table->time('end_time');
			$table->enum('shift_type', array('morning', 'evening', 'night'));
			$table->text('notes')->nullable();
			$table->json('days');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('shifts');
	}
}
