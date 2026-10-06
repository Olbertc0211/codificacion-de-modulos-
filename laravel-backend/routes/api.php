<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post("/auth/login", [AuthController::class, "login"])
    ->middleware("throttle:5,1");

Route::middleware("auth:sanctum")->group(function (): void {
    Route::post("/auth/logout", [AuthController::class, "logout"]);
    Route::get("/auth/me", [AuthController::class, "me"]);

    Route::get("/productos", [ProductController::class, "index"]);
    Route::get("/productos/{product}", [ProductController::class, "show"]);
    Route::post("/ventas", [SaleController::class, "store"]);

    Route::middleware("admin")->group(function (): void {
        Route::get("/usuarios", [UserController::class, "index"]);
        Route::post("/usuarios", [UserController::class, "store"]);
        Route::get("/ventas", [SaleController::class, "index"]);

        Route::post("/productos", [ProductController::class, "store"]);
        Route::put("/productos/{product}", [ProductController::class, "update"]);
        Route::delete("/productos/{product}", [ProductController::class, "destroy"]);
        Route::put("/inventario/{product}", [InventoryController::class, "update"]);
    });
});
