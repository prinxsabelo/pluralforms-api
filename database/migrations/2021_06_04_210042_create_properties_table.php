<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->increments('property_id');
            $table->string('shape')->default("star");
            $table->boolean('allow_multiple_selection')->default(false);
            $table->boolean('required')->default(false);
            $table->boolean('randomize')->default(false);
            $table->unsignedBigInteger('q_id')->constrained()->onDelete('cascade');
            $table->foreign('q_id')
            ->references('q_id')
            ->on('questions')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
