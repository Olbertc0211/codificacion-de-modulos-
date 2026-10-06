<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            "correo" => ["required", "email", "max:150"],
            "password" => ["required", "string"],
        ]);

        $user = User::query()
            ->where("correo", $credentials["correo"])
            ->first();

        if (!$user || !Hash::check($credentials["password"], $user->password)) {
            return response()->json([
                "message" => "Las credenciales no son correctas.",
            ], 401);
        }

        $token = $user->createToken("suplementor-api")->plainTextToken;

        return response()->json([
            "mensaje" => "Inicio de sesión exitoso.",
            "token" => $token,
            "token_type" => "Bearer",
            "usuario" => $user,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(["mensaje" => "Sesión cerrada."]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(["usuario" => $request->user()]);
    }
}
