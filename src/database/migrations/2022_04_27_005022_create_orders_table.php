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
    Schema::create('orders', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('customer_id')->nullable();
      $table->unsignedBigInteger('article_id')->nullable();
      $table->BigInteger('quantity')->unsigned();
      $table->decimal('amount', 8, 2);
      $table->text('order_number');
      $table->string('status')->default('pending');
      $table->integer('discount')->default(0);

      $table->foreign('customer_id')
        ->references('id')
        ->on('customers')
        ->onUpdate('cascade')
        ->onDelete('set null');

      $table->foreign('article_id')
        ->references('id')
        ->on('articles')
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
    Schema::dropIfExists('orders');
  }
};
