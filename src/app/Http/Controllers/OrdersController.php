<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Article;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\InvoicesController;

class OrdersController extends Controller
{
  public function query($order_number)
  {
    return Order::join('articles', 'articles.id', '=', 'orders.article_id')
    ->where('orders.order_number', '=', $order_number)->get();
  }

  public function get_code()
  {
    $item = Order::where('status', '=', 'pending')->first();
    
    if($item) { 
      return response()->json($item->order_number, 200); 
    }
    return response('', 204);
  }

  public function set_code()
  {
    $numbers = range(0, 999999);
    shuffle($numbers);
    $final = array_slice($numbers, 0, 1)[0];
    
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    // generate a pin based on 2 * 7 digits + a random character
    $pin = mt_rand(1000000, 9999999)
       . mt_rand(1000000, 9999999)
       . $characters[rand(0, strlen($characters) - 1)];
    // shuffle the result
    $string = str_shuffle($pin);
    
    return $string.str_pad($final + 1, 8, "0", STR_PAD_LEFT);
  }

  public static function store($request, $article) 
  {
    $order = new Order;
    $order->customer_id   = $request->customer_id;
    $order->article_id    = $article->id;
    $order->quantity      = 1;
    $order->amount        = $article->price;
    $order->order_number  = $request->order_number;
    $order->save();
  }

  public function generate_order(Request $request)
  {
    if(!$request->order_number) {
      return 'order_number is required';
    }

    $article  = Article::where('barcode', '=', $request->barcode)->first();
    $orders   = Order::where('order_number', '=', $request->order_number)->get();
    $invoices = Invoice::where('order_number', '=', $request->order_number)->first();

    if(!$article) {
      return 'This article doesn\'t exist';
    }

    if($article->discount > 0) {
      $total = $article->price * ($article->discount / 100);
    } else {
      $total = $article->price;
    }

    if($orders->isEmpty()) {
      $this::store($request, $article);
      if(!$invoices) {
        InvoicesController::store($request, $article, $total);
        $orders = Order::where('order_number', '=', $request->order_number)->get();
        return response()->json($orders, 201);
      } else {
        InvoicesController::update($request, 1, $article->price, $total);
        $orders = Order::where('order_number', '=', $request->order_number)->get();
        return response()->json($orders, 201);
      }
    }
    
    if(!$orders->isEmpty()) {
      $item = $orders->contains('article_id', $article->id);
      if($item) {
        $order_item = $orders->where('article_id', $article->id)->first();
        $order_item->increment('quantity', 1);
        $order_item->increment('amount', $article->price);
        InvoicesController::update($request, 1, $article->price, $total);
        return response()->json($orders, 201);
      } else {
        $this::store($request, $article);
        InvoicesController::update($request, 1, $article->price, $total);
        return response()->json($orders, 201);
      }
    }
  }

  public function quantity(Request $request)
  {
    if(
      !$request->article_id || 
      !$request->order_number ||
      !$request->quantity
    ){
      return response('Missing data', 204);
    }

    $item = Order::where('order_number', '=', $request->order_number)
      ->where('article_id', '=', $request->article_id)
      ->first();
    
    $article = Article::where('id', '=', $request->article_id)->first();
    
    $amount   = $article->price * $request->quantity;
    $total    = $amount * ($article->discount / 100);

    $item->update([
      'quantity'  => $request->quantity,
      'amount'    => $amount
    ]);

    return InvoicesController::update($request->order_number);
  }

  public function customer(Request $request)
  {
    if($request->order_number == '' ||
      $request->dni == '') {
      return [];
    }
     
    $customer = Customer::where('dni', '=', $request->dni)->first();

    $orders = Order::where('order_number', '=', $request->order_number)->get();

    $invoice = Invoice::where('order_number', '=', $request->order_number)->first();

    if(!$orders->isEmpty() && !$customer == null) {
      foreach($orders as $order) {
        $order->update([
          'customer_id' => $customer->id
        ]);
      }
    }

    if(!$invoice == null && !$customer == null) {
      $invoice->update([
        'customer_id' => $customer->id
      ]);
    }

     return $invoice;
  }

  public function payment(Request $request)
  { 
    if($request->order_number == '' ||
      $request->payment == '') {
      return [];
    }

    $orders = Order::where('order_number', '=', $request->order_number)->get();
    $invoice = Invoice::where('order_number', '=', $request->order_number)->first();
    $invoice->update([
      'status' => 'pagado'
    ]);

    if($invoice->customer_id != null) {
      $customer = Customer::where('id', '=', $invoice->customer_id)->first();
      $customer->update([
        'spent' => $invoice->total
      ]);
    }

    foreach($orders as $order) {
      $order->update([
        'status' => 'pagado'
      ]);
    }

    $cambio = $request->payment - $invoice->total;

    return $cambio;
  }

  public function delete(Request $request)
  {
    if($request->order_number == '' ||
      $request->barcode == '') {
      return 'algo pasa';
    }

    $article = Article::where('barcode', '=', $request->barcode)->first();

    if(!$article->isEmpty) {
      $order = Order::where('order_number', '=', $request->order_number)->get();

      $item = $order->where('article_id', '=', $article->id)->first();

      $invoice = Invoice::where('order_number', '=', $request->order_number)->first();

      $item->delete();

      $invoice->decrement('items', $item->quantity);
      $invoice->decrement('total', $item->subtotal);
    }

    return ;
  }

  public function cancel($order_number) 
  {
    $orders = Order::where('order_number', '=', $order_number)->get();
    $invoice = Invoice::where('order_number', '=', $order_number)->first();

    $invoice->delete();
    foreach($orders as $order) {
      $order->delete();
    }
  }

  public function discount(Request $request)
  {
    if($request->order_number == '' ||
    $request->article_id == '' || $request->discount == '') {
      return [];
    }

    $order = Order::where('order_number', '=', $request->order_number)->get();

    $item = $order->where('article_id', '=', $request->article_id)->first();

    $invoice = Invoice::where('order_number', '=', $request->order_number)->first();

    $percent = $request->discount / 100;
    
    $total = number_format($item->subtotal * $percent, 2);
    
    $item->update([
      'discount' => $total
    ]);

    if($invoice->discount !== 0) {
      $invoice->increment('discount', $total);
    } else {
      $invoice->update([
        'discount' => $total
      ]);
    }

    $factor = $invoice->total - $total;

    $invoice->update([
      'total' => $factor
    ]);

    return 0;
  }
}