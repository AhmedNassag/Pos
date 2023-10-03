<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPurchaseReturnsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_purchase_returns', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->integer('id', true);
			$table->date('date')->nullable();
			$table->string('Ref', 192)->nullable();
			$table->float('montant', 10, 0)->nullable();
			$table->string('Reglement', 191)->nullable();
			$table->text('notes')->nullable();
			
			// $table->integer('user_id')->index('user_id_payment_return_purchase');
			$table->integer('purchase_return_id')->index('supplier_id_payment_return_purchase');
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
		Schema::drop('payment_purchase_returns');
	}

}
