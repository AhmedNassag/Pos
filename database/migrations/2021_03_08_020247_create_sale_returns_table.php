<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleReturnsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sale_returns', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->date('date')->nullable();
			$table->string('Ref', 192)->nullable();
			$table->float('tax_rate', 10, 0)->nullable()->default(0);
			$table->float('TaxNet', 10, 0)->nullable()->default(0);
			$table->float('discount', 10, 0)->nullable()->default(0);
			$table->float('shipping', 10, 0)->nullable()->default(0);
			$table->float('GrandTotal', 10, 0)->nullable();
			$table->float('paid_amount', 10, 0)->default(0)->nullable();
			$table->string('payment_status', 192)->nullable();
			$table->string('status', 192)->nullable();
			$table->text('notes')->nullable();

			// $table->integer('user_id')->index('user_id_returns');
			$table->integer('client_id')->index('client_id_returns');
			$table->integer('warehouse_id')->index('warehouse_id_sale_return_id');
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
		Schema::drop('sale_returns');
	}

}
