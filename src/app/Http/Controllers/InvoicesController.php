<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoicesController extends Controller
{
  public function query($order_number)
  {
    $invoice = Invoice::where('order_number', '=', $order_number)->first();

    if($invoice->customer_id === null) {
      return $invoice;
    } else {
      $item = Invoice::where('order_number', '=', $order_number)
      ->join('customers', 'customers.id', '=', 'invoices.customer_id')
      ->first();
      return $item;
    }
  }

  public static function store($request, $article, $total)
  {
    $invoice = new Invoice;
    $invoice->customer_id   = $request->customer_id;
    $invoice->order_number  = $request->order_number;
    $invoice->items         = 1;
    $invoice->discount      = $article->discount;
    $invoice->subtotal       = $article->price;
    $invoice->total         = $total;
    $invoice->save();
  }

  public static function update($request, $items, $subtotal, $total)
  {
    $invoice = Invoice::where('order_number', '=', $request->order_number)->first();
    $invoice->increment('items', $items);
    $invoice->increment('subtotal', $subtotal);
    $invoice->increment('total', $total);
  }
}
