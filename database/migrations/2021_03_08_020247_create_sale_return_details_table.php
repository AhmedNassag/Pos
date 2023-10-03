<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sale_return_details', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->float('price', 10, 0)->nullable();
			$table->float('TaxNet', 10, 0)->nullable()->default(0);
			$table->string('tax_method', 192)->nullable()->default('1');
			$table->float('discount', 10, 0)->nullable()->default(0);
			$table->string('discount_method', 192)->nullable()->default('1');
			$table->float('quantity', 10, 0)->nullable();
			$table->float('total', 10, 0)->nullable();

			$table->integer('sale_return_id')->index('return_id');
			$table->integer('product_id')->index('product_id_details_returns');
			$table->integer('product_variant_id')->nullable()->index('sale_return_id_product_variant_id');
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
		Schema::drop('sale_return_details');
	}

}
