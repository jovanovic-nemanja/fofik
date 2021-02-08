<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCvPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ff_cv_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cv_id');
            $table->string('photo');
            $table->timestamps();
            $table->foreign('cv_id')->references('id')->on('ff_cv_resource')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ff_cv_photos');
    }
}
