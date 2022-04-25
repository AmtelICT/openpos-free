<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use App\Models\User;
use App\Models\Configuration;

class ConfigurationsController extends Controller
{

  public function checkinstall()
  {
    $status = Configuration::where('status', '=', "closed")->first();
    if (!empty($status)) {
      return response(true);
    } 
    return response(null, 204);
  }

  public function install(Request $request) 
  {
    $this->validate($request, [
      'name'        => 'required',
      'rut'         => 'required',
      'country_id'  => 'required',
      'state_id'    => 'required',
      'city_id'     => 'required',
      'address'     => 'required',
      'phone'       => 'required',
      'theme'       => 'required'
    ]);

    Configuration::create([
      'name'        => $request->name,
      'rut'         => $request->rut,
      'country_id'  => $request->country_id,
      'state_id'    => $request->state_id,
      'city_id'     => $request->city_id,
      'address'     => $request->address,
      'phone'       => $request->phone,
      'theme'       => $request->theme
    ]);

    return response()->json('', 204 );
  }

  public function index() 
  {
    $configuration = Configuration::all()->first();
    return response()->json($configuration, 200);
  }

  public function update(Request $request)
  {
    $configuration = Configuration::all()->first();
    $configuration->update($request->all());
    
    return response()->json('', 204);
  }
}
