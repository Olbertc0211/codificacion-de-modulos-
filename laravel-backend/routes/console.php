<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command("suplementor:status", function (): void {
    $this->info("API Suplementor disponible.");
})->purpose("Comprueba que el comando de consola de Suplementor está configurado.");
