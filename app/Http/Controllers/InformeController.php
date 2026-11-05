<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\Informe;
use App\Models\PagoGastoFijo;
use App\Models\User;
use App\Services\GeneradorInforme;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InformeController extends Controller
{
    /**
     * Muestra el informe calculado para el mes seleccionado.
     */
    public function mes(Request $request, GeneradorInforme $generador)
{
    $datos = $request->validate([
        'mes' => ['nullable', 'date_format:Y-m'],
    ]);

    $mes = $datos['mes'] ?? now()->format('Y-m');
    $fecha = Carbon::createFromFormat('!Y-m', $mes);

    $usuario = $request->user();

    // Solo se pueden consultar meses que ya terminaron.
    $informeDisponible = $fecha->lt(now()->startOfMonth());

    if ($informeDisponible) {
        $resultado = $this->calcularMes($usuario, $fecha, $generador);

        $informe = $resultado['calculos']['bloques'];
        $detalle = $resultado['detalle'];
        $resumen = $resultado['calculos'];
        $gastosHormiga = $resultado['gastosHormiga'];

        $informeGuardado = $usuario->informes()
            ->where('anio', $fecha->year)
            ->where('mes', $fecha->month)
            ->exists();
    } else {
        $informe = [];
        $detalle = [];
        $resumen = [];
        $gastosHormiga = [
            'total' => 0,
            'cantidad' => 0,
            'promedio' => 0,
            'detalle' => collect(),
        ];
        $informeGuardado = false;
    }

    return view('app.informes.actual', [
        'informe' => $informe,
        'detalle' => $detalle,
        'resumen' => $resumen,
        'mesSeleccionado' => $mes,
        'informeGuardado' => $informeGuardado,
        'gastosHormiga' => $gastosHormiga,
        'informeDisponible' => $informeDisponible,
    ]);
}

    /**
     * Guarda una fotografía del informe mensual.
     */
    public function guardar(Request $request, GeneradorInforme $generador)
    {
        $datos = $request->validate([
            'mes' => ['required', 'date_format:Y-m'],
        ]);

        $usuario = $request->user();
        $fecha = Carbon::createFromFormat('!Y-m', $datos['mes']);
        if (!$fecha->lt(now()->startOfMonth())) {
    return redirect()
        ->route('informes.actual', ['mes' => $datos['mes']])
        ->with('error', 'El informe estará disponible cuando termine el mes seleccionado.');
}

        if ((int) ($usuario->ingreso_mensual ?? 0) <= 0) {
            return back()->with(
                'error',
                'Registra tu ingreso mensual antes de guardar el informe.'
            );
        }

        $yaExiste = $usuario->informes()
            ->where('anio', $fecha->year)
            ->where('mes', $fecha->month)
            ->exists();

        if ($yaExiste) {
            return redirect()
                ->route('informes.actual', ['mes' => $datos['mes']])
                ->with('error', 'Ya existe un informe guardado para este mes.');
        }

        $resultado = $this->calcularMes($usuario, $fecha, $generador);

        DB::transaction(function () use (
            $usuario,
            $fecha,
            $resultado
        ) {
            $informeGuardado = Informe::create([
                'user_id' => $usuario->id,
                'anio' => $fecha->year,
                'mes' => $fecha->month,
                'ingreso' => $usuario->ingreso_mensual,
                'total_gastado' => $resultado['calculos']['total_gastado'],
            ]);

            foreach ($resultado['detalle'] as $item) {
                $bloque = $item['bloque'];

                $porcentajeRecomendado = match ($bloque) {
                    'necesidades' => 50,
                    'discrecionales' => 30,
                    'ahorro' => 20,
                    default => 0,
                };

                $porcentajeReal = round(
                    ($item['monto_gastado'] / $usuario->ingreso_mensual) * 100,
                    2
                );

                $excede = isset($resultado['calculos']['bloques'][$bloque])
                    && $resultado['calculos']['bloques'][$bloque]['estado'] === 'excede';

                $informeGuardado->detalles()->create([
                    'categoria_id' => $item['categoria_id'],
                    'categoria_nombre' => $item['categoria_nombre'],
                    'bloque' => $bloque,
                    'monto_gastado' => $item['monto_gastado'],
                    'porcentaje_real' => $porcentajeReal,
                    'porcentaje_recomendado' => $porcentajeRecomendado,
                    'excede' => $excede,
                ]);
            }
        });

        return redirect()
            ->route('informes.actual', ['mes' => $datos['mes']])
            ->with('exito', 'El informe mensual se guardó correctamente.');
    }

    
    /**
     * Muestra el historial del usuario autenticado con filtros.
     */
    public function historial(Request $request)
    {
        $datos = $request->validate([
            'anio' => [
                'nullable',
                'integer',
                'min:2000',
                'max:' . (now()->year + 5),
            ],
            'mes' => ['nullable', 'integer', 'between:1,12'],
        ]);

        $consulta = $request->user()
            ->informes()
            ->with('detalles');

        if (!empty($datos['anio'])) {
            $consulta->where('anio', $datos['anio']);
        }

        if (!empty($datos['mes'])) {
            $consulta->where('mes', $datos['mes']);
        }

        $informes = $consulta
            ->orderByDesc('anio')
            ->orderByDesc('mes')
            ->paginate(12)
            ->withQueryString();

        return view('app.informes.historial', [
            'informes' => $informes,
            'anioSeleccionado' => $datos['anio'] ?? '',
            'mesSeleccionado' => $datos['mes'] ?? '',
        ]);
    }


    /**
     * Abre una fotografía histórica sin recalcularla con los gastos actuales.
     */
    public function ver(
        Request $request,
        int $id,
        GeneradorInforme $generador
    ) {
        $informeGuardado = $request->user()
            ->informes()
            ->with('detalles')
            ->findOrFail($id);

        $totalesPorBloque = [
            'necesidades' => 0,
            'discrecionales' => 0,
            'ahorro' => 0,
            'sin_clasificar' => 0,
        ];

        foreach ($informeGuardado->detalles as $item) {
            $bloque = in_array(
                $item->bloque,
                Categoria::BLOQUES,
                true
            ) ? $item->bloque : 'sin_clasificar';

            $totalesPorBloque[$bloque] += (int) $item->monto_gastado;
        }

        // Identificar gastos hormiga según su frecuencia e impacto mensual.

        $calculos = $generador->generar(
            (int) $informeGuardado->ingreso,
            $totalesPorBloque
        );

        $detalle = $informeGuardado->detalles->map(function ($item) {
            return [
                'categoria_id' => $item->categoria_id,
                'categoria_nombre' => $item->categoria_nombre,
                'bloque' => $item->bloque,
                'monto_gastado' => (int) $item->monto_gastado,
                'porcentaje_real' => (float) $item->porcentaje_real,
                'porcentaje_recomendado' => (int) $item->porcentaje_recomendado,
                'excede' => (bool) $item->excede,
            ];
        });

        return view('app.informes.actual', [
            'informe' => $calculos['bloques'],
            'detalle' => $detalle,
            'resumen' => $calculos,
            'mesSeleccionado' => sprintf(
                '%04d-%02d',
                $informeGuardado->anio,
                $informeGuardado->mes
            ),
            'informeGuardado' => true,
            'informeDisponible' => true,
            'esHistorico' => true,
            'fechaGuardado' => $informeGuardado->created_at,
        ]);
    }

    /**
     * Exporta el informe del mes seleccionado a CSV.
     */
    public function exportarCsv(
        Request $request,
        GeneradorInforme $generador
    ) {
        $datos = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ]);

        $mes = $datos['mes'] ?? now()->format('Y-m');
        $fecha = Carbon::createFromFormat('!Y-m', $mes);
        $resultado = $this->calcularMes(
            $request->user(),
            $fecha,
            $generador
        );

        return $this->descargarInformeCsv(
            $resultado['calculos']['bloques'],
            $resultado['detalle'],
            $resultado['calculos'],
            'informe-' . $mes . '.csv'
        );
    }

    /**
     * Exporta un informe histórico sin consultar los gastos actuales.
     */
    public function exportarHistoricoCsv(Request $request, int $id)
    {
        $guardado = $request->user()
            ->informes()
            ->with('detalles')
            ->findOrFail($id);

        $bloques = [
            'necesidades' => [
                'nombre' => 'Necesidades',
                'monto_real' => 0,
                'porcentaje_real' => null,
                'porcentaje_recomendado' => 50,
            ],
            'discrecionales' => [
                'nombre' => 'Deseos',
                'monto_real' => 0,
                'porcentaje_real' => null,
                'porcentaje_recomendado' => 30,
            ],
            'ahorro' => [
                'nombre' => 'Ahorro',
                'monto_real' => 0,
                'porcentaje_real' => null,
                'porcentaje_recomendado' => 20,
            ],
        ];

        $detalle = $guardado->detalles->map(function ($item) {
            return [
                'categoria_nombre' => $item->categoria_nombre,
                'bloque' => $item->bloque,
                'monto_gastado' => (int) $item->monto_gastado,
                'porcentaje_real' => (float) $item->porcentaje_real,
                'porcentaje_recomendado' => (int) $item->porcentaje_recomendado,
            ];
        })->all();

        foreach ($detalle as $item) {
            if (isset($bloques[$item['bloque']])) {
                $bloques[$item['bloque']]['monto_real'] +=
                    $item['monto_gastado'];
            }
        }

        $ingreso = (int) $guardado->ingreso;

        foreach ($bloques as &$bloque) {
            $bloque['porcentaje_real'] = $ingreso > 0
                ? round(($bloque['monto_real'] / $ingreso) * 100, 2)
                : null;
        }
        unset($bloque);

        $resumen = [
            'ingreso' => $ingreso,
            'total_gastado' => (int) $guardado->total_gastado,
            'saldo_disponible' => $ingreso - (int) $guardado->total_gastado,
        ];

        $mes = sprintf('%04d-%02d', $guardado->anio, $guardado->mes);

        return $this->descargarInformeCsv(
            $bloques,
            $detalle,
            $resumen,
            'informe-guardado-' . $mes . '.csv'
        );
    }

    /**
     * Construye y descarga el archivo CSV.
     */
    private function descargarInformeCsv(
        array $bloques,
        array $detalle,
        array $resumen,
        string $nombreArchivo
    ) {
        return response()->streamDownload(function () use (
            $bloques,
            $detalle,
            $resumen
        ) {
            $salida = fopen('php://output', 'w');

            // BOM para facilitar la lectura de tildes en Excel.
            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, ['Informe 50/30/20'], ';');
            fputcsv($salida, ['Ingreso mensual', $resumen['ingreso'] ?? 0], ';');
            fputcsv($salida, ['Total gastado', $resumen['total_gastado'] ?? 0], ';');
            fputcsv($salida, ['Saldo disponible', $resumen['saldo_disponible'] ?? 0], ';');
            fputcsv($salida, [], ';');
            fputcsv($salida, [
                'Distribución',
                'Monto gastado',
                'Porcentaje real',
                'Porcentaje recomendado',
            ], ';');

            foreach ($bloques as $bloque) {
                fputcsv($salida, [
                    $bloque['nombre'] ?? '',
                    $bloque['monto_real'] ?? 0,
                    $bloque['porcentaje_real'] ?? '',
                    $bloque['porcentaje_recomendado'] ?? 0,
                ], ';');
            }

            fputcsv($salida, [], ';');
            fputcsv($salida, [
                'Categoría',
                'Distribución',
                'Monto gastado',
                'Porcentaje real',
                'Porcentaje recomendado',
            ], ';');

            foreach ($detalle as $item) {
                fputcsv($salida, [
                    $item['categoria_nombre'] ?? '',
                    $item['bloque'] ?? '',
                    $item['monto_gastado'] ?? 0,
                    $item['porcentaje_real'] ?? '',
                    $item['porcentaje_recomendado'] ?? 0,
                ], ';');
            }

            fclose($salida);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Exporta el informe del mes seleccionado a Word.
     */
    public function exportarWord(
        Request $request,
        GeneradorInforme $generador
    ) {
        $datos = $request->validate([
            'mes' => ['nullable', 'date_format:Y-m'],
        ]);

        $mes = $datos['mes'] ?? now()->format('Y-m');
        $fecha = Carbon::createFromFormat('!Y-m', $mes);

        $resultado = $this->calcularMes(
            $request->user(),
            $fecha,
            $generador
        );

        $bloques = $resultado['calculos']['bloques'];

        foreach ($bloques as &$bloque) {
            $bloque['monto_real'] = $bloque['monto_real']
                ?? $bloque['monto_gastado']
                ?? 0;
        }
        unset($bloque);

        return response()->view('app.informes.exportar-word', [
            'bloques' => $bloques,
            'detalle' => $resultado['detalle'],
            'resumen' => $resultado['calculos'],
            'mes' => $mes,
        ], 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="informe-' . $mes . '.doc"',
        ]);
    }

    /**
     * Exporta un informe histórico a Word usando sus datos guardados.
     */
    public function exportarHistoricoWord(Request $request, int $id)
    {
        $guardado = $request->user()
            ->informes()
            ->with('detalles')
            ->findOrFail($id);

        $nombres = [
            'necesidades' => 'Necesidades',
            'discrecionales' => 'Deseos',
            'ahorro' => 'Ahorro',
            'sin_clasificar' => 'Sin clasificar',
        ];

        $bloques = [];

        foreach ($nombres as $clave => $nombre) {
            $bloques[$clave] = [
                'nombre' => $nombre,
                'monto_real' => 0,
                'porcentaje_real' => null,
                'porcentaje_recomendado' => match ($clave) {
                    'necesidades' => 50,
                    'discrecionales' => 30,
                    'ahorro' => 20,
                    default => 0,
                },
            ];
        }

        $detalle = $guardado->detalles->map(function ($item) {
            return [
                'categoria_nombre' => $item->categoria_nombre,
                'bloque' => $item->bloque,
                'monto_gastado' => (int) $item->monto_gastado,
                'porcentaje_real' => (float) $item->porcentaje_real,
            ];
        })->all();

        foreach ($detalle as $item) {
            $clave = $item['bloque'];

            if (isset($bloques[$clave])) {
                $bloques[$clave]['monto_real'] += $item['monto_gastado'];
            } else {
                $bloques['sin_clasificar']['monto_real'] +=
                    $item['monto_gastado'];
            }
        }

        $ingreso = (int) $guardado->ingreso;

        foreach ($bloques as &$bloque) {
            $bloque['porcentaje_real'] = $ingreso > 0
                ? round(($bloque['monto_real'] / $ingreso) * 100, 2)
                : null;
        }
        unset($bloque);

        $mes = sprintf('%04d-%02d', $guardado->anio, $guardado->mes);

        return response()->view('app.informes.exportar-word', [
            'bloques' => $bloques,
            'detalle' => $detalle,
            'resumen' => [
                'ingreso' => $ingreso,
                'total_gastado' => (int) $guardado->total_gastado,
                'saldo_disponible' => $ingreso - (int) $guardado->total_gastado,
            ],
            'mes' => $mes,
        ], 200, [
            'Content-Type' => 'application/msword; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="informe-guardado-' . $mes . '.doc"',
        ]);
    }


    /**
     * Calcula los gastos del usuario para el mes indicado.
     *
     * No guarda ni modifica registros.
     */
    public function calcularMes(
        User $usuario,
        Carbon $fecha,
        GeneradorInforme $generador
    ): array {
        $inicio = $fecha->copy()->startOfMonth()->toDateString();
        $fin = $fecha->copy()->endOfMonth()->toDateString();

        $gastos = Gasto::query()
            ->where('user_id', $usuario->id)
            ->whereBetween('fecha', [$inicio, $fin])
            ->with('categoria')
            ->get();


        $totalesPorBloque = [
            'necesidades' => 0,
            'discrecionales' => 0,
            'ahorro' => 0,
            'sin_clasificar' => 0,
        ];

        $categorias = [];

        foreach ($gastos as $gasto) {
            $categoria = $gasto->categoria;

            $bloque = $categoria && in_array(
                $categoria->bloque,
                Categoria::BLOQUES,
                true
            ) ? $categoria->bloque : 'sin_clasificar';

            $monto = (int) $gasto->monto;
            $totalesPorBloque[$bloque] += $monto;

            $clave = $categoria
                ? (string) $categoria->id
                : 'sin_categoria';

            if (!isset($categorias[$clave])) {
                $categorias[$clave] = [
                    'categoria_id' => $categoria?->id,
                    'categoria_nombre' => $categoria?->nombre
                        ?? 'Sin categoría',
                    'bloque' => $bloque,
                    'monto_gastado' => 0,
                ];
            }

            $categorias[$clave]['monto_gastado'] += $monto;
        }
        
        // Incluir únicamente los gastos fijos pagados durante el mes consultado.
        $pagosFijos = PagoGastoFijo::query()
            ->where('user_id', $usuario->id)
            ->where('estado', 'pagado')
            ->whereBetween('fecha_pago', [$inicio, $fin])
            ->with('gastoFijo.categoria')
            ->get();

        foreach ($pagosFijos as $pago) {
            $categoria = $pago->gastoFijo?->categoria;

            $bloque = $categoria && in_array(
                $categoria->bloque,
                Categoria::BLOQUES,
                true
            ) ? $categoria->bloque : 'sin_clasificar';

            $monto = (int) $pago->monto;
            $totalesPorBloque[$bloque] += $monto;

            $clave = $categoria
                ? (string) $categoria->id
                : 'sin_categoria';

            if (!isset($categorias[$clave])) {
                $categorias[$clave] = [
                    'categoria_id' => $categoria?->id,
                    'categoria_nombre' => $categoria?->nombre
                        ?? 'Sin categoría',
                    'bloque' => $bloque,
                    'monto_gastado' => 0,
                ];
            }

            $categorias[$clave]['monto_gastado'] += $monto;
        }

// Identificar gastos hormiga según su frecuencia e impacto mensual.
$totalGastosMes = array_sum($totalesPorBloque);

$gastosAgrupados = $gastos
    ->filter(function ($gasto) {
        return !empty(trim((string) $gasto->descripcion));
    })
    ->groupBy(function ($gasto) {
        return mb_strtolower(
            preg_replace('/\s+/', ' ', trim($gasto->descripcion))
        );
    });

$gastosHormiga = $gastosAgrupados
    ->map(function ($grupo) use ($totalGastosMes) {
        $primerGasto = $grupo->first();

        $total = (int) $grupo->sum('monto');
        $frecuencia = $grupo->count();

        $porcentaje = $totalGastosMes > 0
            ? round(($total / $totalGastosMes) * 100, 2)
            : 0;

        return [
            'concepto' => trim($primerGasto->descripcion),
            'frecuencia' => $frecuencia,
            'total' => $total,
            'porcentaje' => $porcentaje,
            'registros' => $grupo->sortByDesc('fecha')->values(),
        ];
    })
    ->filter(function ($item) {
        return $item['frecuencia'] >= 5
            && $item['porcentaje'] >= 5;
    })
    ->sortByDesc('total')
    ->values();

$totalGastosHormiga = (int) $gastosHormiga->sum('total');
$cantidadGastosHormiga = (int) $gastosHormiga->sum('frecuencia');

$resumenGastosHormiga = [
    'total' => $totalGastosHormiga,
    'cantidad' => $cantidadGastosHormiga,
    'conceptos' => $gastosHormiga->count(),
    'porcentaje' => $totalGastosMes > 0
        ? round(($totalGastosHormiga / $totalGastosMes) * 100, 2)
        : 0,
    'promedio' => $cantidadGastosHormiga > 0
        ? (int) round($totalGastosHormiga / $cantidadGastosHormiga)
        : 0,
    'detalle' => $gastosHormiga,
];
        $calculos = $generador->generar(

            (int) ($usuario->ingreso_mensual ?? 0),
            $totalesPorBloque
        );

        $detalle = array_values(array_map(
            function (array $item) use ($usuario, $calculos) {
                $ingreso = (int) ($usuario->ingreso_mensual ?? 0);

                $item['porcentaje_real'] = $ingreso > 0
                    ? round(($item['monto_gastado'] / $ingreso) * 100, 2)
                    : null;

                $item['porcentaje_recomendado'] = match ($item['bloque']) {
                    'necesidades' => 50,
                    'discrecionales' => 30,
                    'ahorro' => 20,
                    default => 0,
                };

                $item['excede'] = isset(
                    $calculos['bloques'][$item['bloque']]
                ) && $calculos['bloques'][$item['bloque']]['estado'] === 'excede';

                return $item;
            },
            $categorias
        ));

        return [
    'calculos' => $calculos,
    'detalle' => $detalle,
    'gastosHormiga' => $resumenGastosHormiga,
];
    }
}
