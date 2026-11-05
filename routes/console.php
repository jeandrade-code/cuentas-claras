<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// J6: alerta diaria de gastos fijos (por vencer y vencidos).
// El cron debe ejecutar `php artisan schedule:run` cada minuto.
Schedule::command('alertas:gastos-fijos')->dailyAt('07:00');
// Notificación mensual cuando el informe del mes anterior está disponible.
Schedule::command('informes:notificar-disponibles')
    ->monthlyOn(1, '07:05');
// Se ejecuta el día 1 de cada mes a las 00:05 AM para guardar informes
Schedule::command('informes:guardar-mensuales')->monthlyOn(1, '00:05');
