<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Exception;

class UserController extends Controller
{
    public function register(Request $request) {
        try{
         $request->validate([
             'name' => 'required',
             'phone' => 'required',
             'email' => 'required|email|unique:users',
             'password' => 'required|min:6',
             'role' => 'required'
          ]);
          $user = User::create([
              'name' => $request->name,
              'phone' => $request->phone,
              'role' => $request->role,
              'email' => $request->email,
              'password' => Hash::make($request->password),
          ]);
          $token = $user->createToken('auth_token')->plainTextToken;
          return response()->json([
              'message' => 'User created successfully',
              'token' => $token,
              'user' => $user
          ], 201);
        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
     }
     //Login Sections
     public function login(Request $request) {
         try{
             $request->validate([
                 'email' => 'required|email',
                 'password' => 'required|min:6'
              ]);
             if(!Auth::attempt($request->only('email', 'password'))) {
                 return response()->json([
                     'message' => 'Invalid Credentials'
                 ], 401);
             }
            // $user = Auth::user();
             $user = User::where('email', $request->email)->first();
             $token = $user->createToken('auth_token')->plainTextToken;
             return response()->json([
                 'message' => 'Login successful',
                 'token' => $token,
                 'user' => $user
             ], 200);
         }catch(Exception $e) {
             return response()->json([
                 'message' => $e->getMessage()
             ], 500);
         }
     }
       // ✅ User Logout
    public function logout(Request $request)
    {
        try{
            if (!Auth::check()) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 401);
            }else{
                $request->user()->tokens()->delete();
                return response()->json([
                    'message' => 'User logged out successfully'
                ], 200);
            }

        }catch(Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
