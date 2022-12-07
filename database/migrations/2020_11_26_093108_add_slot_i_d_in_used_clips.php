<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlotIDInUsedClips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('used_clips', function (Blueprint $table) {
            $table->integer('treatment_slot_id')->nullable()->after('treatment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('used_clips', function (Blueprint $table) {
            $table->dropColumn('treatment_slot_id');
        });
    }
}
