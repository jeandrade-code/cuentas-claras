@extends('layouts.app')

@section('titulo', 'Informes anteriores · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/informes-historial.css') }}">
    <link rel="stylesheet" href="{{ asset('css/informes-mejoras.css') }}">
@endpush

@php
    $filtrosActivos = filled($anioSeleccionado ?? null) || filled($mesSeleccionado ?? null);

    $mesesNombre = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    $bloquesMostrar = [
        'necesidades'    => ['nombre' => 'Necesidades',           'recomendado' => 50, 'color' => 'var(--need)'],
        'discrecionales' => ['nombre' => 'Gastos discrecionales', 'recomendado' => 30, 'color' => 'var(--want)'],
        'ahorro'         => ['nombre' => 'Ahorro',                'recomendado' => 20, 'color' => 'var(--save)'],
    ];
@endphp

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

        {{-- CABECERA --}}
        <header class="inf-cabecera">
            <div>
                <h1 class="font-headline-xl text-headline-xl text-on-surface">
                    Informes anteriores
                </h1>

                <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                    Consulta tus informes mensuales guardados y compara cómo distribuías tu dinero.
                </p>
            </div>

            <a href="{{ route('informes.actual') }}" class="btn btn-primary inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">query_stats</span>
                Ir al informe mensual
            </a>
        </header>

        <section class="card p-space-lg" data-dinamico="informes">

            {{-- FILTROS --}}
            <div class="flex items-center gap-2 mb-space-md">
                <span class="material-symbols-outlined text-primary text-[22px]">filter_list</span>
                <h2 class="font-headline-md text-headline-md text-on-surface">
                    Filtrar historial
                </h2>
            </div>

            <form action="{{ route('informes.historial') }}" method="GET" class="inf-filtros">
                <div class="inf-filtros__campo">
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

                <div class="inf-filtros__campo">
                    <label for="mes">Mes</label>
                    <select id="mes" name="mes" class="border rounded-lg p-2 w-full sm:w-48">
                        <option value="">Todos los meses</option>
                        @foreach ($mesesNombre as $numero => $nombre)
                            <option
                                value="{{ $numero }}"
                                @selected((string) $mesSeleccionado === (string) $numero)
                            >
                                {{ $nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="inf-filtros__botones">
                    <button type="submit" class="btn btn-primary inline-flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">search</span>
                        Filtrar
                    </button>

                    @if ($filtrosActivos)
                        <a href="{{ route('informes.historial') }}" class="btn btn-secondary inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">close</span>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>

            <hr class="border-outline-variant/30 my-space-lg">

            {{-- CONTEO Y LISTADO --}}
            <div class="flex items-center justify-between gap-4 mb-space-md">
                <h2 class="font-headline-md text-headline-md text-on-surface">
                    Informes guardados
                </h2>

                @if ($informes->total() > 0)
                    <span class="text-sm font-medium text-on-surface-variant bg-surface-container px-3 py-1 rounded-full">
                        {{ $informes->total() }}
                        {{ $informes->total() === 1 ? 'informe' : 'informes' }}
                        @if ($filtrosActivos) (filtrados) @endif
                    </span>
                @endif
            </div>

            @forelse ($informes as $item)
                @php
                    $totalesBloque = ['necesidades' => 0, 'discrecionales' => 0, 'ahorro' => 0];

                    foreach ($item->detalles as $detalle) {
                        if (array_key_exists($detalle->bloque, $totalesBloque)) {
                            $totalesBloque[$detalle->bloque] += (int) $detalle->monto_gastado;
                        }
                    }

                    $ingresoItem = (int) $item->ingreso;
                    $usoItem = $ingresoItem > 0 ? ($item->total_gastado / $ingresoItem) * 100 : null;
                @endphp

                <article class="inf-hist">
                    {{-- Cabecera de la tarjeta --}}
                    <div class="inf-hist__top">
                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0 mt-1">
                                <span class="material-symbols-outlined text-[24px]">calendar_month</span>
                            </div>
                            <div>
                                <h3 class="inf-hist__mes font-headline-md text-headline-md capitalize">
                                    {{ \Carbon\Carbon::create($item->anio, $item->mes, 1)->locale('es')->translatedFormat('F Y') }}
                                </h3>
                                <p class="inf-hist__fecha">
                                    Guardado el {{ $item->created_at->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>

                        <span class="chip chip-paid flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            Guardado
                        </span>
                    </div>

                    {{-- Stats de la tarjeta --}}
                    <div class="inf-hist__stats">
                        <div class="inf-hist__stat bg-surface-container-lowest border border-outline-variant/30 p-4 rounded-xl">
                            <p class="inf-hist__stat-et flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-primary">payments</span>
                                Ingreso registrado
                            </p>
                            <p class="inf-hist__stat-val">${{ number_format($item->ingreso, 0, ',', '.') }}</p>
                        </div>

                        <div class="inf-hist__stat bg-surface-container-lowest border border-outline-variant/30 p-4 rounded-xl">
                            <p class="inf-hist__stat-et flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-error">receipt_long</span>
                                Total gastado
                            </p>
                            <p class="inf-hist__stat-val">${{ number_format($item->total_gastado, 0, ',', '.') }}</p>
                        </div>

                        <div class="inf-hist__stat bg-surface-container-lowest border border-outline-variant/30 p-4 rounded-xl">
                            <p class="inf-hist__stat-et flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-tertiary">percent</span>
                                Ingreso usado
                            </p>
                            <p class="inf-hist__stat-val">
                                {{ $usoItem === null ? '—' : number_format($usoItem, 1, ',', '.') . '%' }}
                            </p>
                        </div>
                    </div>

                    {{-- Distribución --}}
                    <div>
                        <h4 class="font-headline-sm text-on-surface mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-on-surface-variant">pie_chart</span>
                            Distribución registrada
                        </h4>

                        <div class="inf-bloques">
                            @foreach ($bloquesMostrar as $clave => $bloque)
                                @php
                                    $monto      = $totalesBloque[$clave];
                                    $porcentaje = $ingresoItem > 0 ? ($monto / $ingresoItem) * 100 : null;
                                    $excede     = $porcentaje !== null && $porcentaje > $bloque['recomendado'];
                                    $bajoAhorro = $clave === 'ahorro' && $porcentaje !== null && $porcentaje < 20;

                                    if ($porcentaje === null) {
                                        $claseEstado = 'chip-pending';
                                        $textoEstado = 'No se puede calcular sin un ingreso registrado.';
                                        $chipEstado  = 'Sin calcular';
                                    } elseif ($excede) {
                                        $claseEstado = 'chip-late';
                                        $textoEstado = 'Supera el porcentaje recomendado.';
                                        $chipEstado  = 'Supera lo recomendado';
                                    } elseif ($bajoAhorro) {
                                        $claseEstado = 'chip-soon';
                                        $textoEstado = 'Está por debajo del porcentaje recomendado.';
                                        $chipEstado  = 'Por debajo';
                                    } else {
                                        $claseEstado = 'chip-paid';
                                        $textoEstado = 'Dentro del límite recomendado.';
                                        $chipEstado  = 'Dentro del límite';
                                    }
                                @endphp

                                <div class="inf-bloque" style="--bloque: {{ $bloque['color'] }};">
                                    <div class="inf-bloque__top">
                                        <h5 class="inf-bloque__titulo font-headline-sm">{{ $bloque['nombre'] }}</h5>
                                        <span class="chip {{ $claseEstado }}">{{ $chipEstado }}</span>
                                    </div>

                                    <p class="inf-bloque__pct">
                                        {{ $porcentaje === null ? 'No disponible' : number_format($porcentaje, 1, ',', '.') . '%' }}
                                    </p>

                                    @if ($porcentaje !== null)
                                        <div class="inf-barra">
                                            <div class="inf-barra__pista"
                                                 role="img"
                                                 aria-label="{{ $bloque['nombre'] }}: {{ number_format($porcentaje, 1, ',', '.') }}% real, {{ $bloque['recomendado'] }}% recomendado.">
                                                <div class="inf-barra__fill"
                                                     style="width: {{ min(100, max(0, $porcentaje)) }}%; background: {{ $bloque['color'] }};"></div>
                                            </div>
                                            <span class="inf-barra__meta" aria-hidden="true"
                                                  style="left: {{ $bloque['recomendado'] }}%;"></span>
                                        </div>
                                    @endif

                                    <p class="inf-bloque__datos">
                                        <span>Recomendado: <strong>{{ $bloque['recomendado'] }}%</strong></span>
                                        <span>Gastado: <strong>${{ number_format($monto, 0, ',', '.') }}</strong></span>
                                    </p>

                                    <p class="inf-bloque__mensaje">{{ $textoEstado }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Botón de acción --}}
                    <div class="pt-4 border-t border-outline-variant/30 flex justify-end">
                        <a href="{{ route('informes.ver', $item->id) }}" class="btn btn-primary inline-flex items-center gap-2">
                            Ver informe guardado
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                    </div>
                </article>
            @empty
                <div class="inf-vacio" role="status">
                    <div class="w-20 h-20 mx-auto rounded-full bg-surface-container flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-[40px] text-on-surface-variant" aria-hidden="true">folder_open</span>
                    </div>

                    <h3 class="font-headline-md text-on-surface mt-2">
                        {{ $filtrosActivos ? 'No hay informes con esos filtros' : 'Aún no hay informes anteriores' }}
                    </h3>

                    <p class="font-body-md text-on-surface-variant mt-2 max-w-md mx-auto">
                        {{ $filtrosActivos
                            ? 'Prueba con otro año o mes, o limpia los filtros para ver todos tus informes.'
                            : 'Cuando guardes tu primer informe mensual, aparecerá aquí.' }}
                    </p>

                    @if ($filtrosActivos)
                        <a href="{{ route('informes.historial') }}" class="btn btn-secondary inline-flex items-center gap-2 mt-4">
                            <span class="material-symbols-outlined text-[18px]">filter_list_off</span>
                            Limpiar filtros
                        </a>
                    @else
                        <a href="{{ route('informes.actual') }}" class="btn btn-primary inline-flex items-center gap-2 mt-4">
                            <span class="material-symbols-outlined text-[18px]">add_chart</span>
                            Ir al informe mensual
                        </a>
                    @endif
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