<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;

    protected $table = "usuarios";

    public const CREATED_AT = "creado_en";
    public const UPDATED_AT = null;

    protected $fillable = [
        "nombre",
        "correo",
        "password",
        "rol",
    ];

    protected $hidden = [
        "password",
        "remember_token",
    ];

    protected function casts(): array
    {
        return [
            "password" => "hashed",
        ];
    }
}
