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
    Schema::create('customers', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->bigInteger('points')->unsigned()->default(0);
      $table->string('dni');
      $table->string('phone');
      $table->string('country_id');
      $table->string('state_id');
      $table->string('city_id');
      $table->string('address');
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
    Schema::dropIfExists('customers');
  }
};
