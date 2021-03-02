<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ff_users', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('social_id');
            $table->string('social_site');
            $table->string('lang');
            $table->string('platform');
            $table->string('device_id');
            $table->string('fb_token');
            $table->text('access_token');
            $table->rememberToken();
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('ff_users');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
