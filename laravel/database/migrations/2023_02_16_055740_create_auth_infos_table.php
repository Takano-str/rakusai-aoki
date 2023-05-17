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
        Schema::create('auth_infos', function (Blueprint $table) {
            $table->id();
            $table->string('access_token');
            $table->integer('expires_in');
            $table->string('scope');
            $table->string('token_type');
            $table->integer('created');
            $table->string('refresh_token');
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
        Schema::dropIfExists('auth_infos');
    }
};
