<?php

namespace App\Console\Commands;

use App\Http\Controllers\InformeController;
use App\Models\Informe;
use App\Models\User;
use App\Services\GeneradorInforme;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GuardarInformesMensuales extends Command
{
    /**
     * Nombre y firma del comando.
     * El argumento --fecha es opcional y sirve para simular la ejecución.
     */
    protected $signature = 'informes:guardar-mensuales {--fecha= : Fecha de referencia (YYYY-MM-DD)}';

    /**
     * Descripción del comando.
     */
    protected $description = 'Calcula y guarda automáticamente el informe 50/30/20 del mes anterior para todos los usuarios activos.';

    /**
     * Ejecuta el comando.
     */
    public function handle(InformeController $controller, GeneradorInforme $generador)
    {
        // 1. Determinar la fecha de referencia y el mes a procesar (el mes anterior)
        $fechaReferencia = $this->option('fecha') 
            ? Carbon::parse($this->option('fecha')) 
            : now();

        $mesObjetivo = $fechaReferencia->copy()->subMonth();
        $anio = (int) $mesObjetivo->year;
        $mes = (int) $mesObjetivo->month;

        $this->info("Iniciando generación de informes para: {$mesObjetivo->translatedFormat('F Y')}");

        $creados = 0;
        $omitidos = 0;

        // 2. Buscar usuarios activos (solo estandar y familiar)
        User::where('activo', true)
            ->whereIn('rol', ['estandar', 'familiar'])
            ->chunkById(100, function ($usuarios) use ($controller, $generador, $mesObjetivo, $anio, $mes, &$creados, &$omitidos) {
                
                foreach ($usuarios as $usuario) {
                    
                    // Regla: solo usuarios con ingreso registrado
                    if ((int) ($usuario->ingreso_mensual ?? 0) <= 0) {
                        $omitidos++;
                        continue;
                    }

                    // Idempotencia: verificar si el informe ya existe
                    $existe = Informe::where('user_id', $usuario->id)
                        ->where('anio', $anio)
                        ->where('mes', $mes)
                        ->exists();

                    if ($existe) {
                        $omitidos++;
                        continue;
                    }

                    try {
                        // 3. Calcular los datos usando el método del controlador
                        $resultado = $controller->calcularMes($usuario, $mesObjetivo->copy(), $generador);

                        // Si no hay gastos, no guardamos un informe vacío
                        if (empty($resultado['calculos']['porcentajes_disponibles']) || ($resultado['calculos']['total_gastado'] ?? 0) <= 0) {
                            $omitidos++;
                            continue;
                        }

                        // 4. Crear la cabecera y los detalles dentro de una transacción
                        DB::transaction(function () use ($usuario, $anio, $mes, $resultado) {
                            $informe = Informe::create([
                                'user_id'       => $usuario->id,
                                'anio'          => $anio,
                                'mes'           => $mes,
                                'ingreso'       => $usuario->ingreso_mensual,
                                'total_gastado' => $resultado['calculos']['total_gastado'],
                            ]);

                            foreach ($resultado['detalle'] as $item) {
                                $informe->detalles()->create([
                                    'categoria_id'           => $item['categoria_id'],
                                    'categoria_nombre'       => $item['categoria_nombre'],
                                    'bloque'                 => $item['bloque'],
                                    'monto_gastado'          => $item['monto_gastado'],
                                    'porcentaje_real'        => $item['porcentaje_real'] ?? 0,
                                    'porcentaje_recomendado' => $item['porcentaje_recomendado'],
                                    'excede'                 => $item['excede'] ?? false,
                                ]);
                            }
                        });

                        $creados++;

                    } catch (\Throwable $e) {
                        $this->error("Error al procesar el usuario {$usuario->email}: " . $e->getMessage());
                    }
                }
            });

        // 5. Mostrar resultados
        $this->newLine();
        $this->info("Proceso finalizado.");
        $this->line("Informes creados: {$creados}");
        $this->line("Informes omitidos: {$omitidos}");
        
        return Command::SUCCESS;
    }
}