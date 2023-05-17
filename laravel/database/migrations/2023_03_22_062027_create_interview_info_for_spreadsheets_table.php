<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interview_info_for_spreadsheets', function (Blueprint $table) {
            $table->id();
            $table->integer('consumer_id');
            $table->integer('schedule_id')->nullable();
            $table->string('decide_date')->nullable();
            $table->string('write_status')->nullable()->default('not_yet');
            $table->json('option')->nullable();
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
        Schema::dropIfExists('interview_info_for_spreadsheets');
    }
};
