<?php

use Illuminate\Database\Migrations\Migration;

class AddContainers extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('containers', function($table) {
            $table->increments('id');
            $table->string('docker_id', 64);
            $table->integer('site_id')->unsigned()->nullable();
            $table->foreign('site_id')->references('id')->on('sites');
            $table->index('site_id');
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
        Schema::drop('containers');
	}

}
