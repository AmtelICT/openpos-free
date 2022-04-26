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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('email')->unique()->nullable()->default(null);
            $table->string('phone');
            $table->string('country_id');
            $table->string('state_id');
            $table->string('city_id');
            $table->string('address');
            $table->timestamps();
            $table->foreign('state_id')->references('id')->on('states')
            ->onUpdate('cascade')
            ->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')
            ->onUpdate('cascade')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('providers');
    }
};
