<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Article;
use Illuminate\Http\Request;

class StocksController extends Controller
{
  public function index()
  {
    return Stock::join('articles', 'stocks.article_id', '=', 'articles.id')
      ->get();
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'barcode'     => 'required',
      'provider_id' => 'required|integer',
      'buy_price'   => 'required|integer',
      'name'        => 'required',
      'measure'     => 'required',
      'price'       => 'required|integer',      
      'quantity'    => 'required|integer'
    ]);

    $article = ArticlesController::store($request);

    ExpensesController::store($request, $article);
 
    $stock = new Stock;
    $stock->article_id = $article->id;
    $stock->stock = $request->quantity;
    $stock->save();

    return response()->json('', 204);
  }

  public function update(Request $request)
  {
    
    $stock = Stock::where('id', '=', $request->id)->first();
    
    $article = Article::where('id', '=', $stock->article_id)->first();  

    $stock->update($request->all());

    $article->update($request->all());

    return response()->json('', 204);
  }

  public function buy(Request $request)
  {
    $this->validate($request, [
      'name'          => 'required',
      'provider_id'   => 'required',
      'buy_price'     => 'required',
      'quantity'      => 'required'
    ]);

    $stock = Stock::where('id', '=', $request->id)
      ->first();
    
    $article = Article::where('id', '=', $stock->article_id)
      ->first(); 
    
    ExpensesController::store($request, $article);

    $stock->increment('stock', $request->quantity);

    return response()->json('', 204);
  }

  public function delete($id)
  {
    $stock = Stock::where('id', '=', $id)->first();
    
    $article = Article::where('id', '=', $stock->article_id)->first();

    $stock->article->delete();
    $stock->delete();
    
    return response()->json($stock, 204);
  }

  public function query($id)
  {
    return Stock::join('articles', 'stocks.article_id', '=', 'articles.id')
      ->where('stocks.article_id', '=', $id)
      ->first();
  }
}