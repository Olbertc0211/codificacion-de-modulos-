<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    protected $table = "ventas";

    public const CREATED_AT = "creado_en";
    public const UPDATED_AT = null;

    protected $fillable = [
        "usuario_id",
        "total",
    ];

    protected function casts(): array
    {
        return [
            "total" => "decimal:2",
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "usuario_id");
    }

    public function details(): HasMany
    {
        return $this->hasMany(SaleDetail::class, "venta_id");
    }
}
