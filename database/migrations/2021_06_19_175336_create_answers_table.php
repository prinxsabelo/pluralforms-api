<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->string('answer')->nullable();
            $table->boolean('submitted')->default(false);
            $table->integer('form_id');
            $table->string('token');
            $table->unsignedBigInteger('q_id')->constrained()->onDelete('cascade');
            $table->foreign('q_id')
            ->references('q_id')
            ->on('questions')
            ->onDelete('cascade');
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
        Schema::dropIfExists('answers');
    }
}
