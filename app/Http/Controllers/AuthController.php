<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        return response()->json(compact('token'));
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Sesión cerrada']);
    }

    public function refresh()
    {
        $token = JWTAuth::refresh(JWTAuth::getToken());
        return response()->json(compact('token'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'           => 'sometimes|string|max:255',
            'age'            => 'sometimes|integer|min:1|max:120',
            'gender'         => 'sometimes|string|in:masculino,femenino,otro',
            'weight'         => 'sometimes|numeric|min:1|max:500',
            'height'         => 'sometimes|numeric|min:0.3|max:3',
            'activity_level' => 'sometimes|string|in:sedentario,leve,moderado,activo,muy_activo',
            'hta_level'      => 'sometimes|string|in:leve,moderada,severa',
        ]);

        $user = auth()->user();
        $user->update($request->only([
            'name', 'age', 'gender', 'weight', 'height', 'activity_level', 'hta_level'
        ]));

        return response()->json($user);
    }
}
