<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_a_bearer_token(): void
    {
        User::query()->create([
            "nombre" => "Admin",
            "correo" => "admin@example.test",
            "password" => Hash::make("correct-horse"),
            "rol" => "Administrador",
        ]);

        $this->postJson("/api/auth/login", [
            "correo" => "admin@example.test",
            "password" => "correct-horse",
        ])
            ->assertOk()
            ->assertJsonPath("usuario.rol", "Administrador")
            ->assertJsonPath("token_type", "Bearer")
            ->assertJsonStructure(["token", "usuario" => ["id", "nombre", "correo"]]);
    }

    public function test_unauthenticated_client_cannot_list_products(): void
    {
        $this->getJson("/api/productos")->assertUnauthorized();
    }

    public function test_only_administrators_can_create_products(): void
    {
        $client = User::query()->create([
            "nombre" => "Cliente",
            "correo" => "cliente@example.test",
            "password" => Hash::make("correct-horse"),
            "rol" => "Cliente",
        ]);

        $this->actingAs($client, "sanctum")
            ->postJson("/api/productos", [
                "nombre" => "Proteína Whey",
                "descripcion" => "Proteína de rápida absorción",
                "precio" => 120000,
                "stock" => 5,
            ])
            ->assertForbidden();
    }

    public function test_sale_checks_and_decrements_stock_atomically(): void
    {
        $user = User::query()->create([
            "nombre" => "Cliente",
            "correo" => "cliente@example.test",
            "password" => Hash::make("correct-horse"),
            "rol" => "Cliente",
        ]);

        $product = Product::query()->create([
            "nombre" => "Proteína Whey",
            "descripcion" => "Proteína de rápida absorción",
            "precio" => 120000,
            "stock" => 5,
        ]);

        $this->actingAs($user, "sanctum")
            ->postJson("/api/ventas", [
                "productos" => [
                    ["producto_id" => $product->id, "cantidad" => 2],
                    ["producto_id" => $product->id, "cantidad" => 1],
                ],
            ])
            ->assertCreated()
            ->assertJsonPath("venta.total", "360000.00")
            ->assertJsonPath("venta.details.0.cantidad", 3);

        $this->assertDatabaseHas("productos", [
            "id" => $product->id,
            "stock" => 2,
        ]);
    }
}
