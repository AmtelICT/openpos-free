<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomersController extends Controller
{
  public function index()
  {
    return Customer::join('states', 'customers.state_id', '=', 'states.id')
    ->join('cities', 'customers.city_id', '=', 'cities.id')
    ->select('customers.*', 'states.name as statename', 'cities.name as cityname')
    ->get();
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'name'        => 'required',
      'dni'         => 'required',
      'phone'       => 'required',
      'country_id'  => 'required',
      'state_id'    => 'required',
      'city_id'     => 'required',
      'address'     => 'required'
     ]);

     Customer::create($request->all());
     return response()->json('', 201);
  }

  public function update(Request $request)
  {
    $customer = Customer::where('id', '=', $request->id)->first();

    $customer->update($request->all());

    return response()->json('', 204);
  }

  public function delete($id)
  {
    $customer = Customer::where('id', '=', $id)->first();

    $customer->delete();
    
    return response()->json('', 204);
  }

  public function query($id)
  {
    return Customer::where('id', '=', $id)->first();
  }
}