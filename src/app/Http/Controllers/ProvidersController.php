<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provider;

class ProvidersController extends Controller
{
  public function index()
  {
    return Provider::all();
  }

  public function store(Request $request)
  {
     $this->validate($request, [
      'company'   => 'required',
      'phone'     => 'required',
      'state_id'  => 'required',
      'city_id'   => 'required',
      'address'   => 'required'
     ]);

     Provider::create($request->all());
     return response()->json(['A new provider was added !'], 201);
  }

  public function update(Request $request)
  {
    $provider = Provider::where('id', '=', $request->id)
      ->first();

    $provider->update($request->all());

    return response()->json(['A registry was updated !'], 201);
  }

  public function delete($id)
  {
    $provider = Provider::where('id', '=', $id)
      ->first();

    $provider->delete();
    
    return response()->json(['A registry was deleted !'], 201);
  }

  public function query($id)
  {
    return Provider::where('id', '=', $id)
      ->first();
  }
}