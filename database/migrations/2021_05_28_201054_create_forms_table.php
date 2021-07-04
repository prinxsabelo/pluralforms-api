<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->bigIncrements('form_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('avatar')->nullable();
            $table->string('status')->default('ACTIVE');
            $table->string('begin_header')->default('Welcome')->nullable();
            $table->string('begin_desc')->default('Hi there, please fill out and submit this form.')->nullable();
            $table->string('end_header')->default('Thank You!')->nullable();
            $table->string('end_desc')->default('Your submission has been received!')->nullable();
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
        Schema::dropIfExists('forms');
    }
}
