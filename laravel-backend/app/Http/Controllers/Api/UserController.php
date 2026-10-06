<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            "datos" => User::query()->latest("id")->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            "nombre" => ["required", "string", "max:100"],
            "correo" => ["required", "email", "max:150", "unique:usuarios,correo"],
            "password" => ["required", "string", "min:8"],
            "rol" => [
                "sometimes",
                Rule::in(["Administrador", "Empleado", "Cliente", "Supervisor"]),
            ],
        ]);

        $user = User::query()->create([
            "nombre" => $datos["nombre"],
            "correo" => $datos["correo"],
            "password" => Hash::make($datos["password"]),
            "rol" => $datos["rol"] ?? "Cliente",
        ]);

        return response()->json([
            "mensaje" => "Usuario creado.",
            "usuario" => $user,
        ], 201);
    }
}
