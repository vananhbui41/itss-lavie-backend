<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMeaningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meanings', function (Blueprint $table) {
            $table->string('explanation_of_meaning')->nullable()->change();
            $table->string('example')->nullable()->change();
            $table->string('example_meaning')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meanings', function (Blueprint $table) {
            $table->string('explanation_of_meaning')->nullable(false)->change();
                $table->string('example')->nullable(false)->change();
                $table->string('example_meaning')->nullable(false)->change();
        });

    }
}
