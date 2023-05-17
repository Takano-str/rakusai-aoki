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
        Schema::table('schedules', function (Blueprint $table) {
            $table->integer("booking_count")->nullable()->after("type");
            $table->integer("capacity")->nullable()->after("booking_count");
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
        Schema::table('schedules', function (Blueprint $table) {
            $table->renameColumn('event_id', 'google_event_id');
            $table->dropColumn("booking_count");
            $table->dropColumn("capacity");
        });
    }
};
