<?php

use Illuminate\Database\Migrations\Migration;

class AddNameToContainerAndRemoveSiteIdRequirement extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('containers', function($table) {
            $table->string('name', 32)->nullable();
            $table->string('addon_type', 32)->nullable();
        });
        DB::statement('ALTER TABLE containers ALTER COLUMN site_id DROP NOT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('containers', function($table) {
            $table->dropColumn('name');
            $table->dropColumn('addon_type');
        });
        DB::statement('ALTER TABLE containers ALTER COLUMN site_id SET NOT NULL');
	}

}
