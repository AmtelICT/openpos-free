<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Invoices;

class Customer extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'points',
    'dni',
    'phone',
    'country_id',
    'state_id',
    'city_id',
    'address'
  ];

  public function invoices() 
  {
    return $this->hasMany(Invoices::class);
  }
}
