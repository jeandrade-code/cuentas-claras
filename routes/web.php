<?php

use App\Http\Middleware\UsuarioMaqueta;
use Illuminate\Support\Facades\Route;

/*
 * ==============================================================================
 * routes/web.php  —  ÍNDICE DE RUTAS (solo Juan lo modifica)
 * Cada módulo vive en routes/modulos/<modulo>.php y lo escribe su dueño.
 * Los NOMBRES de ruta salen de docs/backend/00-rutas.php y NO se cambian.
 *
 * FASE 1 (ahora): sin login real. Cada ruta devuelve su vista (maqueta) y el
 *   middleware App\Http\Middleware\UsuarioMaqueta simula un usuario para que menú y barra superior
 *   no fallen.
 * FASE 2: J3 agrega 'auth'; J4 registra 'cuenta.activa' y 'rol' y se quitan
 *   los marcadores  // FASE 2  de abajo. Al llegar J3 se borra el middleware UsuarioMaqueta.
 * ==============================================================================
 */

// GRUPO A - PÚBLICAS
require __DIR__.'/modulos/publico.php';   // Nikol (N5)
require __DIR__.'/modulos/auth.php';      // Juan  (J3)

// GRUPOS B a E - PRIVADAS
Route::middleware([UsuarioMaqueta::class /* FASE 2: 'auth', 'cuenta.activa' */])->group(function () {

    // GRUPO B - todos los roles con sesión
    require __DIR__.'/modulos/ajustes.php';          // Valeria (V4)

    // GRUPO C - estándar y familiar            // FASE 2: ->middleware('rol:estandar,familiar')
    Route::group([], function () {
        require __DIR__.'/modulos/panel.php';            // Juan    (J8)
        require __DIR__.'/modulos/gastos.php';           // Nikol   (N1, N2)
        require __DIR__.'/modulos/gastos-fijos.php';     // Nikol   (N3)
        require __DIR__.'/modulos/notificaciones.php';   // Nikol   (N4)
        require __DIR__.'/modulos/creditos.php';         // Valeria (V1, V2)
        require __DIR__.'/modulos/informes.php';         // Valeria (V3)
    });

    // GRUPO D - solo familiar                   // FASE 2: ->middleware('rol:familiar')
    Route::group([], function () {
        require __DIR__.'/modulos/metas.php';            // Juan (J5)
    });

    // GRUPO E - solo administrador              // FASE 2: ->middleware('rol:administrador')
    Route::prefix('admin')->name('admin.')->group(function () {
        require __DIR__.'/modulos/admin.php';            // Juan (J7)
    });
});

