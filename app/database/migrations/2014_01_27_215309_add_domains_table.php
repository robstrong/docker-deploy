<?php

use Illuminate\Database\Migrations\Migration;

class AddDomainsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('domains', function($table) {
            $table->increments('id');
            $table->string('domain', 64)->unique();
            $table->timestamps();
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('domains');
	}

}
