@extends('layouts.app')

@section('titulo', 'Informe 50/30/20 · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/informes-50-30-20.css') }}">
@endpush

@php
    $resumen = $resumen ?? [];

    $ingreso = (int) ($resumen['ingreso'] ?? 0);
    $totalGastado = (int) ($resumen['total_gastado'] ?? 0);
    $saldo = (int) ($resumen['saldo_disponible'] ?? 0);
    $usoDelIngreso = $ingreso > 0 ? min(100, round(($totalGastado / $ingreso) * 100, 1)) : null;

    $mesActual = $mesSeleccionado ?? now()->format('Y-m');
    $etiquetaMes = rescue(
        fn() => \Carbon\Carbon::createFromFormat('!Y-m', $mesActual)->locale('es')->translatedFormat('F Y'),
        null,
        false
    );

    $colorBloque = fn($bloque) => match ($bloque) {
        'necesidades' => 'var(--need)',
        'discrecionales', 'deseos' => 'var(--want)',
        'ahorro' => 'var(--save)',
        default => 'var(--primary)',
    };

    $detalleHormiga = collect($gastosHormiga['detalle'] ?? []);
    $hayGastosHormiga = !empty($gastosHormiga)
        && $detalleHormiga->isNotEmpty()
        && (int) ($gastosHormiga['total'] ?? 0) > 0;
@endphp

