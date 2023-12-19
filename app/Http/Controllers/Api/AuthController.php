<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User does not exist'
            ], 404);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $token = $user->createToken('todo-login')->plainTextToken;

            return response()->json([
                'message' => 'Success',
                'data' => [
                    'name' => $user->name,
                    'access_token' => $token,
                ]
            ], 200);
        } else {
            return response()->json([
                'error' => 'Invalid email or password'
            ], 422);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);

        $token = $user->createToken('todo-login')->plainTextToken;

        return response()->json([
            'message' => 'Success',
            'data' => [
                'name' => $user->name,
                'access_token' => $token,
            ],
        ], 200);
    }
}
