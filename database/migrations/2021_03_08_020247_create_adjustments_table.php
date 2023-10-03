<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('adjustments', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->date('date')->nullable();
			$table->string('Ref', 192)->nullable();
			$table->float('items', 10, 0)->nullable()->default(0);
			$table->text('notes')->nullable();
			// $table->integer('user_id')->index('user_id_adjustment');
			$table->integer('warehouse_id')->index('warehouse_id_adjustment');
			$table->timestamps(6);
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('adjustments');
	}

}