@section('content')
    <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
        <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

            {{-- CABECERA con botones de descarga a la derecha --}}
            <header class="inf-cabecera">
                <div>
                    <h1 class="font-headline-xl text-headline-xl text-on-surface">
                        Informe 50/30/20
                    </h1>

                    <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                        {{ !empty($esHistorico) ? 'Consulta de un informe guardado' : 'Cómo se distribuyó tu gasto' }}
                    </p>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    @if ($etiquetaMes)
                        <span class="inf-mes">
                            <span class="material-symbols-outlined" aria-hidden="true">calendar_month</span>
                            {{ $etiquetaMes }}
                        </span>
                    @endif
                    @if (!empty($esHistorico) || !empty($informeDisponible))

                        @php
                            $csvUrl = !empty($esHistorico)
                                ? route('informes.historico.csv', request()->route('id'))
                                : route('informes.exportar.csv', ['mes' => $mesSeleccionado]);

                            $wordUrl = !empty($esHistorico)
                                ? route('informes.historico.word', request()->route('id'))
                                : route('informes.exportar.word', ['mes' => $mesSeleccionado]);
                        @endphp

                        <div class="flex items-center gap-3" aria-label="Descargas del informe">
                            {{-- Botón CSV --}}
                            <a href="{{ $csvUrl }}" class="btn btn-secondary p-3 transition-colors"
                                title="Descargar informe mensual en CSV" aria-label="Descargar informe mensual en CSV">
                                <svg viewBox="0 0 24 24" fill="none" class="w-7 h-7">
                                    <!-- Papel de fondo y doblez -->
                                    <path d="M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="#E2E8F0" />
                                    <path d="M14 2l6 6h-6V2z" fill="#CBD5E1" />
                                    <line x1="8" y1="7" x2="14" y2="7" stroke="#94A3B8" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <line x1="8" y1="10" x2="16" y2="10" stroke="#94A3B8" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <!-- Cinta inferior CSV (Verde) -->
                                    <path d="M3 13h18v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7z" fill="#10B981" />
                                    <path d="M3 13v7a2 2 0 0 1-2-2v-5a2 2 0 0 1 2 2z" fill="#059669" />
                                    <text x="12" y="18.5" font-size="6" fill="#ffffff" font-weight="900"
                                        font-family="system-ui, -apple-system, sans-serif" text-anchor="middle">CSV</text>
                                </svg>
                            </a>

                            {{-- Botón Word (DOC) --}}
                            <a href="{{ $wordUrl }}" class="btn btn-secondary p-3 transition-colors"
                                title="Descargar informe mensual en Word" aria-label="Descargar informe mensual en Word">
                                <svg viewBox="0 0 24 24" fill="none" class="w-7 h-7">
                                    <!-- Papel de fondo y doblez -->
                                    <path d="M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="#E2E8F0" />
                                    <path d="M14 2l6 6h-6V2z" fill="#CBD5E1" />
                                    <line x1="8" y1="7" x2="14" y2="7" stroke="#94A3B8" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <line x1="8" y1="10" x2="16" y2="10" stroke="#94A3B8" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <!-- Cinta inferior Word (Azul) -->
                                    <path d="M3 13h18v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7z" fill="#2563EB" />
                                    <path d="M3 13v7a2 2 0 0 1-2-2v-5a2 2 0 0 1 2 2z" fill="#1D4ED8" />
                                    <text x="12" y="18.5" font-size="6" fill="#ffffff" font-weight="900"
                                        font-family="system-ui, -apple-system, sans-serif" text-anchor="middle">DOC</text>
                                </svg>
                            </a>

                            {{-- Botón PDF --}}
                            <button type="button" class="btn btn-secondary p-3 transition-colors" onclick="window.print()"
                                title="Descargar informe mensual en PDF" aria-label="Descargar informe mensual en PDF">
                                <svg viewBox="0 0 24 24" fill="none" class="w-7 h-7">
                                    <!-- Papel de fondo y doblez -->
                                    <path d="M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="#E2E8F0" />
                                    <path d="M14 2l6 6h-6V2z" fill="#CBD5E1" />
                                    <line x1="8" y1="7" x2="14" y2="7" stroke="#94A3B8" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <line x1="8" y1="10" x2="16" y2="10" stroke="#94A3B8" stroke-width="1.5"
                                        stroke-linecap="round" />
                                    <!-- Cinta inferior PDF (Rojo) -->
                                    <path d="M3 13h18v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7z" fill="#EF4444" />
                                    <path d="M3 13v7a2 2 0 0 1-2-2v-5a2 2 0 0 1 2 2z" fill="#DC2626" />
                                    <text x="12" y="18.5" font-size="6" fill="#ffffff" font-weight="900"
                                        font-family="system-ui, -apple-system, sans-serif" text-anchor="middle">PDF</text>
                                </svg>
                            </button>
                        </div>


                    @endif
                </div>
            </header>

            @if (session('error'))
                <div role="alert" class="inf-aviso">
                    <span class="material-symbols-outlined" aria-hidden="true">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (!empty($esHistorico))
                <p class="inf-aviso">
                    <span class="material-symbols-outlined" aria-hidden="true">photo_camera</span>
                    <span>
                        Este informe es una fotografía guardada.
                        No cambia cuando modificas tus gastos actuales.
                        @if (!empty($fechaGuardado))
                            Guardado el {{ $fechaGuardado->format('d/m/Y H:i') }}.
                        @endif
                    </span>
                </p>
            @endif

            <section class="card p-space-lg" data-tour="inf-selector">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                    Selecciona el mes
                </h2>

                <form action="{{ route('informes.actual') }}" method="GET" class="inf-selector">
                    <div class="inf-selector__campo">
                        <label for="mes">Mes del informe</label>
                        <input type="month" id="mes" name="mes" value="{{ $mesActual }}" required
                            class="border rounded-lg p-2" {{ !empty($esHistorico) ? 'disabled' : '' }}>
                    </div>

                    @if (empty($esHistorico))
                        <button type="submit" class="btn btn-primary">
                            Consultar mes
                        </button>
                    @endif

                    {{-- Botón de historial --}}
                    <div class="flex justify-end mt-space-lg ml-auto">
                        <a href="{{ route('informes.historial') }}" class="btn btn-secondary inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">history</span>
                            Consultar informes anteriores
                        </a>
                    </div>
                </form>

            </section>

            @if (!empty($esHistorico) || !empty($informeDisponible))

                @if (empty($resumen['porcentajes_disponibles']))
                    <section class="card p-space-lg" role="status">
                        <h2 class="font-headline-md text-headline-md text-on-surface">
                            Registra tu ingreso mensual
                        </h2>
                        <p class="mt-2 text-on-surface-variant">
                            {{ $resumen['mensaje_general'] ?? 'Necesitas registrar tu ingreso para calcular los porcentajes.' }}
                        </p>
                        <a href="{{ route('ajustes.perfil') }}" class="btn btn-secondary mt-3 inline-block">
                            Ir a Ajustes
                        </a>
                    </section>
                @elseif (!empty($resumen['sin_gastos']))
                    <section class="card p-space-lg" role="status">
                        <h2 class="font-headline-md text-headline-md text-on-surface">
                            Aún no tienes gastos en este mes
                        </h2>
                        <p class="mt-2 text-on-surface-variant">
                            Puedes registrar tus gastos para consultar cómo se distribuye tu dinero.
                        </p>
                    </section>
                @endif

                {{-- RESUMEN --}}
                <section class="card p-space-lg" data-tour="inf-resumen">
                    <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                        Resumen del mes
                    </h2>

                    <div class="inf-resumen">
                        <div class="inf-stat">
                            <span class="inf-stat__icono material-symbols-outlined" aria-hidden="true">payments</span>
                            <div>
                                <p class="inf-stat__etiqueta">Ingreso mensual</p>
                                <p class="inf-stat__valor">${{ number_format($ingreso, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="inf-stat">
                            <span class="inf-stat__icono material-symbols-outlined" aria-hidden="true">receipt_long</span>
                            <div>
                                <p class="inf-stat__etiqueta">Total gastado</p>
                                <p class="inf-stat__valor">${{ number_format($totalGastado, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div class="inf-stat {{ $saldo < 0 ? 'inf-stat--negativo' : '' }}">
                            <span class="inf-stat__icono material-symbols-outlined"
                                aria-hidden="true">account_balance_wallet</span>
                            <div>
                                <p class="inf-stat__etiqueta">Saldo disponible</p>
                                <p class="inf-stat__valor">
                                    {{ $saldo < 0 ? '-' : '' }}${{ number_format(abs($saldo), 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($usoDelIngreso !== null)
                        <div class="inf-uso">
                            <div class="inf-uso__texto">
                                <span>Has usado <strong>{{ number_format($usoDelIngreso, 1, ',', '.') }}%</strong> de tu
                                    ingreso</span>
                                <span>${{ number_format($totalGastado, 0, ',', '.') }} de
                                    ${{ number_format($ingreso, 0, ',', '.') }}</span>
                            </div>
                            <div class="inf-barra">
                                <div class="inf-barra__pista" role="img"
                                    aria-label="Has usado {{ number_format($usoDelIngreso, 1, ',', '.') }}% de tu ingreso mensual">
                                    <div class="inf-barra__fill"
                                        style="width: {{ $usoDelIngreso }}%; background: {{ $usoDelIngreso >= 100 ? 'var(--error)' : 'var(--primary)' }};">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </section>

                {{-- DISTRIBUCIÓN REAL VS RECOMENDADA --}}
                <section class="card p-space-lg" data-dinamico="informe" data-tour="inf-distribucion">
                    <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                        Distribución real vs recomendada
                    </h2>

                    <div class="inf-leyenda" aria-label="Leyenda de la gráfica">
                        <span class="inf-leyenda__item">
                            <span class="inf-punto" aria-hidden="true" style="background: var(--primary);"></span>
                            Porcentaje real
                        </span>
                        <span class="inf-leyenda__item">
                            <span aria-hidden="true"
                                style="display:inline-block; width:3px; height:16px; border-radius:2px; background:var(--on-surface);"></span>
                            Porcentaje recomendado
                        </span>
                    </div>

                    <div class="inf-bloques">
                        @forelse (($informe ?? []) as $clave => $item)
                            @php
                                $estado = $item['estado'] ?? 'sin_calcular';
                                $claseEstado = match ($estado) {
                                    'excede' => 'chip-late',
                                    'dentro' => 'chip-paid',
                                    default => 'chip-pending',
                                };
                                $textoEstado = match ($estado) {
                                    'excede' => 'Excede lo recomendado',
                                    'dentro' => 'Dentro de lo recomendado',
                                    default => 'Sin calcular',
                                };
                                $pctReal = $item['porcentaje_real'];
                                $pctRec = $item['porcentaje_recomendado'];
                            @endphp

                            <article class="inf-bloque" style="--bloque: {{ $colorBloque($clave) }};">
                                <header class="inf-bloque__top">
                                    <h3 class="inf-bloque__titulo font-headline-sm">{{ $item['nombre'] }}</h3>
                                </header>

                                <p class="inf-bloque__pct">
                                    {{ $pctReal === null ? 'No disponible' : number_format($pctReal, 1, ',', '.') . '%' }}
                                    <small>de tu ingreso</small>
                                </p>

                                @if ($pctReal !== null)
                                    <div>
                                        <div class="inf-barra">
                                            <div class="inf-barra__pista" role="img"
                                                aria-label="Porcentaje real: {{ number_format($pctReal, 1, ',', '.') }}%. Porcentaje recomendado: {{ $pctRec }}%.">
                                                <div class="inf-barra__fill"
                                                    style="width: {{ min(100, max(0, $pctReal)) }}%; background: {{ $colorBloque($clave) }};">
                                                </div>
                                            </div>
                                            <span class="inf-barra__meta" aria-hidden="true"
                                                style="left: {{ min(100, max(0, $pctRec)) }}%;"></span>
                                        </div>

                                        <p class="inf-bloque__datos" style="margin-top: 10px;">
                                            <span>Recomendado: <strong>{{ $pctRec }}%</strong></span>
                                            <span>
                                                Diferencia:
                                                <strong>{{ $item['diferencia_porcentual'] > 0 ? '+' : '' }}{{ number_format($item['diferencia_porcentual'], 1, ',', '.') }}
                                                    puntos</strong>
                                            </span>
                                        </p>
                                    </div>
                                @else
                                    <p class="inf-bloque__datos">
                                        <span>Recomendado: <strong>{{ $pctRec }}%</strong></span>
                                    </p>
                                @endif

                                <p class="inf-bloque__datos">
                                    <span>Gastado</span>
                                    <strong>${{ number_format($item['monto_real'], 0, ',', '.') }}</strong>
                                </p>

                                <p class="inf-bloque__mensaje">{{ $item['mensaje'] }}</p>
                            </article>
                        @empty
                            <p class="text-on-surface-variant">No hay datos suficientes para generar el informe.</p>
                        @endforelse
                    </div>

                    @if (($resumen['monto_sin_clasificar'] ?? 0) > 0)
                        <div class="inf-nota">
                            <strong>Sin clasificar: ${{ number_format($resumen['monto_sin_clasificar'], 0, ',', '.') }}</strong>
                            <span>
                                Porcentaje del ingreso:
                                {{ $resumen['porcentaje_sin_clasificar'] === null
                        ? 'No disponible'
                        : number_format($resumen['porcentaje_sin_clasificar'], 1, ',', '.') . '%' }}
                            </span>
                        </div>
                    @endif
                </section>

                @php
                    $datosDistribucion = collect($informe ?? [])
                        ->map(fn($item) => [
                            'nombre' => $item['nombre'] ?? '',
                            'monto' => (int) ($item['monto_real'] ?? $item['monto_gastado'] ?? 0),
                        ])
                        ->filter(fn($item) => $item['monto'] > 0)
                        ->values();

                    $categoriasGrafica = collect($detalle ?? [])
                        ->filter(fn($item) => ($item['monto_gastado'] ?? 0) > 0)
                        ->sortByDesc('monto_gastado')
                        ->values();

                    $maximoCategoria = $categoriasGrafica->max('monto_gastado') ?? 0;
                    $totalCategorias = $categoriasGrafica->sum('monto_gastado');

                    $datosCategoriasGrafica = $categoriasGrafica
                        ->map(fn($item) => [
                            'nombre' => $item['categoria_nombre'] ?? 'Sin categoría',
                            'monto' => (int) $item['monto_gastado'],
                            'bloque' => $item['bloque'] ?? 'sin_clasificar',
                        ])
                        ->values();
                @endphp

                {{-- ========================================== --}}
                {{-- SECCIÓN: ANÁLISIS VISUAL --}}
                {{-- ========================================== --}}
                <section class="mt-space-lg">
                    <div class="flex items-center gap-2 mb-space-md">
                        <span class="material-symbols-outlined text-primary text-[24px]">insights</span>
                        <h2 class="font-headline-md text-headline-md text-on-surface">Análisis Visual</h2>
                    </div>

                    <div class="inf-graficas">
                        {{-- GRÁFICA DE DONA --}}
                        <div class="inf-grafica-card card p-space-lg">
                            <div class="inf-grafica-header">
                                <div class="inf-grafica-header__icono text-primary iconos-text-graficas">
                                    <span class="material-symbols-outlined">pie_chart</span>
                                    <h3 class="inf-grafica__titulo">Distribución de tus gastos</h3>
                                </div>
                                <div>
                                    <p class="inf-grafica__texto">Compara cuánto dinero se destinó a cada bloque.</p>
                                </div>
                            </div>

                            <div class="inf-canvas-container">
                                <canvas id="grafica-distribucion" data-chart-data="{{ json_encode($datosDistribucion) }}"
                                    role="img" aria-label="Gráfica de dona de la distribución de gastos por bloque">
                                </canvas>
                            </div>

                            @if ($datosDistribucion->isEmpty())
                                <p class="text-on-surface-variant text-center mt-4">Aún no hay gastos registrados para mostrar esta
                                    gráfica.</p>
                            @endif
                        </div>

                        {{-- GRÁFICA DE CATEGORÍAS (BARRAS) --}}
                        <div class="inf-grafica-card card p-space-lg" data-dinamico="grafica-categorias">
                            <div class="inf-grafica-header">
                                <div class="inf-grafica-header__icono text-tertiary-container iconos-text-graficas">
                                    <span class="material-symbols-outlined">bar_chart</span>
                                    <h3 class="inf-grafica__titulo">¿En qué categorías gastaste más?</h3>
                                </div>
                                <div>
                                    <p class="inf-grafica__texto">Las barras comparan los montos entre categorías del periodo.
                                    </p>
                                </div>
                            </div>

                            @if ($categoriasGrafica->isEmpty())
                                <p class="text-on-surface-variant text-center mt-4">
                                    No hay gastos por categoría para representar en la gráfica.
                                </p>
                            @else
                                <div class="inf-cat-lista">
                                    @foreach ($categoriasGrafica as $categoria)
                                        @php
                                            $montoCategoria = (int) ($categoria['monto_gastado'] ?? 0);
                                            $porcentajeCategoria = $maximoCategoria > 0 ? ($montoCategoria / $maximoCategoria) * 100 : 0;
                                            $participacion = $totalCategorias > 0 ? ($montoCategoria / $totalCategorias) * 100 : 0;
                                            $color = $colorBloque($categoria['bloque'] ?? 'sin_clasificar');
                                        @endphp

                                        <div class="inf-cat"
                                            title="{{ $categoria['categoria_nombre'] ?? 'Sin categoría' }}: ${{ number_format($montoCategoria, 0, ',', '.') }}">
                                            <div class="inf-cat__top">
                                                <span class="inf-cat__nombre">
                                                    <span class="inf-punto" aria-hidden="true" style="background: {{ $color }};"></span>
                                                    {{ $categoria['categoria_nombre'] ?? 'Sin categoría' }}
                                                </span>
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="inf-cat__monto">${{ number_format($montoCategoria, 0, ',', '.') }}</span>
                                                    <span class="inf-cat__participacion" style="color: {{ $color }};">
                                                        {{ number_format($participacion, 1, ',', '.') }}%
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="inf-cat__pista">
                                                <div class="inf-cat__fill" role="progressbar"
                                                    aria-label="Gasto en {{ $categoria['categoria_nombre'] ?? 'Sin categoría' }}"
                                                    aria-valuenow="{{ round($porcentajeCategoria) }}" aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    style="width: {{ $porcentajeCategoria }}%; background: {{ $color }};"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
                {{-- GASTOS HORMIGA --}}
                @if ($hayGastosHormiga)
                    @php
                        $pctHormiga = (float) ($gastosHormiga['porcentaje'] ?? 0);
                        $nivelHormiga = match (true) {
                            $pctHormiga >= 10 => 'Impacto alto',
                            $pctHormiga >= 5 => 'Impacto moderado',
                            default => 'Impacto leve',
                        };
                        $maxHormiga = max(1, (int) $detalleHormiga->max('total'));
                        $criterios = $gastosHormiga['criterios'] ?? null;
                    @endphp

                    <section class="card p-space-lg gastos-hormiga" data-dinamico="gastos-hormiga" aria-labelledby="titulo-hormiga">
                        <div class="gastos-hormiga__encabezado">
                            <div class="gastos-hormiga__icono" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m10.3 3.9-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.7-3.1l-8-14a2 2 0 0 0-3.4 0Z" />
                                    <path d="M12 9v4" />
                                    <path d="M12 17h.01" />
                                </svg>
                            </div>

                            <div class="gastos-hormiga__intro">
                                <p class="gastos-hormiga__etiqueta">¡Atención! Revisa tus hábitos de consumo</p>

                                <h2 id="titulo-hormiga" class="font-headline-md text-headline-md">Gastos hormiga</h2>

                                <p class="mt-1">
                                    Identificamos consumos recurrentes que están teniendo un impacto importante en tu presupuesto.
                                </p>

                                @if ($criterios)
                                    <p class="gastos-hormiga__criterios">
                                        Se consideran compras de hasta ${{ number_format($criterios['monto_maximo'], 0, ',', '.') }}
                                        que se repiten {{ $criterios['frecuencia'] }} o más veces en el mes.
                                    </p>
                                @endif
                            </div>

                            <span class="gastos-hormiga__nivel">{{ $nivelHormiga }}</span>
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
                                <strong>{{ number_format($pctHormiga, 2, ',', '.') }} %</strong>
                            </article>
                        </div>

                        <div class="gastos-hormiga__detalle">
                            <h3 class="font-headline-sm text-on-surface">Hábitos de consumo identificados</h3>

                            <div class="gastos-hormiga__lista">
                                @foreach ($detalleHormiga as $gastoHormiga)
                                    @php
                                        $promedio = $gastoHormiga['promedio']
                                            ?? (int) round($gastoHormiga['total'] / max(1, $gastoHormiga['frecuencia']));
                                    @endphp

                                    <article class="gastos-hormiga__compra">
                                        <div class="gastos-hormiga__compra-info">
                                            <strong>{{ $gastoHormiga['concepto'] }}</strong>
                                            <span>
                                                {{ $gastoHormiga['frecuencia'] }} registros durante el mes
                                                · ${{ number_format($promedio, 0, ',', '.') }} por compra
                                            </span>
                                            <span>
                                                Representa {{ number_format($gastoHormiga['porcentaje'], 2, ',', '.') }} % de tus gastos
                                            </span>
                                            <div class="gastos-hormiga__barra" aria-hidden="true">
                                                <span style="width: {{ round(($gastoHormiga['total'] / $maxHormiga) * 100) }}%;"></span>
                                            </div>
                                        </div>

                                        <strong class="gastos-hormiga__monto">
                                            ${{ number_format($gastoHormiga['total'], 0, ',', '.') }}
                                        </strong>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <div class="gastos-hormiga__recomendacion">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true" style="flex-shrink:0;">
                                <path d="M9 18h6" />
                                <path d="M10 22h4" />
                                <path d="M15.09 14a6 6 0 1 0-6.18 0c.6.45 1.09 1.16 1.09 2h4c0-.84.49-1.55 1.09-2Z" />
                            </svg>

                            <div>
                                <strong>¿Qué puedes hacer?</strong>
                                Revisa cuáles de estos consumos puedes reducir o evitar.
                                Pequeños cambios en tus hábitos pueden ayudarte a liberar dinero durante el mes.
                            </div>
                        </div>
                    </section>
                @endif

                {{-- DETALLE POR CATEGORÍA --}}
                <section class="card p-space-lg" data-dinamico="detalle">
                    <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                        Detalle por categoría
                    </h2>

                    <div class="inf-filas">
                        @forelse (($detalle ?? []) as $item)
                                <article class="inf-fila">
                                    <h3 class="inf-fila__nombre font-headline-sm">
                                        <span class="inf-punto" aria-hidden="true"
                                            style="background: {{ $colorBloque($item['bloque']) }};"></span>
                                        {{ $item['categoria_nombre'] }}
                                    </h3>

                                    <strong class="inf-fila__monto">
                                        ${{ number_format($item['monto_gastado'], 0, ',', '.') }}
                                    </strong>

                                    <p class="inf-fila__sub">
                                        Bloque: {{ $item['bloque'] === 'sin_clasificar' ? 'Sin clasificar' : ucfirst($item['bloque']) }}
                                        · Porcentaje del ingreso:
                                        {{ $item['porcentaje_real'] === null
                            ? 'No disponible'
                            : number_format($item['porcentaje_real'], 1, ',', '.') . '%' }}
                                    </p>
                                </article>
                        @empty
                            <p class="text-on-surface-variant">Sin categorías en el periodo.</p>
                        @endforelse
                    </div>
                </section>

                {{-- GUARDAR --}}
                @if (empty($esHistorico) && !empty($resumen['porcentajes_disponibles']))
                    <section class="card p-space-lg">
                        @if (empty($informeGuardado))
                            <h2 class="font-headline-md text-headline-md text-on-surface">
                                Guardar informe mensual
                            </h2>
                            <p class="mt-2 text-on-surface-variant">
                                Conserva el resumen financiero de este mes para revisarlo cuando lo necesites.
                            </p>
                            <form action="{{ route('informes.guardar') }}" method="POST" class="mt-4">
                                @csrf
                                <input type="hidden" name="mes" value="{{ $mesSeleccionado }}">
                                <button type="submit" class="btn btn-primary">
                                    Guardar informe
                                </button>
                            </form>
                        @else
                            <p class="inf-guardado" role="status">
                                <span class="material-symbols-outlined" aria-hidden="true">check_circle</span>
                                El informe de este mes ya está guardado y disponible en tu historial
                            </p>
                        @endif
                    </section>
                @endif

            @else
                <section class="card p-space-lg" role="status">
                    <h2 class="font-headline-md text-headline-md text-on-surface">
                        Informe aún no disponible
                    </h2>

                    <p class="mt-2 text-on-surface-variant">
                        El informe del mes seleccionado todavía no está disponible.
                        Podrás consultarlo y guardarlo cuando termine el mes.
                    </p>
                </section>
            @endif
        </div>
    </main>

    {{-- ONBOARDING TOUR --}}
    <x-onboarding-tour name="informes" :auto-start="true" />
@endsection