<?php

namespace App\Services;

use App\Models\Gasto;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * ARCHIVO : app/Services/ServicioGastosHormiga.php
 * QUÉ ES  : Detecta los gastos hormiga REALES de un usuario en un mes.
 *
 * REGLA (ajustable con las dos constantes):
 *   Un gasto hormiga es un consumo pequeño (monto <= MONTO_MAXIMO_COMPRA)
 *   que se repite con el mismo concepto (descripción) al menos
 *   FRECUENCIA_MINIMA veces dentro del mes.
 *
 * RESULTADO:
 *   - null  → no hay gastos hormiga (la vista NO muestra el letrero).
 *   - array → total, conceptos, porcentaje, detalle (Collection), criterios.
 */
class ServicioGastosHormiga
{
    public const MONTO_MAXIMO_COMPRA = 20000;
    public const FRECUENCIA_MINIMA   = 3;

    /**
     * @param  int       $userId        Usuario dueño de los gastos.
     * @param  string    $mes           Formato 'Y-m' (ej. 2026-11).
     * @param  int|null  $totalGastado  Total gastado del mes; si es null se calcula aquí.
     */
    public function analizar(int $userId, string $mes, ?int $totalGastado = null): ?array
    {
        try {
            $inicio = Carbon::createFromFormat('!Y-m', $mes)->startOfMonth();
        } catch (\Throwable $e) {
            return null;
        }

        $fin = $inicio->copy()->endOfMonth();

        $gastosMes = Gasto::query()
            ->where('user_id', $userId)
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()]);

        $totalMes = $totalGastado ?? (int) (clone $gastosMes)->sum('monto');

        $candidatos = (clone $gastosMes)
            ->where('monto', '>', 0)
            ->where('monto', '<=', self::MONTO_MAXIMO_COMPRA)
            ->whereNotNull('descripcion')
            ->get(['id', 'monto', 'fecha', 'descripcion'])
            ->filter(fn ($gasto) => trim((string) $gasto->descripcion) !== '');

        $detalle = $candidatos
            ->groupBy(fn ($gasto) => $this->normalizar($gasto->descripcion))
            ->filter(fn (Collection $grupo) => $grupo->count() >= self::FRECUENCIA_MINIMA)
            ->map(function (Collection $grupo) use ($totalMes) {
                $total = (int) $grupo->sum('monto');
                $veces = $grupo->count();

                return [
                    'concepto'   => Str::ucfirst(Str::squish((string) $grupo->first()->descripcion)),
                    'frecuencia' => $veces,
                    'total'      => $total,
                    'promedio'   => (int) round($total / $veces),
                    'porcentaje' => $totalMes > 0 ? round(($total / $totalMes) * 100, 2) : 0.0,
                ];
            })
            ->sortByDesc('total')
            ->values();

        if ($detalle->isEmpty()) {
            return null;
        }

        $total = (int) $detalle->sum('total');

        return [
            'total'      => $total,
            'conceptos'  => $detalle->count(),
            'porcentaje' => $totalMes > 0 ? round(($total / $totalMes) * 100, 2) : 0.0,
            'detalle'    => $detalle,
            'criterios'  => [
                'monto_maximo' => self::MONTO_MAXIMO_COMPRA,
                'frecuencia'   => self::FRECUENCIA_MINIMA,
            ],
        ];
    }

    /** "Café ", "cafe" y "CAFÉ" cuentan como el mismo concepto. */
    private function normalizar(string $texto): string
    {
        return Str::of($texto)->squish()->ascii()->lower()->toString();
    }
}