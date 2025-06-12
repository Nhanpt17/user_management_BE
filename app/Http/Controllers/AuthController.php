<?php

namespace App\Http\Controllers;

use App\Models\User;
use Error;
use Exception;
use GuzzleHttp\Psr7\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //
    //register user
    public function register(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users',
                'password' => 'required|string|min:6',
                'address' => 'nullable|string|max:255',
                'phonenumber' => 'nullable|string|max:20',
                'sex' => 'nullable|in:male,female,other',
                'birthday' => 'nullable|date',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phonenumber' => $request->phonenumber,
                'sex' => $request->sex,
                'birthday' => $request->filled('birthday') ? $request->birthday : null,
            ]);

            return response()->json(['message' => 'User created successfully'], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',

            ], 422);
        }catch(Exception $e){
            return response()->json([
                'message' => 'Có lỗi xảy ra',
            ], 400);
        }
    }

    //login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();


        return response()->json(['token' => $token, 'user'=>[

            'name'=>$user->name,
            'role'=>$user->role,
            'avatar'=>$user->avatar ? url('/storage/' . $user->avatar) : null
        ]]);
    }
}
