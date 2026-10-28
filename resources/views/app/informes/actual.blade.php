
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
                        <div class="mt-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span>Real</span>
                                <span>Recomendado</span>
                            </div>

                            <div class="w-full rounded-full overflow-hidden"
                                 style="height: 12px; background: #e5e7eb;">
                                <div
                                    style="height: 12px; width: {{ min(100, max(0, $item['porcentaje_real'])) }}%; background: #5454e9;"
                                    role="img"
                                    aria-label="Porcentaje real: {{ number_format($item['porcentaje_real'], 1, ',', '.') }}%"
                                ></div>
                            </div>

                            <p class="text-sm mt-1">
                                Diferencia:
                                {{ $item['diferencia_porcentual'] > 0 ? '+' : '' }}{{ number_format($item['diferencia_porcentual'], 1, ',', '.') }}
                                puntos porcentuales
                            </p>
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
                        Guarda una fotografía de los valores de este mes para consultarla después.
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
                        Ya existe un informe guardado para este mes.
                    </p>
                @endif
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
