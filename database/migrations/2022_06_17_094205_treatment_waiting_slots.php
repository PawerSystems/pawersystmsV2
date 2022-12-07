<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TreatmentWaitingSlots extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('treatment_waiting_slots', function (Blueprint $table) {
            $table->id();
            $table->integer('business_id');
            $table->integer('date_id');
            $table->integer('user_id');
            $table->integer('treatment_id');
            $table->integer('department_id')->nullable();
            $table->string('status');
            $table->string('time');
            $table->longText('comment')->nullable();
            $table->integer('is_active')->default(1);
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
        Schema::dropIfExists('treatment_waiting_slots');
    }
}
