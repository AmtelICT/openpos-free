<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;

class CitiesController extends Controller
{
  public function index($id)
  {
    $cities = City::where('state_id', '=', $id)->get();

    return $cities;
  }
}
