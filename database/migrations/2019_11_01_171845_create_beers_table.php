<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBeersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->text('description')->nullable();

            $table->unsignedBigInteger('manufacturer_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();

            $table->timestamps();

            $table->foreign('manufacturer_id')
            ->references('id')->on('manufacturers')
            ->onDelete('restrict');

            $table->foreign('type_id')
            ->references('id')->on('beer_types')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beers');
    }
}
