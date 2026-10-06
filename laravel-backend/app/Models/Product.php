<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = "productos";

    public const CREATED_AT = "creado_en";
    public const UPDATED_AT = null;

    protected $fillable = [
        "nombre",
        "descripcion",
        "precio",
        "stock",
    ];

    protected function casts(): array
    {
        return [
            "precio" => "decimal:2",
            "stock" => "integer",
        ];
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(SaleDetail::class, "producto_id");
    }
}
