<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('currencies', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true)->nullable();
			$table->string('code', 192)->nullable();
			$table->string('name', 192)->nullable();
			$table->string('symbol', 192)->nullable();
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
		Schema::drop('currencies');
	}

}
