<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Stock;

class Article extends Model
{
  use HasFactory;

  protected $fillable = [
    'barcode',
    'name',
    'measure',
    'price',
    'discount',
    'points'
  ];

  public function orders()
  {
    return $this->hasMany(Order::class);
  }

  public function invoice()
  {
    return $this->hasOne(Invoice::class);
  }

  public function expense()
  {
    return $this->hasOne(Expense::class);
  }

  public function stock(){
    return $this->hasOne(Stock::class);
  }
}
