<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'rut',
    'country_id',
    'state_id',
    'city_id',
    'address',
    'phone',
    'theme',
    'status'
  ];
}