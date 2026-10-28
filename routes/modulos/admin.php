<?php

use App\Http\Controllers\Admin\CategoriaSistemaController;
use App\Http\Controllers\Admin\EstadisticaController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\Admin\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuditoriaController;

/*
 * routes/modulos/admin.php — DUEÑO: Juan (J7).
 * Prefijo /admin y nombres admin.* ya puestos en web.php.
 * Middleware: auth + cuenta.activa + rol:administrador.
 *
 * 2026-10-21: se agrega /admin/panel (admin.panel) como dashboard principal
 * del administrador. Decisión #60. El Grupo E pasa de 9 a 10 rutas.
 *
 * 2026-10-22: se agrega DELETE /admin/categorias/{categoria}
 * (admin.categorias.destroy) con re-auth (current_password) y throttle.
 * Decisión #68. El Grupo E pasa de 10 a 11 rutas.
 *
 * Total proyecto: 68 rutas (11 + 9 + 37 + 0 + 11) + Route::fallback().
 */

// Panel (dashboard del admin). Pantalla principal del rol administrador.
Route::get('/panel', [PanelController::class, 'index'])->name('panel');

Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios');
Route::put('/usuarios/{usuario}/rol', [UsuarioController::class, 'cambiarRol'])
    ->middleware('throttle:30,1')
    ->name('usuarios.rol');
Route::put('/usuarios/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.estado');
Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

Route::get('/categorias', [CategoriaSistemaController::class, 'index'])->name('categorias');
Route::post('/categorias', [CategoriaSistemaController::class, 'store'])->name('categorias.store');
Route::put('/categorias/{categoria}', [CategoriaSistemaController::class, 'update'])->name('categorias.update');
Route::patch('/categorias/{categoria}/desactivar', [CategoriaSistemaController::class, 'desactivar'])->name('categorias.desactivar');
Route::delete('/categorias/{categoria}', [CategoriaSistemaController::class, 'destroy'])
    ->middleware('throttle:30,1')
    ->name('categorias.destroy');

Route::get('/estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas');

Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria');