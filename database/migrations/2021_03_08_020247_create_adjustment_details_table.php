<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdjustmentDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('adjustment_details', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->float('quantity', 10, 0)->nullable();
			$table->string('type', 192)->nullable();
			$table->integer('product_id')->index('adjust_product_id');
			$table->integer('adjustment_id')->index('adjust_adjustment_id');
			$table->integer('product_variant_id')->nullable()->index('adjust_product_variant');
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
		Schema::drop('adjustment_details');
	}

}