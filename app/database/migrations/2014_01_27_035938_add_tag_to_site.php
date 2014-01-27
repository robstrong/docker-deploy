<?php

use Illuminate\Database\Migrations\Migration;

class AddTagToSite extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('sites', function($table) {
            $table->string('tag', 64)->nullable();
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
            $table->dropColumn('tag');
        });
	}

}
