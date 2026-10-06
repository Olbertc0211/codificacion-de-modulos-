<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function update(Request $request, Product $product): JsonResponse
    {
        $datos = $request->validate([
            "stock" => ["required", "integer", "min:0"],
        ]);

        $product->update(["stock" => $datos["stock"]]);

        return response()->json([
            "mensaje" => "Inventario actualizado.",
            "producto" => $product->refresh(),
        ]);
    }
}
