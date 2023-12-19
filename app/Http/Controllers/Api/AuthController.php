<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'error' => 'User does not exist.'
            ], 404);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Invalid email or password.'
            ], 422);
        }

        $tokenResult = $user->createToken('todo-login');
        $plainTextToken = $tokenResult->accessToken;
        $token = $tokenResult->token;
        $token->expires_at = now()->addHours(8);
        $token->save();

        return response()->json([
            'message' => 'Success',
            'data' => [
                'name' => $user->name,
                'token' => $plainTextToken,
                'token_expires_at' => $token->expires_at->toDateTimeString(),
            ]
        ])->cookie('access_token', $token->id, 8*60);
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
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $tokenResult = $user->createToken('todo-login');

        $plainTextToken = $tokenResult->accessToken;
        $token = $tokenResult->token;
        $token->expires_at = now()->addHours(8);
        $token->save();

        return response()->json([
            'message' => 'Success',
            'data' => [
                'name' => $user->name,
                'token' => $plainTextToken,
                'token_expires_at' => $token->expires_at->toDateTimeString(),
            ],
        ])->cookie('access_token', $token->id, 8*60);
    }
}
