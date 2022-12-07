<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DateTreatment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('date_treatment',function(Blueprint $table){
            $table->unsignedBigInteger('treatment_id');
            $table->unsignedBigInteger('date_id');

            $table->foreign('treatment_id')->references('id')->on('treatments')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('date_id')->references('id')->on('dates')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('date_treatment');
    }
}
