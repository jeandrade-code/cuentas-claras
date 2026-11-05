
@extends('layouts.app')

@section('titulo', 'Informe 50/30/20 · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/informes-50-30-20.css') }}">
@endpush

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

        <header>
            <h1 class="font-headline-xl text-headline-xl text-on-surface">
                Informe 50/30/20
            </h1>

            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                {{ !empty($esHistorico) ? 'Consulta de un informe guardado' : 'Cómo se distribuyó tu gasto' }}
            </p>
        </header>

        @if (session('error'))
            <div role="alert" class="card p-space-md">
                {{ session('error') }}
            </div>
        @endif

        @if (!empty($esHistorico))
            <p class="font-body-md text-on-surface-variant">
                Este informe es una fotografía guardada.
                No cambia cuando modificas tus gastos actuales.
                @if (!empty($fechaGuardado))
                    Guardado el {{ $fechaGuardado->format('d/m/Y H:i') }}.
                @endif
            </p>
        @endif

        <section class="card p-space-lg" data-tour="inf-selector">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                Selecciona el mes
            </h2>

            <form action="{{ route('informes.actual') }}" method="GET"
                  class="flex flex-wrap items-end gap-space-md">
                <div>
                    <label for="mes" class="block mb-2">Mes del informe</label>
                    <input
                        type="month"
                        id="mes"
                        name="mes"
                        value="{{ $mesSeleccionado ?? now()->format('Y-m') }}"
                        required
                        class="border rounded-lg p-2"
                        {{ !empty($esHistorico) ? 'disabled' : '' }}
                    >
                </div>

                @if (empty($esHistorico))
                    <button type="submit" class="btn btn-primary">
                        Consultar mes
                    </button>
                @endif
            </form>
            </section>

        @if (!empty($esHistorico) || !empty($informeDisponible))

        @if (empty($resumen['porcentajes_disponibles']))
            <section class="card p-space-lg" role="status">
                <h2 class="font-headline-md text-headline-md text-on-surface">
                    Registra tu ingreso mensual
                </h2>
                <p class="mt-2">
                    {{ $resumen['mensaje_general'] ?? 'Necesitas registrar tu ingreso para calcular los porcentajes.' }}
                </p>
                <a href="{{ route('ajustes.perfil') }}" class="underline mt-3 inline-block">
                    Ir a Ajustes
                </a>
            </section>
        @elseif (!empty($resumen['sin_gastos']))
            <section class="card p-space-lg" role="status">
                <h2 class="font-headline-md text-headline-md text-on-surface">
                    Aún no tienes gastos en este mes
                </h2>
                <p class="mt-2">
                    Puedes registrar tus gastos para consultar cómo se distribuye tu dinero.
                </p>
            </section>
        @endif

        <section class="card p-space-lg" data-tour="inf-resumen">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                Resumen del mes
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
                <div class="p-space-md rounded-lg border">
                    <p class="text-on-surface-variant">Ingreso mensual</p>
                    <p class="font-headline-md">
                        ${{ number_format($resumen['ingreso'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="p-space-md rounded-lg border">
                    <p class="text-on-surface-variant">Total gastado</p>
                    <p class="font-headline-md">
                        ${{ number_format($resumen['total_gastado'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>

                <div class="p-space-md rounded-lg border">
                    <p class="text-on-surface-variant">Saldo disponible</p>
                    <p class="font-headline-md">
                        ${{ number_format($resumen['saldo_disponible'] ?? 0, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </section>

        <section class="card p-space-lg" data-dinamico="informe" data-tour="inf-distribucion">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                Distribución real vs recomendada
            </h2>
<div class="flex flex-wrap items-center gap-4 mb-space-lg text-sm text-on-surface-variant" aria-label="Leyenda de la gráfica">
    <div class="flex items-center gap-2">
        <span aria-hidden="true" style="display:inline-block; width:22px; height:10px; border-radius:999px; background:var(--primary);"></span>
        <span>Porcentaje real</span>
    </div>

    <div class="flex items-center gap-2">
        <span aria-hidden="true" style="display:inline-block; width:3px; height:16px; border-radius:2px; background:var(--on-surface);"></span>
        <span>Porcentaje recomendado</span>
    </div>
</div>
            @forelse(($informe ?? []) as $clave => $item)
                <article class="border rounded-lg p-space-md mb-space-md">
                    <div class="flex flex-wrap justify-between gap-2">
                        <h3 class="font-headline-sm">
                            {{ $item['nombre'] }}
                        </h3>

                        <span>
                            {{ match ($item['estado']) {
    'sin_calcular' => 'Sin calcular',
    'excede' => 'Excede lo recomendado',
    'dentro' => 'Dentro de lo recomendado',
    default => 'Sin calcular',
} }}
                        </span>
                    </div>

                    <p class="mt-2">
                        Gastado:
                        <strong>${{ number_format($item['monto_real'], 0, ',', '.') }}</strong>
                    </p>

                    <p>
                        Porcentaje real:
                        {{ $item['porcentaje_real'] === null
                            ? 'No disponible'
                            : number_format($item['porcentaje_real'], 1, ',', '.') . '%' }}
                    </p>

                    <p>
                        Porcentaje recomendado:
                        {{ $item['porcentaje_recomendado'] }}%
                    </p>

                    @if ($item['porcentaje_real'] !== null)
                        
<div class="mt-4">
    <div class="flex flex-wrap justify-between gap-2 text-sm mb-2">
        <span class="font-semibold text-on-surface">
            Distribución del ingreso
        </span>

        <span class="text-on-surface-variant">
            Real: {{ number_format($item['porcentaje_real'], 1, ',', '.') }}%
            / Recomendado: {{ $item['porcentaje_recomendado'] }}%
        </span>
    </div>

    
<div
    class="relative w-full rounded-full overflow-hidden"
    style="height: 16px; background: var(--surface-container);"
    role="img"
    aria-label="Porcentaje real: {{ number_format($item['porcentaje_real'], 1, ',', '.') }}%. Porcentaje recomendado: {{ $item['porcentaje_recomendado'] }}%.">

    <div
        aria-hidden="true"
        style="height: 100%; width: {{ min(100, max(0, $item['porcentaje_real'])) }}%; background: var(--primary); border-radius: 999px; transition: width 0.35s ease;">
    </div>

    <div
        aria-hidden="true"
        style="position: absolute; top: 0; bottom: 0; left: {{ min(100, max(0, $item['porcentaje_recomendado'])) }}%; width: 3px; transform: translateX(-50%); background: var(--on-surface); border-radius: 2px; box-shadow: 0 0 0 1px var(--surface-container-lowest);">
    </div>
</div>


    <div class="flex justify-between gap-2 mt-2 text-sm">
        <span class="text-on-surface-variant">
            Recomendación: {{ $item['porcentaje_recomendado'] }}%
        </span>

        <span class="font-semibold text-on-surface">
            Diferencia:
            {{ $item['diferencia_porcentual'] > 0 ? '+' : '' }}{{ number_format($item['diferencia_porcentual'], 1, ',', '.') }}
            puntos
        </span>
    </div>
</div>

                    @endif

                    <p class="mt-3">
                        {{ $item['mensaje'] }}
                    </p>
                </article>
            @empty
                <p>No hay datos suficientes para generar el informe.</p>
            @endforelse

            @if (($resumen['monto_sin_clasificar'] ?? 0) > 0)
                <div class="border rounded-lg p-space-md mt-space-md">
                    <h3 class="font-headline-sm">Sin clasificar</h3>
                    <p>
                        ${{ number_format($resumen['monto_sin_clasificar'], 0, ',', '.') }}
                    </p>
                    <p>
                        Porcentaje del ingreso:
                        {{ $resumen['porcentaje_sin_clasificar'] === null
                            ? 'No disponible'
                            : number_format($resumen['porcentaje_sin_clasificar'], 1, ',', '.') . '%' }}
                    </p>
                </div>
            @endif
        </section>
        
@php
    $datosDistribucion = collect($informe ?? [])
        ->map(function ($item) {
            return [
                'nombre' => $item['nombre'] ?? '',
                'monto' => (int) (
                    $item['monto_real']
                    ?? $item['monto_gastado']
                    ?? 0
                ),
            ];
        })
        ->filter(fn ($item) => $item['monto'] > 0)
        ->values();
@endphp

<div class="tarjeta-informe">
    <h3>Distribución de tus gastos</h3>
    <p>Compara cuánto dinero se destinó a cada bloque.</p>

    <div style="position: relative; width: 100%; max-width: 420px; height: 300px; margin: 0 auto;">
        <canvas
            id="grafica-distribucion"
            data-chart-data="{{ json_encode($datosDistribucion) }}"
            role="img"
            aria-label="Gráfica de dona de la distribución de gastos por bloque">
        </canvas>
    </div>

    @if ($datosDistribucion->isEmpty())
        <p>Aún no hay gastos registrados para mostrar esta gráfica.</p>
    @endif
</div>

        
<section class="card p-space-lg" data-dinamico="grafica-categorias">
    <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
        ¿En qué categorías gastaste más?
    </h2>

    @php
        $categoriasGrafica = collect($detalle ?? [])
            ->filter(fn ($item) => ($item['monto_gastado'] ?? 0) > 0)
            ->sortByDesc('monto_gastado')
            ->values();

        $maximoCategoria = $categoriasGrafica->max('monto_gastado') ?? 0;
    @endphp

    @if ($categoriasGrafica->isEmpty())
        <p class="text-on-surface-variant">
            No hay gastos por categoría para representar en la gráfica.
        </p>
    @else

<div class="flex flex-col gap-space-md">
    @foreach ($categoriasGrafica as $categoria)
        @php
            $colorBloque = match ($categoria['bloque'] ?? 'sin_clasificar') {
                'necesidades' => 'var(--need)',
                'discrecionales', 'deseos' => 'var(--want)',
                'ahorro' => 'var(--save)',
                default => 'var(--primary)',
            };

            $montoCategoria = (int) ($categoria['monto_gastado'] ?? 0);

            $porcentajeCategoria = $maximoCategoria > 0
                ? ($montoCategoria / $maximoCategoria) * 100
                : 0;
        @endphp

        <div>
            <div class="flex justify-between items-center gap-space-sm mb-space-xs">
                <span class="text-on-surface">
                    {{ $categoria['categoria_nombre'] ?? 'Sin categoría' }}
                </span>

                <span class="text-on-surface font-medium">
                    ${{ number_format($montoCategoria, 0, ',', '.') }}
                </span>
            </div>

            <div class="w-full rounded-full overflow-hidden"
                 style="height: 10px; background: var(--surface-container);">
                <div
                    role="progressbar"
                    aria-label="Gasto en {{ $categoria['categoria_nombre'] ?? 'Sin categoría' }}"
                    aria-valuenow="{{ round($porcentajeCategoria) }}"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    style="width: {{ $porcentajeCategoria }}%; height: 100%; background: {{ $colorBloque }}; border-radius: 9999px;">
                </div>
            </div>
        </div>
    @endforeach
</div>


        <p class="text-sm text-on-surface-variant mt-space-md">
            Las barras comparan los montos entre categorías del periodo seleccionado.
        </p>
   
    @endif
</section>

@php
    $datosCategoriasGrafica = collect($detalle ?? [])
    ->filter(fn($item) => (int) ($item['monto_gastado'] ?? 0) > 0)
    ->sortByDesc('monto_gastado')
    ->map(fn($item) => [
        'nombre' => $item['categoria_nombre'] ?? 'Sin categoría',
        'monto' => (int) $item['monto_gastado'],
        'bloque' => $item['bloque'] ?? 'sin_clasificar',
    ])
    ->values();
@endphp

<div class="tarjeta-informe">
    <h3>Gastos por categoría</h3>
    <p>Identifica las categorías en las que más dinero gastaste.</p>

    @if ($datosCategoriasGrafica->isNotEmpty())
        <div style="position: relative; width: 100%; height: {{ max(240, min(600, $datosCategoriasGrafica->count() * 45)) }}px;">
            <canvas
                id="grafica-categorias-chart"
                data-chart-data="{{ json_encode($datosCategoriasGrafica) }}"
                role="img"
                aria-label="Gráfica de barras de gastos por categoría">
            </canvas>
        </div>
    @else
        <p>Aún no hay gastos registrados para mostrar esta gráfica.</p>
    @endif
</div>



{{-- Indicador de gastos hormiga --}}
@if (isset($gastosHormiga))
<section class="card p-space-lg gastos-hormiga" data-dinamico="gastos-hormiga">
    <div class="gastos-hormiga__encabezado">
    <div class="gastos-hormiga__icono" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg"
        width="26"
        height="26"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round">
        <path d="m10.3 3.9-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3.1l-8-14a2 2 0 0 0-3.4 0Z"/>
        <path d="M12 9v4"/>
        <path d="M12 17h.01"/>
    </svg>
</div>

    <div>
        <p class="gastos-hormiga__etiqueta">¡Atención! Revisa tus hábitos de consumo</p>

        <h2 class="font-headline-md text-headline-md">
            Gastos hormiga
        </h2>

        <p class="mt-1">
            Identificamos consumos recurrentes que están teniendo un impacto importante en tu presupuesto.
        </p>
    </div>
</div>

    <div class="gastos-hormiga__indicadores">
        <article class="gastos-hormiga__indicador">
            <span>Total acumulado</span>
            <strong>${{ number_format($gastosHormiga['total'], 0, ',', '.') }}</strong>
        </article>

        <article class="gastos-hormiga__indicador">
            <span>Conceptos identificados</span>
            <strong>{{ $gastosHormiga['conceptos'] }}</strong>
        </article>

        <article class="gastos-hormiga__indicador">
            <span>Impacto en tus gastos</span>
            <strong>{{ number_format($gastosHormiga['porcentaje'], 2, ',', '.') }} %</strong>
        </article>
    </div>

    <div class="gastos-hormiga__detalle">
        <h3 class="font-headline-sm text-on-surface">
            Hábitos de consumo identificados
        </h3>

        @if ($gastosHormiga['detalle']->isEmpty())
            <p class="gastos-hormiga__vacio">
                Aún no identificamos gastos recurrentes que cumplan los criterios establecidos. Registra tus consumos durante el mes para conocer su impacto.
            </p>
        @else
            <div class="gastos-hormiga__lista">
                @foreach ($gastosHormiga['detalle'] as $gastoHormiga)
                    <article class="gastos-hormiga__compra">
                        <div class="gastos-hormiga__compra-info">
                            <strong>{{ $gastoHormiga['concepto'] }}</strong>
                            <span>
                                {{ $gastoHormiga['frecuencia'] }} registros durante el mes
                            </span>
                            <span>
                                Representa {{ number_format($gastoHormiga['porcentaje'], 2, ',', '.') }} % de tus gastos
                            </span>
                        </div>

                        <strong class="gastos-hormiga__monto">
                            ${{ number_format($gastoHormiga['total'], 0, ',', '.') }}
                        </strong>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
        <div class="gastos-hormiga__recomendacion">
        <svg xmlns="http://www.w3.org/2000/svg"
        width="22"
        height="22"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        aria-hidden="true">
    <path d="M9 18h6"/>
    <path d="M10 22h4"/>
    <path d="M15.09 14a6 6 0 1 0-6.18 0c.6.45 1.09 1.16 1.09 2h4c0-.84.49-1.55 1.09-2Z"/>
</svg>

        <div>
            <strong>¿Qué puedes hacer?</strong>
            Revisa cuáles de estos consumos puedes reducir o evitar.
            Pequeños cambios en tus hábitos pueden ayudarte a liberar dinero durante el mes.
        </div>
    </div>
</section>
@endif

<section class="card p-space-lg" data-dinamico="detalle">

            <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                Detalle por categoría
            </h2>




            @forelse(($detalle ?? []) as $item)
                <article class="border-b py-space-md">
                    <div class="flex flex-wrap justify-between gap-2">
                        <h3 class="font-headline-sm">
                            {{ $item['categoria_nombre'] }}
                        </h3>

                        <strong>
                            ${{ number_format($item['monto_gastado'], 0, ',', '.') }}
                        </strong>
                    </div>

                    <p class="text-on-surface-variant mt-1">
                        Bloque: {{ $item['bloque'] === 'sin_clasificar'
                            ? 'Sin clasificar'
                            : ucfirst($item['bloque']) }}
                    </p>

                    <p class="text-on-surface-variant">
                        Porcentaje del ingreso:
                        {{ $item['porcentaje_real'] === null
                            ? 'No disponible'
                            : number_format($item['porcentaje_real'], 1, ',', '.') . '%' }}
                    </p>
                </article>
            @empty
                <p>Sin categorías en el periodo.</p>
            @endforelse
        </section>

        @if (empty($esHistorico) && !empty($resumen['porcentajes_disponibles']))
            <section class="card p-space-lg">
                @if (empty($informeGuardado))
                    <h2 class="font-headline-md text-headline-md text-on-surface">
                        Guardar informe mensual
                    </h2>

                    <p class="mt-2">
                        Conserva el resumen financiero de este mes para revisarlo cuando lo necesites.
                    </p>

                    <form action="{{ route('informes.guardar') }}" method="POST" class="mt-4">
                        @csrf
                        <input
                            type="hidden"
                            name="mes"
                            value="{{ $mesSeleccionado }}"
                        >

                        <button type="submit" class="btn btn-primary">
                            Guardar informe
                        </button>
                    </form>
                @else
                    <p role="status">
                        El informe de este mes ya está guardado y disponible en tu historial
                    </p>
                @endif
            </section>
        @endif

        
<section class="card p-space-lg" data-exportaciones>
    <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
        Exportar informe
    </h2>

    <p class="text-on-surface-variant mb-space-md">
        Descarga una copia del informe del periodo seleccionado.
    </p>

    

<div class="flex flex-wrap gap-space-lg">
    @if (!empty($esHistorico))
        <a
            href="{{ route('informes.historico.csv', request()->route('id')) }}"
            class="btn btn-primary"
        >
            Descargar CSV
        </a>

        <a
            href="{{ route('informes.historico.word', request()->route('id')) }}"
            class="btn btn-primary"
        >
            Descargar Word
        </a>
    @else
        <a
            href="{{ route('informes.exportar.csv', ['mes' => $mesSeleccionado]) }}"
            class="btn btn-primary"
        >
            Descargar CSV
        </a>

        <a
            href="{{ route('informes.exportar.word', ['mes' => $mesSeleccionado]) }}"
            class="btn btn-primary"
        >
            Descargar Word
        </a>
    @endif

    <button
        type="button"
        class="btn btn-primary btn-pdf"
        onclick="window.print()"
    >
        Descargar PDF
    </button>
</div>

</section>

@else
    <section class="card p-space-lg" role="status">
        <h2 class="font-headline-md text-headline-md text-on-surface">
            Informe aún no disponible
        </h2>

        <p class="mt-2">
            El informe del mes seleccionado todavía no está disponible.
            Podrás consultarlo y guardarlo cuando termine el mes.
        </p>
    </section>
@endif

<div>
    <a href="{{ route('informes.historial') }}" class="underline">
        Consultar informes anteriores
    </a>
</div>


    </div>
</main>
{{-- ONBOARDING TOUR --}}
<x-onboarding-tour name="informes" :auto-start="true" />
@endsection
