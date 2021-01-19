<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ff_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('celeb_id');
            $table->unsignedBigInteger('person_id');
            $table->string('rel_type');
            $table->foreign('person_id')->references('id')->on('ff_persons')->onDelete('cascade');
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
        Schema::dropIfExists('ff_relations');
    }
}
