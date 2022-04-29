<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;
use App\Models\User;
use App\Models\Configuration;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $this->validate($request, [
      'username'              => 'required|unique:users',
      'email'                 => 'required|unique:users|email',
      'password'              => 'required|confirmed',
      'password_confirmation' => 'required'
    ]);

    $user = User::create([
      'username'  => $request->username,
      'email'     => $request->email,
      'password'  => bcrypt($request->password),
      'role'      => 'admin'
    ]);

    $token = $user->createToken($request->username)->plainTextToken;

    $response = [
      'user'  => $user,
      'token' => $token
    ];

    $configuration = Configuration::all()->first();
    $configuration->update(['status' => 'closed']);

    return response($response, 201);
  }

  public function login(Request $request)
  {
    $this->validate($request, [
      'username' => 'required',
      'password' => 'required',
    ]);

    $user = User::where('username', $request->username)->first();
    
    if (! $user || ! Hash::check($request->password, $user->password)) {
      return response()->json([
        'errors' => [
          'password' => 'Invalid credentials'
        ]
      ], 403);
    }

    $token = $user->createToken($request->username)->plainTextToken;

    return response($token, 200);
  }

  public function logout(Request $request)
  {
    if ($token = $request->bearerToken()) {
      $model = Sanctum::$personalAccessTokenModel;
      $accessToken = $model::findToken($token);
      if($accessToken !== null) {
        $accessToken->delete();
        return response()->json('', 204 );
      } else {
        return response()->json('', 204 );
      }
    } 
    return response()->json('', 204 );
  }
}
