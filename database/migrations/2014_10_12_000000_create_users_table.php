<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('username');
			$table->string('email')->unique();
			$table->string('password', 60);
			$table->string('first_name');
			$table->string('last_name');
			$table->enum('main_role', ['student', 'teacher'])->default( 'teacher' );
			$table->text('subjects');
			$table->string('rate');
			$table->string('county');
			$table->text('bio');
			$table->string('picture');
			$table->string('cover_picture');
			$table->rememberToken();
			$table->timestamps();
		});

		DB::statement('ALTER TABLE `users` ADD FULLTEXT `search`(subjects)');
		DB::statement('ALTER TABLE `users` ADD FULLTEXT `bio`(bio)');

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table) {
			$table->dropIndex('search');
        });

        Schema::table('users', function($table) {
			$table->dropIndex('bio');
        });


		Schema::drop('users');
	}

}
