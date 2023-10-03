<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToAdjustmentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('adjustments', function(Blueprint $table)
		{
			// $table->foreign('user_id', 'adjustment_user_id')->references('id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('warehouse_id', 'adjustment_warehouse_id')->references('id')->on('warehouses')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			
			$table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
			// $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->cascadeOnDelete()->cascadeOnUpdate();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('adjustments', function(Blueprint $table)
		{
			$table->dropForeign('user_id_adjustment');
			$table->dropForeign('warehouse_id_adjustment');
		});
	}

}
