<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'surname',
    'intake',
    'dni',
    'phone',
    'country_id',
    'state_id',
    'city_id',
    'address'
  ];
}
