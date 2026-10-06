<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(): JsonResponse
    {
        $sales = Sale::query()
            ->with(["user:id,nombre,correo", "details.product:id,nombre"])
            ->latest("id")
            ->get();

        return response()->json(["datos" => $sales]);
    }

    public function store(Request $request): JsonResponse
    {
        $datos = $request->validate([
            "productos" => ["required", "array", "min:1"],
            "productos.*.producto_id" => ["required", "integer", "min:1"],
            "productos.*.cantidad" => ["required", "integer", "min:1"],
        ]);

        $sale = DB::transaction(function () use ($datos, $request): Sale {
            $cantidades = [];
            foreach ($datos["productos"] as $item) {
                $productId = (int) $item["producto_id"];
                $cantidades[$productId] = ($cantidades[$productId] ?? 0)
                    + (int) $item["cantidad"];
            }

            $productos = Product::query()
                ->whereIn("id", array_keys($cantidades))
                ->orderBy("id")
                ->lockForUpdate()
                ->get()
                ->keyBy("id");

            $totalEnCentavos = 0;
            foreach ($cantidades as $productId => $cantidad) {
                $product = $productos->get($productId);

                if (!$product || $product->stock < $cantidad) {
                    throw ValidationException::withMessages([
                        "productos" => [
                            "Producto {$productId} inexistente o con stock insuficiente.",
                        ],
                    ]);
                }

                $precioEnCentavos = (int) round((float) $product->precio * 100);
                $totalEnCentavos += $precioEnCentavos * $cantidad;
            }

            $sale = Sale::query()->create([
                "usuario_id" => $request->user()->id,
                "total" => number_format($totalEnCentavos / 100, 2, ".", ""),
            ]);

            foreach ($cantidades as $productId => $cantidad) {
                $product = $productos->get($productId);
                $sale->details()->create([
                    "producto_id" => $productId,
                    "cantidad" => $cantidad,
                    "precio_unitario" => $product->precio,
                ]);
                $product->decrement("stock", $cantidad);
            }

            return $sale;
        }, attempts: 3);

        return response()->json([
            "mensaje" => "Venta registrada.",
            "venta" => $sale->load(["details.product"]),
        ], 201);
    }
}
