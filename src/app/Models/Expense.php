<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Article;
use App\Models\Provider;

class Expense extends Model
{
  use HasFactory;

  protected $fillable = [
    'article_id',
    'provider_id',
    'total',
    'quantity'
  ];

  public function articles()
  {
    return $this->belongsToMany(Article::class);
  }

  public function provider()
  {
    return $this->belongsTo(Provider::class);
  }
}
