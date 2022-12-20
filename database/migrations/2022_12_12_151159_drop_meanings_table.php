<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropMeaningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('meanings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('meanings', function (Blueprint $table) {
            $table->id();
            $table->string('word');
            $table->string('meaning');
            $table->string('dongnghia');
            $table->string('trainghia');
            $table->string('hiragana');
            $table->timestamps();
        });
    }
}
