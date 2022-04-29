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
    Schema::create('invoices', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('customer_id')->nullable();
      $table->string('order_number');
      $table->bigInteger('items')->unsigned()->default(0);
      $table->decimal('subtotal', 8, 2)->default(0);
      $table->decimal('total', 8, 2)->default(0);
      $table->string('status')->default('pending');
      $table->decimal('discount', 8, 2)->default(0);

      $table->foreign('customer_id')
        ->references('id')
        ->on('customers')
        ->onUpdate('cascade')
        ->onDelete('cascade');

      $table->foreign('order_number')
        ->references('order_number')
        ->on('orders')
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
    Schema::dropIfExists('invoices');
  }
};
