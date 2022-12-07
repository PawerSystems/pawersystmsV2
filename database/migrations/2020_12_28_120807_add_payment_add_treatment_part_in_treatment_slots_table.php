<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentAddTreatmentPartInTreatmentSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('treatment_slots', function (Blueprint $table) {
            $table->integer('payment_method_id')->after('comment')->nullable();
            $table->integer('treatment_part_id')->after('payment_method_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('treatment_slots', function (Blueprint $table) {
            $table->dropColumn('payment_method_id');
            $table->dropColumn('treatment_part_id');
        });
    }
}
