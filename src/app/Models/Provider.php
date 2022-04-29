<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Expense;

class Provider extends Model
{
  use HasFactory;

  protected $fillable = [
    'company',
    'email',
    'phone',
    'country_id',
    'state_id',
    'city_id',
    'address'
  ];
  
  public function expense()
  {
    return $this->hasOne(Expense::class);
  }
}
