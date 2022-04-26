<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class CountriesController extends Controller
{
  public function index()
  {
    // Temporay hide countries with missings flags in www.phoca.cz/cssflags
    $country = Country::where('name', '!=', "Antarctica")
      ->where('name', '!=', "East Timor")
      ->where('name', '!=', "Bonaire, Sint Eustatius and Saba")
      ->where('name', '!=', "Saint-Barthelemy")
      ->where('name', '!=', "Kosovo")
      ->where('name', '!=', "Curaçao")
      ->where('name', '!=', "Sint Maarten (Dutch part)")
      ->where('name', '!=', "South Sudan")
      ->get();

    return response()->json($country);
  }
}
