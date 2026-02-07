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

        $user = auth()->user();

        return response()->json([
            'token' => $token,
            'onboarding_completed' => $user->onboarding_completed,
        ]);
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

    /**
     * RF-01: Onboarding completo del usuario.
     * Recibe todos los datos del perfil clínico inicial.
     */
    public function onboarding(Request $request)
    {
        $request->validate([
            'age'               => 'required|integer|min:1|max:120',
            'gender'            => 'required|string|in:masculino,femenino,otro',
            'weight'            => 'required|numeric|min:1|max:500',
            'height'            => 'required|numeric|min:0.3|max:3',
            'activity_level'    => 'required|string|in:sedentario,leve,moderado,activo,muy_activo',
            'hta_level'         => 'required|string|in:leve,moderada,severa',
            'initial_systolic'  => 'required|integer|min:50|max:300',
            'initial_diastolic' => 'required|integer|min:30|max:200',
            'food_restrictions' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $user->update(array_merge(
            $request->only([
                'age', 'gender', 'weight', 'height',
                'activity_level', 'hta_level',
                'initial_systolic', 'initial_diastolic',
                'food_restrictions',
            ]),
            ['onboarding_completed' => true]
        ));

        // Crear registro inicial de PA
        $user->bloodPressureRecords()->create([
            'systolic'    => $request->initial_systolic,
            'diastolic'   => $request->initial_diastolic,
            'measured_at' => now(),
        ]);

        return response()->json([
            'message' => 'Onboarding completado',
            'user'    => $user->fresh(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'              => 'sometimes|string|max:255',
            'age'               => 'sometimes|integer|min:1|max:120',
            'gender'            => 'sometimes|string|in:masculino,femenino,otro',
            'weight'            => 'sometimes|numeric|min:1|max:500',
            'height'            => 'sometimes|numeric|min:0.3|max:3',
            'activity_level'    => 'sometimes|string|in:sedentario,leve,moderado,activo,muy_activo',
            'hta_level'         => 'sometimes|string|in:leve,moderada,severa',
            'food_restrictions' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $user->update($request->only([
            'name', 'age', 'gender', 'weight', 'height',
            'activity_level', 'hta_level', 'food_restrictions',
        ]));

        return response()->json($user);
    }
}
