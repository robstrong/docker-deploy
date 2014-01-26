<?php

use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('tokens', function($table)
        {
            $table->increments('id');
            $table->string('authorization_server', 32);
            $table->string('access_token', 128);
            $table->string('refresh_token', 128)->nullable();
            $table->timestamp('expiration')->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
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
		Schema::drop('users');
	}

}
