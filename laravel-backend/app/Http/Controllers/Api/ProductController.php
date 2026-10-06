<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            "datos" => Product::query()->latest("id")->get(),
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(["producto" => $product]);
    }

    public function store(Request $request): JsonResponse
    {
        $product = Product::query()->create($this->validarProducto($request));

        return response()->json([
            "mensaje" => "Producto creado.",
            "producto" => $product,
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $product->update($this->validarProducto($request));

        return response()->json([
            "mensaje" => "Producto actualizado.",
            "producto" => $product->refresh(),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        if ($product->saleDetails()->exists()) {
            return response()->json([
                "message" => "No se puede eliminar un producto incluido en ventas.",
            ], Response::HTTP_CONFLICT);
        }

        $product->delete();

        return response()->json(["mensaje" => "Producto eliminado."]);
    }

    private function validarProducto(Request $request): array
    {
        return $request->validate([
            "nombre" => ["required", "string", "max:120"],
            "descripcion" => ["required", "string", "max:255"],
            "precio" => ["required", "numeric", "min:0", "decimal:0,2"],
            "stock" => ["required", "integer", "min:0"],
        ]);
    }
}
