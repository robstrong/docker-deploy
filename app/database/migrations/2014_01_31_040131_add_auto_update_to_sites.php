<?php

use Illuminate\Database\Migrations\Migration;

class AddAutoUpdateToSites extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('sites', function($table) {
            $table->boolean('auto_update')->default(true);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('sites', function($table) {
            $table->dropColumn('auto_update');
        });
	}

}
