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
    Schema::create('expenses', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('article_id');
      $table->unsignedBigInteger('provider_id');
      $table->decimal('total', 8, 2);
      $table->bigInteger('quantity')->unsigned();

      $table->foreign('article_id')
        ->references('id')
        ->on('articles')
        ->onUpdate('cascade')
        ->onDelete('cascade');

      $table->foreign('provider_id')
        ->references('id')
        ->on('providers')
        ->onUpdate('cascade')
        ->onDelete('cascade');
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
    Schema::dropIfExists('expenses');
  }
};
