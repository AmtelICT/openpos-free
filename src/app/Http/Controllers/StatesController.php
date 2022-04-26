<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;

class StatesController extends Controller
{
  public function index($id)
  {
    $states = State::where('country_id', '=', $id)->get();

    return $states;
  }

  public function state($id)
  {
    $state = State::where('id', '=', $id)->first();

    return $state;
  }
}
