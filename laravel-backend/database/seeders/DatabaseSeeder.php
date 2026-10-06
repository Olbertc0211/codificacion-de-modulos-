<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $productos = [
            [
                "nombre" => "Proteína Whey 2 lb",
                "descripcion" => "Proteína de suero en presentación de 2 lb, sabor vainilla.",
                "precio" => 159900,
                "stock" => 15,
            ],
            [
                "nombre" => "Creatina Monohidratada 300 g",
                "descripcion" => "Creatina monohidratada en polvo, presentación de 300 g.",
                "precio" => 89900,
                "stock" => 20,
            ],
            [
                "nombre" => "Pre-entreno 300 g",
                "descripcion" => "Mezcla en polvo para antes del entrenamiento, presentación de 300 g.",
                "precio" => 74900,
                "stock" => 12,
            ],
            [
                "nombre" => "Omega 3 60 cápsulas",
                "descripcion" => "Suplemento de aceite de pescado, frasco con 60 cápsulas.",
                "precio" => 39900,
                "stock" => 18,
            ],
            [
                "nombre" => "Multivitamínico 60 tabletas",
                "descripcion" => "Complejo multivitamínico en frasco con 60 tabletas.",
                "precio" => 34900,
                "stock" => 25,
            ],
        ];

        foreach ($productos as $producto) {
            Product::query()->firstOrCreate(
                ["nombre" => $producto["nombre"]],
                $producto
            );
        }

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
