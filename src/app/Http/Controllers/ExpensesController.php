<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class ExpensesController extends Controller
{
  public static function store($request, $article)
  {
    $expense = new Expense;
    $expense->article_id    = $article->id;
    $expense->provider_id   = $request->provider_id;
    $expense->total         = $request->buy_price;
    $expense->quantity      = $request->quantity;
    $expense->save();

    return null;
  }
}
