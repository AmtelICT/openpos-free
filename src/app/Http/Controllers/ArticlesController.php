<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticlesController extends Controller
{
  public function query($barcode)
  {
    return Article::where('barcode', '=', $barcode)->first();
  }

  public static function store($request)
  {
    $article = new Article;
    $article->barcode = $request->barcode;
    $article->name    = $request->name;
    $article->measure = $request->measure;
    $article->price   = $request->price;
    $article->save();
    
    return $article;
  }
}
