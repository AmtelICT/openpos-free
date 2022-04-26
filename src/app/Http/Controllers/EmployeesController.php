<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeesController extends Controller
{
  public function index()
  {
    return Employee::join('users', 'employees.user_id', '=', 'users.id')->get();
  }

  public function store(Request $request)
  {
    $this->validate($request, [
      'username'  => 'required | unique:users',
      'password'  => 'required',
      'terminal'  => 'required | integer'
    ]);

    $user = User::create([
      'username'  => $request->username,
      'password'  => bcrypt($request->password)
    ]);

    Employee::create([
      'user_id'   => $user->id,
      'terminal'  => $request->terminal,
      'phone'     => $request->phone
    ]);

    return response()->json(['A new employee was successfull added!'], 201);
  }

  public function update(Request $request)
  {
    $employee = Employee::where('employees.user_id', '=', $request->id)
      ->first();

    $user = $employee->user->makeVisible(['password']);
    $employee->update($request->all());

    if($request->username){$user->update(['username' => $request->username]);}
    if($request->password){$user->update(['password' => bcrypt($request->password)]);}

    return response()->json(['A registry was updated !'], 201);
  }

  public function delete($id)
  {
    $employee = Employee::where('employees.user_id', '=', $id)
      ->first();
    $user = $employee->user;

    $user->delete();
    $employee->delete();
    
    return response()->json(['A registry was deleted!'], 201);
  }

  public function query($id)
  {
    return Employee::join(
      'users', 'employees.user_id', '=', 'users.id')
      ->where('employees.user_id', '=', $id)
      ->first();
  }
}
