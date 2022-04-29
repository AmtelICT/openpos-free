<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Order;

class Invoice extends Model
{
  use HasFactory;

  protected $fillable = [
    'customer_id',
    'order_number',
    'items',
    'subtotal',
    'total',
    'status',
    'discount'
  ];

  public function customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function orders()
  {
    return $this->belongsToMany(Order::class);
  }
}
