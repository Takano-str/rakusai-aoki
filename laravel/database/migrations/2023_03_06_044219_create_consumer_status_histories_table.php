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
        Schema::create('consumer_status_histories', function (Blueprint $table) {
            $table->integer('consumer_id');
            $table->integer('history_number');
            $table->integer('status_code');
            $table->string('changer_id')->nullable();
            $table->timestamps();
            $table->unique(['consumer_id', 'history_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumer_status_histories');
    }
};
