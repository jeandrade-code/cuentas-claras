<?php

namespace App\Console\Commands;

use App\Models\Notificacion;
use App\Models\User;
use App\Services\ServicioNotificaciones;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificarInformesMensualesDisponibles extends Command
{
    protected $signature = 'informes:notificar-disponibles {--fecha= : Fecha YYYY-MM-DD para simular la ejecución}';

    protected $description = 'Notifica a los usuarios cuando el informe del mes anterior está disponible';

    public function handle(ServicioNotificaciones $notificaciones): int
    {
        $fecha = $this->option('fecha')
            ? Carbon::parse($this->option('fecha'))->startOfDay()
            : Carbon::today();

        $mes = $fecha->copy()->subMonthNoOverflow()->startOfMonth();
        $mesClave = $mes->format('Y-m');
        $mesNombre = $mes->locale('es')->translatedFormat('F \d\e Y');

        $clave = 'informe_disponible:' . $mesClave;
        $cantidad = 0;

        User::query()
            ->where('activo', true)
            ->whereIn('rol', ['estandar', 'familiar'])
            ->orderBy('id')
            ->chunkById(100, function ($usuarios) use (
                $notificaciones,
                $mesNombre,
                $clave,
                &$cantidad
            ) {
                foreach ($usuarios as $usuario) {
                    $existia = Notificacion::query()
                        ->where('user_id', $usuario->id)
                        ->where('clave_unica', $clave)
                        ->exists();

                    $notificaciones->crear(
                        $usuario,
                        ServicioNotificaciones::TIPO_INFORME_DISPONIBLE,
                        "Tu informe de {$mesNombre} ya está disponible. Ingresa a Cuentas Claras para consultarlo y guardarlo.",
                        ['clave_unica' => $clave]
                    );

                    if (!$existia) {
                        $cantidad++;
                    }
                }
            });

        $this->info("Notificaciones de informes creadas: {$cantidad}");

        return self::SUCCESS;
    }
}
