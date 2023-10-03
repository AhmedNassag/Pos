<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentSaleReturnsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_sale_returns', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->date('date')->nullable();
			$table->string('Ref', 192)->nullable();
			$table->float('montant', 10, 0)->nullable();
			$table->string('Reglement', 191)->nullable();
			$table->text('notes')->nullable()->nullable();
			
			// $table->integer('user_id')->index('factures_sale_return_user_id');
			$table->integer('sale_return_id')->index('factures_sale_return');
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
		Schema::drop('payment_sale_returns');
	}

}
