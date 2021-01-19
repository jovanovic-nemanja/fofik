<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCelebrityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ff_celebs', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('fullname');
            $table->string('photo_url');
            $table->dateTime('birth_date');
            $table->dateTime('death_date');
            $table->string('born_in');
            $table->string('citizen_ship');
            $table->text('education');
            $table->string('occupation');
            $table->string('net_worth');
            $table->text('award');
            $table->text('early_life');
            $table->text('career');
            $table->text('filmography');
            $table->text('personal_life');
            $table->text('activities');
            $table->dateTime('active_start_date');
            $table->dateTime('active_end_date');
            $table->string('facebook');
            $table->string('instagram');
            $table->string('twitter');
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
        Schema::dropIfExists('ff_celebs');
    }
}
