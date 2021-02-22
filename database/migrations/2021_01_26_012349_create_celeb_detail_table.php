<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCelebDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ff_celeb_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('celeb_id');
            $table->string('en_name')->nullable();
            $table->string('natl_name')->nullable();
            $table->string('comment')->nullable();
            $table->string('born_in')->nullable();
            $table->string('citizen_ship')->nullable();
            $table->string('spouse')->nullable();
            $table->string('children')->nullable();
            $table->text('education')->nullable();
            $table->string('occupation')->nullable();
            $table->string('net_worth')->nullable();
            $table->text('award')->nullable();
            $table->longtext('description')->nullable();
            $table->string('lang')->nullable();
            $table->foreign('celeb_id')->references('id')->on('ff_celebs')->onDelete('cascade');

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
        Schema::dropIfExists('ff_celeb_detail');
    }
}
