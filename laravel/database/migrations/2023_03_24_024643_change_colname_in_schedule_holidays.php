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
        Schema::table('schedule_holidays', function (Blueprint $table) {
            $table->renameColumn('google_event_id', 'event_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_holidays', function (Blueprint $table) {
            $table->renameColumn('event_id', 'google_event_id');
        });
    }
};
