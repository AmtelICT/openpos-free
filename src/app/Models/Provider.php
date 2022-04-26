<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
