<?php

use Illuminate\Database\Migrations\Migration;

class CreateSites extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('sites', function($table) {
            $table->increments('id');
            $table->string('subdomain', 128);
            $table->string('branch', 128);
            $table->integer('repository_id')->unsigned()->nullable();
            $table->foreign('repository_id')->references('id')->on('repositories');
            $table->index('repository_id');
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
        Schema::drop('sites');
	}

}
