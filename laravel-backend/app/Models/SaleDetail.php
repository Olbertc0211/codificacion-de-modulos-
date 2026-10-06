<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleDetail extends Model
{
    protected $table = "detalle_ventas";

    public $timestamps = false;

    protected $fillable = [
        "venta_id",
        "producto_id",
        "cantidad",
        "precio_unitario",
    ];

    protected function casts(): array
    {
        return [
            "cantidad" => "integer",
            "precio_unitario" => "decimal:2",
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class, "venta_id");
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "producto_id");
    }
}
