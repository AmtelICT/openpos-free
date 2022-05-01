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
      $table->unsignedBigInteger('article_id')->nullable();
      $table->unsignedBigInteger('provider_id')->nullable();
      $table->decimal('total', 8, 2);
      $table->bigInteger('quantity')->unsigned();

      $table->foreign('article_id')
        ->references('id')
        ->on('articles')
        ->onUpdate('cascade')
        ->onDelete('set null');

      $table->foreign('provider_id')
        ->references('id')
        ->on('providers')
        ->onUpdate('cascade')
        ->onDelete('set null');
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
