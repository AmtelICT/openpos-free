<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;

class City extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'state_id',
    'state_code',
    'country_id',
    'country_code',
    'latitude',
    'longitude',
    'flag',
    'wikiDataId'
  ];

  public function state()
  {
    return $this->belongsTo(State::class);
  }
}
