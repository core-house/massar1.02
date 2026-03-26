<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateLeavesTable extends Migration {

	public function up()
	{
		Schema::create('leaves', function(Blueprint $table) {
			$table->id();
			$table->bigInteger('employee_id')->unsigned();
			$table->date('start_date');
			$table->date('end_date');
			$table->text('reason')->nullable();
			$table->enum('status', ['pending', 'approved', 'rejected']);
			$table->datetime('applied_at');
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::drop('leaves');
	}
}
