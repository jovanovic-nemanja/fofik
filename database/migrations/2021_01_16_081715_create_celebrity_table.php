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
            $table->string('photo_url')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('death_date')->nullable();
            $table->dateTime('active_start_date')->nullable();
            $table->dateTime('active_end_date')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
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
