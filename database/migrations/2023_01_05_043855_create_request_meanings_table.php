<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestMeaningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 
        Schema::create('request_meanings', function (Blueprint $table) {
            $table->id();
            $table->integer('request_id');
            $table->string('meaning');
            $table->string('explanation_of_meaning');
            $table->string('source')->nullable();
            $table->string('context')->nullable();
            $table->string('topic')->nullable();
            $table->string('example')->nullable();
            $table->string('example_meaning')->nullable();
            $table->string('image')->nullable('true');
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
        Schema::dropIfExists('request_meanings');
    }
}
