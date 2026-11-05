<?php

use App\Http\Controllers\InformeController;
use Illuminate\Support\Facades\Route;

Route::get('/informes', [InformeController::class, 'mes'])
    ->name('informes.actual');

Route::post('/informes/guardar', [InformeController::class, 'guardar'])
    ->name('informes.guardar');

Route::get('/informes/historial', [InformeController::class, 'historial'])
    ->name('informes.historial');

Route::get('/informes/historial/{id}', [InformeController::class, 'ver'])
    ->whereNumber('id')
    ->name('informes.ver');
// Exportaciones del informe mensual actual.
Route::get('/informes/exportar/csv', [InformeController::class, 'exportarCsv'])
    ->name('informes.exportar.csv');

Route::get('/informes/exportar/word', [InformeController::class, 'exportarWord'])
    ->name('informes.exportar.word');

// Exportaciones de un informe histórico guardado.
Route::get('/informes/historial/{id}/exportar/csv', [InformeController::class, 'exportarHistoricoCsv'])
    ->whereNumber('id')
    ->name('informes.historico.csv');

Route::get('/informes/historial/{id}/exportar/word', [InformeController::class, 'exportarHistoricoWord'])
    ->whereNumber('id')
    ->name('informes.historico.word');
