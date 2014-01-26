<?php

use Illuminate\Database\Migrations\Migration;

class CreateRepos extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('repositories', function($table) {
            $table->increments('id');
            $table->string('owner', 64);
            $table->string('name', 64);
            $table->integer('auth_user_id')->unsigned()->nullable();
            $table->foreign('auth_user_id')->references('id')->on('users');
            $table->index('auth_user_id');
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
        Schema::drop('repositories');
	}

}
