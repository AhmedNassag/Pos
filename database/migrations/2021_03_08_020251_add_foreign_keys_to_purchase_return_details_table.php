<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToPurchaseReturnDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('purchase_return_details', function(Blueprint $table)
		{
			$table->foreign('product_id', 'product_id_details_purchase_return')->references('id')->on('products')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('purchase_return_id', 'purchase_return_id_return')->references('id')->on('purchase_returns')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('product_variant_id', 'purchase_return_product_variant_id')->references('id')->on('product_variants')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			
			// $table->foreignId('product_id')->nullable()->constrained('products')->cascadeOnDelete()->cascadeOnUpdate();
			// $table->foreignId('purchase_return_id')->nullable()->constrained('purchase_returns')->cascadeOnDelete()->cascadeOnUpdate();
			// $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete()->cascadeOnUpdate();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('purchase_return_details', function(Blueprint $table)
		{
			$table->dropForeign('product_id_details_purchase_return');
			$table->dropForeign('purchase_return_id_return');
			$table->dropForeign('purchase_return_product_variant_id');
		});
	}

}
