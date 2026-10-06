<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $correo = env("SUPLEMENTOR_ADMIN_EMAIL");
        $password = env("SUPLEMENTOR_ADMIN_PASSWORD");

        if (!$correo || !$password) {
            $this->command?->warn(
                "No se creó el administrador: configura SUPLEMENTOR_ADMIN_EMAIL y SUPLEMENTOR_ADMIN_PASSWORD en .env."
            );

            return;
        }

        User::query()->updateOrCreate(
            ["correo" => $correo],
            [
                "nombre" => env("SUPLEMENTOR_ADMIN_NAME", "Administrador"),
                "password" => Hash::make($password),
                "rol" => "Administrador",
            ]
        );
    }
}
