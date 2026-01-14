<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version(), 'env' => config('app.env')];
});

// Incluir rutas de autenticación que necesitan sesiones
require __DIR__ . '/auth.php';

