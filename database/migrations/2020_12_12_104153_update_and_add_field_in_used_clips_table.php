<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAndAddFieldInUsedClipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('used_clips', function (Blueprint $table) {
            //$table->integer('treatment_id')->nullable()->change();
            //$table->integer('treatment_slot_id')->nullable()->change();
            $table->integer('event_id')->after('treatment_slot_id')->nullable();
            $table->integer('event_slot_id')->after('event_id')->nullable();
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
            $table->dropColumn('event_id');
            $table->dropColumn('event_slot_id');
        });
    }
}
