<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropExampleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('examples');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('examples', function (Blueprint $table) {
            $table->id();
            $table->integer('meaning_id');
            $table->string('image');
            $table->string('meaning');
            $table->timestamps();
        });
    }
}
