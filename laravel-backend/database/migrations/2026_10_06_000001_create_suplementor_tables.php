<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("usuarios", function (Blueprint $table): void {
            $table->increments("id");
            $table->string("nombre", 100);
            $table->string("correo", 150)->unique();
            $table->string("password");
            $table->string("rol", 50)->default("Cliente");
            $table->timestamp("creado_en")->useCurrent();
        });

        Schema::create("productos", function (Blueprint $table): void {
            $table->increments("id");
            $table->string("nombre", 120);
            $table->string("descripcion", 255);
            $table->decimal("precio", 12, 2);
            $table->unsignedInteger("stock")->default(0);
            $table->timestamp("creado_en")->useCurrent();
        });

        Schema::create("ventas", function (Blueprint $table): void {
            $table->increments("id");
            $table->unsignedInteger("usuario_id");
            $table->decimal("total", 12, 2);
            $table->timestamp("creado_en")->useCurrent();

            $table->foreign("usuario_id")
                ->references("id")
                ->on("usuarios")
                ->restrictOnDelete();
        });

        Schema::create("detalle_ventas", function (Blueprint $table): void {
            $table->increments("id");
            $table->unsignedInteger("venta_id");
            $table->unsignedInteger("producto_id");
            $table->unsignedInteger("cantidad");
            $table->decimal("precio_unitario", 12, 2);

            $table->foreign("venta_id")
                ->references("id")
                ->on("ventas")
                ->cascadeOnDelete();
            $table->foreign("producto_id")
                ->references("id")
                ->on("productos")
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("detalle_ventas");
        Schema::dropIfExists("ventas");
        Schema::dropIfExists("productos");
        Schema::dropIfExists("usuarios");
    }
};
