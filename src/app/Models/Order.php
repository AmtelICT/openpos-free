<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Article;
use App\Models\Invoice;

class Order extends Model
{
  use HasFactory;

  protected $fillable = [
    'customer_id',
    'article_id',
    'quantity',
    'amount',
    'order_number',
    'status',
    'discount'
  ];

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function articles()
  {
    return $this->belongsToMany(Article::class);
  }

  public function invoice()
  {
    return $this->hasOne(Invoice::class);
  }
}
