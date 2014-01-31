<?php

use Illuminate\Database\Migrations\Migration;

class AddIpToContainers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('containers', function($table) {
            $table->string('ip')->nullable();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('containers', function($table) {
            $table->dropColumn('ip');
        });
	}

}
