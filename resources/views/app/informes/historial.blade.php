
@extends('layouts.app')

@section('titulo', 'Informes anteriores · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/informes-historial.css') }}">
@endpush

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

        <header>
            <h1 class="font-headline-xl text-headline-xl text-on-surface">
                Informes anteriores
            </h1>

            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Consulta tus informes mensuales guardados y compara cómo distribuías tu dinero.
            </p>
        </header>

        <section class="card p-space-lg" data-dinamico="informes">

<form action="{{ route('informes.historial') }}"
      method="GET"
      class="flex flex-col sm:flex-row sm:items-end flex-wrap gap-space-md mb-space-lg">

    <div class="flex flex-col gap-2">
        <label for="anio">Año</label>
        <input
            type="number"
            id="anio"
            name="anio"
            min="2000"
            max="{{ now()->year + 5 }}"
            value="{{ $anioSeleccionado }}"
            placeholder="Ej. {{ now()->year }}"
            class="border rounded-lg p-2 w-full sm:w-40"
        >
    </div>

    <div class="flex flex-col gap-2">
        <label for="mes">Mes</label>
        <select
            id="mes"
            name="mes"
            class="border rounded-lg p-2 w-full sm:w-48"
        >
            <option value="">Todos los meses</option>
            @foreach ([
                1 => 'Enero',
                2 => 'Febrero',
                3 => 'Marzo',
                4 => 'Abril',
                5 => 'Mayo',
                6 => 'Junio',
                7 => 'Julio',
                8 => 'Agosto',
                9 => 'Septiembre',
                10 => 'Octubre',
                11 => 'Noviembre',
                12 => 'Diciembre',
            ] as $numero => $nombre)
                <option
                    value="{{ $numero }}"
                    @selected((string) $mesSeleccionado === (string) $numero)
                >
                    {{ $nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">
            Filtrar
        </button>

        <a href="{{ route('informes.historial') }}"
           class="btn border rounded-lg px-4 py-2">
            Limpiar filtros
        </a>
    </div>
</form>

            <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                Informes por mes
            </h2>

            @forelse ($informes as $item)
                @php
                    $totalesBloque = [
                        'necesidades' => 0,
                        'discrecionales' => 0,
                        'ahorro' => 0,
                    ];

                    foreach ($item->detalles as $detalle) {
                        if (array_key_exists($detalle->bloque, $totalesBloque)) {
                            $totalesBloque[$detalle->bloque] += (int) $detalle->monto_gastado;
                        }
                    }

                    $bloquesMostrar = [
                        'necesidades' => [
                            'nombre' => 'Necesidades',
                            'recomendado' => 50,
                        ],
                        'discrecionales' => [
                            'nombre' => 'Gastos discrecionales',
                            'recomendado' => 30,
                        ],
                        'ahorro' => [
                            'nombre' => 'Ahorro',
                            'recomendado' => 20,
                        ],
                    ];
                @endphp

                <article class="border rounded-lg p-space-md mb-space-md">
                    <div class="flex flex-wrap justify-between items-start gap-3">
                        <div>
                            <h3 class="font-headline-sm text-on-surface">
                                {{ \Carbon\Carbon::create($item->anio, $item->mes, 1)->locale('es')->translatedFormat('F Y') }}
                            </h3>

                            <p class="text-on-surface-variant mt-1">
                                Informe guardado el
                                {{ $item->created_at->format('d/m/Y') }}
                            </p>
                        </div>

                        <span class="text-sm border rounded-lg px-3 py-1">
                            Guardado
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md mt-space-md">
                        <div class="border rounded-lg p-space-md">
                            <p class="text-on-surface-variant">Ingreso registrado</p>
                            <p class="font-headline-sm">
                                ${{ number_format($item->ingreso, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="border rounded-lg p-space-md">
                            <p class="text-on-surface-variant">Total gastado</p>
                            <p class="font-headline-sm">
                                ${{ number_format($item->total_gastado, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-space-md">
                        <h4 class="font-headline-sm mb-3">
                            Distribución registrada
                        </h4>

                        @foreach ($bloquesMostrar as $clave => $bloque)
                            @php
                                $monto = $totalesBloque[$clave];
                                $porcentaje = (int) $item->ingreso > 0
                                    ? ($monto / (int) $item->ingreso) * 100
                                    : null;
                                $excede = $porcentaje !== null
                                    && $porcentaje > $bloque['recomendado'];
                            @endphp

                            <div class="border-b py-3">
                                <div class="flex flex-wrap justify-between gap-2">
                                    <span>{{ $bloque['nombre'] }}</span>

                                    <strong>
                                        {{ $porcentaje === null
                                            ? 'No disponible'
                                            : number_format($porcentaje, 1, ',', '.') . '%' }}
                                    </strong>
                                </div>

                                <p class="text-on-surface-variant text-sm mt-1">
                                    Recomendado: {{ $bloque['recomendado'] }}%
                                    · Gastado: ${{ number_format($monto, 0, ',', '.') }}
                                </p>

                                <p class="text-sm mt-1">
                                    @if ($porcentaje === null)
                                        No se puede calcular sin un ingreso registrado.
                                    @elseif ($excede)
                                        Supera el porcentaje recomendado.
                                    @elseif ($clave === 'ahorro' && $porcentaje < 20)
                                        Está por debajo del porcentaje recomendado.
                                    @else
                                        Dentro del límite recomendado.
                                    @endif
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-space-md">
                        <a
                            href="{{ route('informes.ver', $item->id) }}"
                            class="btn btn-primary inline-block"
                        >
                            Ver informe guardado
                        </a>
                    </div>
                </article>
            @empty
                <div class="py-space-lg text-center" role="status">
                    <h3 class="font-headline-sm text-on-surface">
                        No hay informes anteriores
                    </h3>

                    <p class="font-body-md text-on-surface-variant mt-2">
                        Cuando guardes tu primer informe mensual, aparecerá aquí.
                    </p>

                    <a
                        href="{{ route('informes.actual') }}"
                        class="underline inline-block mt-3"
                    >
                        Ir al informe mensual
                    </a>
                </div>
            @endforelse

            @if ($informes->hasPages())
                <div class="mt-space-lg">
                    {{ $informes->links() }}
                </div>
            @endif
        </section>

    </div>
</main>
@endsection
