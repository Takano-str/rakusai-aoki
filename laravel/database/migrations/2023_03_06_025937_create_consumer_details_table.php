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
        Schema::create('consumer_details', function (Blueprint $table) {
            $table->integer('consumer_id')->primary();
            $table->string('name')->nullable();
            $table->string('kana')->nullable();
            $table->string('tel')->nullable();
            $table->string('mail')->nullable();
            $table->string('address')->nullable();
            $table->string('gender')->nullable();
            $table->json('options')->nullable();
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
        Schema::dropIfExists('consumer_details');
    }
};
