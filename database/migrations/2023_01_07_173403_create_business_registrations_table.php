<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('name');
            $table->string('email');
            $table->string('number');
            $table->integer('plan');
            $table->tinyInteger('status')->default(1);
            $table->longText('message')->nullable();
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
        Schema::dropIfExists('business_registrations');
    }
}
