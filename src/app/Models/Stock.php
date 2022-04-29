<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;

class Stock extends Model
{
  use HasFactory;

  protected $fillable = [
    'article_id',
    'stock'
  ];

  public function article()
  {
    return $this->belongsTo(Article::class);
  }
}
