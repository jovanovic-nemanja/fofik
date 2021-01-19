<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ff_admin', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->string('email');
            $table->string('web_email');
            $table->string('contact');
            $table->string('admin_logo');
            $table->string('contact_address');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->string('facebook');
            $table->string('google');
            $table->string('twitter');
            $table->string('instagram');
            $table->string('pinerest');
            $table->dateTime('last_login');
            $table->dateTime('current__login');
            $table->string('last_ip');
            $table->string('current_ip');
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
        Schema::dropIfExists('ff_admin');
    }
}
