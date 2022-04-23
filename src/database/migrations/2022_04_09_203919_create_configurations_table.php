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
    Schema::create('configurations', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('rut')->nullable();
      $table->integer('country_id')->unsigned();
      $table->integer('state_id')->unsigned();
      $table->integer('city_id')->unsigned();
      $table->string('address');
      $table->string('phone');
      $table->string('theme')->default('dark');
      $table->string('status')->default('open');
      $table->timestamps();
      $table->foreign('country_id')->references('id')->on('countries')
      ->onUpdate('cascade')
      ->onDelete('cascade');
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
    Schema::dropIfExists('configurations');
  }
};
