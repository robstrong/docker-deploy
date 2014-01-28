<?php

use Illuminate\Database\Migrations\Migration;

class AddDomainIdToSitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('sites', function($table) {
            $table->integer('domain_id')->unsigned()->nullable();
            $table->foreign('domain_id')->references('id')->on('domains');
            $table->index('domain_id');
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
            $table->dropColumn('domain_id');
        });
	}

}
