{{--
==============================================================================
ARCHIVO : resources/views/app/dashboard.blade.php
QUÉ ES : Dashboard principal con diseño del code.html.
RUTA : GET /panel (nombre: panel) · auth + cuenta.activa + rol:estandar,familiar
DATOS : $metricas, $distribucion, $proximos, $ultimosGastos, $notificaciones,
$metas (solo familiar), $esUsuarioNuevo
==============================================================================
--}}

@extends('layouts.app')
@section('titulo', 'Dashboard principal · Cuentas Claras')

  @push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-principal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notificaciones.css') }}">
  @endpush

  @section('content')
    <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
      <div class="flex flex-col w-full pb-space-xl">

        {{-- A) Saludo y acciones rápidas --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-md py-space-md mb-space-md">
          <div class="flex flex-col gap-1 min-w-0">
            <div class="flex items-center gap-space-sm flex-wrap">
              <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight break-words">
                Hola, {{ auth()->user()->nombre }}
              </h1>
            </div>

            <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
              <span class="material-symbols-outlined text-[16px]">calendar_today</span>
              <span>{{ now()->locale('es')->translatedFormat('F Y') }} • Período en curso</span>
            </div>
          </div>

          {{-- Botón "Nuevo Gasto": solo en móvil (en escritorio lo muestra el topbar). --}}
          <div class="md:hidden" data-tour="nuevo-gasto-movil">
            <a href="{{ route('gastos.create') }}"
              class="w-full inline-flex items-center justify-center gap-2 px-space-md py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:opacity-95 transition-all active:scale-[0.98]">
              <span class="material-symbols-outlined text-[20px]">add_circle</span>
              <span>Nuevo Gasto</span>
            </a>
          </div>
        </div>

        {{-- B) Tarjeta de bienvenida si el usuario es nuevo.
        Se mantiene OCULTA mientras el tour corre y aparece al terminar. --}}
        @if ($esUsuarioNuevo)
          <section id="welcome-card"
            class="bg-surface-container-lowest rounded-xl p-space-md sm:p-space-lg shadow-sm mb-space-lg"
            role="status" style="display: none;">
            <div class="flex items-start gap-space-md">
              <div class="p-2.5 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[24px]">rocket_launch</span>
              </div>
              <div class="flex-1 min-w-0">
                <h2 class="font-headline-md text-headline-md text-on-surface mb-space-sm">
                  Bienvenido a Cuentas Claras
                </h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-space-md">
                  Para empezar, sigue estos tres pasos:
                </p>
                <ol class="flex flex-col gap-2 font-body-md text-body-md">
                  <li class="flex items-start gap-2">
                    <span
                      class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-on-primary font-label-sm text-label-sm">1</span>
                    <span class="min-w-0">Registrar tu ingreso en
                      <a href="{{ route('ajustes.perfil') }}" class="text-primary underline">Ajustes &gt; Perfil</a>.
                    </span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span
                      class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-on-primary font-label-sm text-label-sm">2</span>
                    <span class="min-w-0">Crear tu primer
                      <a href="{{ route('gastos.create') }}" class="text-primary underline">gasto</a>.
                    </span>
                  </li>
                  <li class="flex items-start gap-2">
                    <span
                      class="shrink-0 inline-flex items-center justify-center w-6 h-6 rounded-full bg-primary text-on-primary font-label-sm text-label-sm">3</span>
                    <span class="min-w-0">Registrar tu primer
                      <a href="{{ route('gastos-fijos.index') }}" class="text-primary underline">gasto fijo</a>.
                    </span>
                  </li>
                </ol>
              </div>
            </div>
          </section>
        @endif

        {{-- C) Métricas KPI · TOUR: data-tour="metricas" --}}
        <div data-tour="metricas" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md mb-space-lg">
          @foreach ($metricas as $item)
            @php
              $esIngreso = $item['clave'] === 'ingreso';
              $esGastos = $item['clave'] === 'gastos';
              $esDisponible = $item['clave'] === 'disponible';
              $esFijos = $item['clave'] === 'fijos_pendientes';

              $icono = match ($item['clave']) {
                'ingreso' => 'payments',
                'gastos' => 'receipt_long',
                'disponible' => 'account_balance_wallet',
                'fijos_pendientes' => 'schedule',
                default => 'analytics',
              };

              $colorIcono = match ($item['clave']) {
                'ingreso' => 'text-primary',
                'gastos' => 'text-secondary',
                'disponible' => 'text-primary',
                'fijos_pendientes' => 'text-error',
                default => 'text-secondary',
              };

              $porcentajeGasto = null;
              $porcentajeDisponible = null;
              if ($esGastos && $metricas[0]['valor'] > 0) {
                $porcentajeGasto = round(($item['valor'] / $metricas[0]['valor']) * 100, 1);
              }
              if ($esDisponible && $metricas[0]['valor'] > 0) {
                $porcentajeDisponible = round(($item['valor'] / $metricas[0]['valor']) * 100, 1);
              }
            @endphp

            <div
              class="bg-surface-container-lowest rounded-xl p-space-md shadow-sm flex flex-col justify-between min-w-0 hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between gap-2 mb-space-xs">
                <span class="font-label-md text-label-md text-secondary">{{ $item['titulo'] }}</span>
                <span class="p-1 rounded-lg bg-surface-container shrink-0 {{ $colorIcono }}">
                  <span class="material-symbols-outlined text-[18px]">{{ $icono }}</span>
                </span>
              </div>

              <div class="flex flex-col gap-1 min-w-0">
                {{-- flex-wrap: si el monto es largo, "COP" baja a la línea siguiente en vez de desbordar. --}}
                <div
                  class="flex flex-wrap items-baseline gap-x-1 font-numeric-lg text-numeric-lg tracking-tight {{ $esFijos ? 'text-error' : 'text-on-surface' }}">
                  @if ($item['es_dinero'])
                    <span>${{ number_format((int) $item['valor'], 0, ',', '.') }}</span>
                    <span class="font-label-sm text-label-sm text-secondary font-normal">COP</span>
                  @else
                    {{ (int) $item['valor'] }}
                  @endif
                </div>

                @if ($esGastos && $porcentajeGasto !== null)
                  <div class="flex items-center gap-1.5 font-label-sm text-label-sm text-secondary mt-1">
                    <div class="w-16 shrink-0 bg-surface-container-high rounded-full h-1.5 overflow-hidden">
                      <div class="bg-secondary h-full rounded-full" style="width: {{ min(100, $porcentajeGasto) }}%"></div>
                    </div>
                    <span>{{ $porcentajeGasto }}% del total mensual</span>
                  </div>
                @elseif ($esDisponible && $porcentajeDisponible !== null)
                  <div class="flex items-center gap-1 font-label-sm text-label-sm text-primary mt-1 font-semibold">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                    <span>{{ $porcentajeDisponible }}% en caja</span>
                  </div>
                @elseif ($esFijos && $item['valor'] > 0)
                  <div class="flex items-center gap-1 font-label-sm text-label-sm text-error mt-1">
                    <span class="material-symbols-outlined text-[16px]">notifications_active</span>
                    <span>Por vencer este mes</span>
                  </div>
                @elseif ($esIngreso)
                  <div class="flex items-center gap-1.5 font-label-sm text-label-sm text-primary mt-1">
                    <span class="material-symbols-outlined text-[16px]">verified</span>
                    <span>Base mensual</span>
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>

        {{-- D) Contenido principal: dos columnas --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">

          {{-- Columna izquierda (8/12) --}}
          <div class="lg:col-span-8 flex flex-col gap-space-lg min-w-0">

            {{-- D.1 Próximos vencimientos · TOUR: data-tour="proximos" --}}
            <div data-tour="proximos" class="bg-surface-container-lowest rounded-xl p-space-md sm:p-space-lg shadow-sm">
              <div class="flex items-start gap-space-md">
                <div
                  class="p-2.5 rounded-xl bg-error-container text-on-error-container flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[24px]">crisis_alert</span>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between flex-wrap gap-2 mb-1">
                    <span class="font-headline-sm text-headline-sm text-on-surface">
                      Vencimientos Próximos
                    </span>
                    <span
                      class="px-2.5 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm font-semibold flex items-center gap-1">
                      <span class="w-1.5 h-1.5 rounded-full bg-error animate-ping"></span>
                      {{ $proximos->count() }} en &lt; 3 días
                    </span>
                  </div>
                  <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">
                    Confirma los pagos próximos para evitar recargos e intereses de mora.
                  </p>

                  <div class="flex flex-col gap-space-sm">
                    @forelse ($proximos as $pago)
                      <div
                        class="flex flex-col sm:flex-row sm:items-center justify-between p-space-md rounded-lg bg-surface-container-low gap-space-sm">
                        <div class="flex items-center gap-space-md min-w-0">
                          <div
                            class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center text-on-surface-variant shrink-0">
                            <span class="material-symbols-outlined text-[20px]">event</span>
                          </div>
                          <div class="min-w-0">
                            <div class="font-headline-sm text-headline-sm text-on-surface truncate">
                              {{ $pago->gastoFijo?->nombre ?? 'Gasto fijo' }}
                            </div>
                            <div class="flex flex-wrap items-center gap-2 mt-0.5">
                              <span
                                class="px-2 py-0.5 rounded-md bg-error-container text-on-error-container font-label-sm text-label-sm font-semibold">
                                Vence el {{ $pago->fecha_vencimiento->format('d/m/Y') }}
                              </span>
                            </div>
                          </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-space-md">
                          <span class="font-numeric-md text-numeric-md text-on-surface font-semibold tracking-tight">
                            ${{ number_format((int) $pago->monto, 0, ',', '.') }}
                          </span>
                          <form method="POST" action="{{ route('gastos-fijos.pagar', $pago->gastoFijo) }}">
                            @csrf
                            <button type="submit"
                              class="px-space-md py-1.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:opacity-95 transition-all active:scale-[0.98] shadow-sm flex items-center gap-1 shrink-0">
                              <span class="material-symbols-outlined text-[16px]">check_circle</span>
                              <span>Pagar</span>
                            </button>
                          </form>
                        </div>
                      </div>
                    @empty
                      <p class="font-body-md text-body-md text-on-surface-variant py-space-md">
                        No tienes vencimientos próximos
                      </p>
                    @endforelse
                  </div>
                </div>
              </div>
            </div>

            {{-- D.2 Gastos recientes · TOUR: data-tour="ultimos" --}}
            <div data-tour="ultimos"
              class="bg-surface-container-lowest rounded-xl p-space-md sm:p-space-lg shadow-sm flex flex-col gap-space-md">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm pb-space-sm">
                <div>
                  <h2 class="font-headline-md text-headline-md text-on-surface">Gastos Recientes</h2>
                  <p class="font-body-sm text-body-sm text-secondary">Registro en tiempo real de tus salidas</p>
                </div>
                <a href="{{ route('gastos.index') }}"
                  class="inline-flex items-center gap-1 text-primary hover:opacity-80 font-label-md text-label-md transition-colors">
                  <span>Ver todos</span>
                  <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
              </div>

              <div class="flex flex-col gap-2">
                @forelse ($ultimosGastos as $gasto)
                  <div
                    class="flex items-center justify-between gap-space-sm p-3 rounded-lg hover:bg-surface-container-low transition-colors group">
                    <div class="flex items-center gap-space-md min-w-0">
                      <div
                        class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                      </div>
                      <div class="min-w-0">
                        <div class="font-headline-sm text-headline-sm text-on-surface truncate">
                          {{ $gasto->descripcion ?: ($gasto->categoria?->nombre ?? 'Gasto') }}
                        </div>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-secondary font-body-sm text-body-sm">
                          <span>{{ $gasto->fecha->format('d/m/Y') }}</span>
                          @if ($gasto->categoria)
                            <span>•</span>
                            <span
                              class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-surface-container-high text-on-surface font-label-sm text-label-sm">
                              {{ $gasto->categoria->nombre }}
                            </span>
                          @endif
                        </div>

                        {{-- Móvil: el monto va debajo de la descripción y la fecha. --}}
                        <div class="sm:hidden mt-1 font-numeric-md text-numeric-md text-error font-semibold tracking-tight">
                          -${{ number_format((int) $gasto->monto, 0, ',', '.') }}
                        </div>
                      </div>
                    </div>

                    {{-- Escritorio/tablet: el monto queda a la derecha. --}}
                    <div class="hidden sm:flex items-center gap-space-md shrink-0">
                      <span class="font-numeric-md text-numeric-md text-error font-semibold tracking-tight">
                        -${{ number_format((int) $gasto->monto, 0, ',', '.') }}
                      </span>
                    </div>
                  </div>
                @empty
                  <p class="font-body-md text-body-md text-on-surface-variant py-space-md">
                    No hay gastos registrados en este periodo
                  </p>
                @endforelse
              </div>
            </div>

          </div>

          {{-- Columna derecha (4/12) --}}
          <div class="lg:col-span-4 flex flex-col gap-space-lg min-w-0">

            {{-- E.1 Regla 50/30/20 · TOUR: data-tour="distribucion" --}}
            <div data-tour="distribucion"
              class="bg-surface-container-lowest rounded-xl p-space-md sm:p-space-lg shadow-sm flex flex-col gap-space-md">
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-0">
                  <span class="material-symbols-outlined text-primary text-[22px] shrink-0">pie_chart</span>
                  <h2 class="font-headline-sm text-headline-sm text-on-surface">Regla 50 / 30 / 20</h2>
                </div>
                <span
                  class="font-label-sm text-label-sm px-2 py-0.5 rounded bg-surface-container text-secondary font-medium shrink-0">
                  {{ now()->locale('es')->translatedFormat('F') }}
                </span>
              </div>

              @php
                $nec = $distribucion['necesidades'] ?? null;
                $dis = $distribucion['discrecionales'] ?? null;
                $aho = $distribucion['ahorro'] ?? null;

                $pNec = $nec['porcentaje_real'] ?? 0;
                $pDis = $dis['porcentaje_real'] ?? 0;
                $pAho = $aho['porcentaje_real'] ?? 0;

                $suma = max(1, (float) $pNec + (float) $pDis + (float) $pAho);
                $wNec = $pNec / $suma * 100;
                $wDis = $pDis / $suma * 100;
                $wAho = $pAho / $suma * 100;
              @endphp

              <div class="flex flex-col gap-1.5">
                <div class="w-full h-3 bg-surface-container rounded-full flex overflow-hidden">
                  <div class="bg-primary h-full" style="width: {{ $wNec }}%" title="Necesidades"></div>
                  <div class="bg-tertiary-container h-full" style="width: {{ $wDis }}%" title="Discrecionales"></div>
                  <div class="bg-secondary-fixed-dim h-full" style="width: {{ $wAho }}%" title="Ahorro"></div>
                </div>
                <div class="flex justify-between gap-1 text-[11px] text-secondary font-medium px-0.5">
                  <span>{{ round($pNec) }}% Nec.</span>
                  <span>{{ round($pDis) }}% Disc.</span>
                  <span>{{ round($pAho) }}% Ahorro</span>
                </div>
              </div>

              <div class="flex flex-col gap-2.5">
                @foreach (['necesidades' => 'primary', 'discrecionales' => 'tertiary-container', 'ahorro' => 'secondary-fixed-dim'] as $clave => $color)
                  @php
                    $bloque = $distribucion[$clave] ?? null;
                    $recomendado = $bloque['porcentaje_recomendado'] ?? 0;
                    $real = $bloque['porcentaje_real'];
                    $excede = $bloque['estado'] === 'excede';
                  @endphp
                  <div class="flex items-center justify-between gap-2 p-2 rounded-lg bg-surface-container-low">
                    <div class="flex items-center gap-2 min-w-0">
                      <span class="w-2.5 h-2.5 rounded-full shrink-0 bg-{{ $color }}"></span>
                      <div class="min-w-0">
                        <div class="font-label-md text-label-md text-on-surface">
                          {{ $bloque['nombre'] ?? $clave }} ({{ $recomendado }}%)
                        </div>
                        <div class="font-body-sm text-body-sm text-secondary">
                          ${{ number_format((int) ($bloque['monto_real'] ?? 0), 0, ',', '.') }} COP
                        </div>
                      </div>
                    </div>
                    @if ($real === null)
                      <span
                        class="shrink-0 px-2 py-0.5 rounded bg-surface-container text-secondary font-label-sm text-label-sm font-semibold">—</span>
                    @elseif ($excede)
                      <span
                        class="shrink-0 px-2 py-0.5 rounded bg-error-container text-on-error-container font-label-sm text-label-sm font-semibold">
                        Excede +{{ number_format(abs($bloque['diferencia_porcentual']), 0) }}%
                      </span>
                    @else
                      <span class="shrink-0 px-2 py-0.5 rounded chip-ok font-label-sm text-label-sm font-semibold">Óptimo</span>
                    @endif
                  </div>
                @endforeach
              </div>
            </div>

            {{-- E.2 Metas (solo familiar) --}}
            @if (auth()->user()->rol === 'familiar')
              <div
                class="bg-surface-container-lowest rounded-xl p-space-md sm:p-space-lg shadow-sm flex flex-col gap-space-md">
                <div class="flex items-center justify-between gap-2">
                  <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">diversity_1</span>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Mis metas</h2>
                  </div>
                  <a href="{{ route('metas.index') }}"
                    class="font-label-sm text-label-sm text-secondary hover:text-primary shrink-0">Ver todas</a>
                </div>

                @forelse ($metas as $meta)
                  <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-sm">
                    <div class="flex items-center justify-between gap-2">
                      <div class="min-w-0">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface truncate">{{ $meta->nombre }}</h3>
                        <p class="font-body-sm text-body-sm text-secondary">
                          {{ $meta->esCompartida() ? 'Compartida' : 'Individual' }}
                        </p>
                      </div>
                      <span class="font-numeric-md text-numeric-md text-primary font-bold shrink-0">
                        {{ $meta->porcentaje() }}%
                      </span>
                    </div>

                    <div class="w-full bg-surface-container-high rounded-full h-2 overflow-hidden my-1">
                      <div class="bg-primary h-full rounded-full" style="width: {{ $meta->porcentaje() }}%"></div>
                    </div>

                    <div
                      class="flex flex-wrap justify-between items-center gap-x-space-sm gap-y-1 font-label-sm text-label-sm text-secondary">
                      <span>Ahorrado: ${{ number_format($meta->totalConjunto(), 0, ',', '.') }}</span>
                      <span class="font-medium text-on-surface">
                        Objetivo: ${{ number_format((int) $meta->monto_objetivo, 0, ',', '.') }}
                      </span>
                    </div>
                  </div>
                @empty
                  <p class="font-body-md text-body-md text-on-surface-variant">
                    Aún no tienes metas activas
                  </p>
                @endforelse

                <a href="{{ route('metas.index') }}"
                  class="w-full py-2.5 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors flex items-center justify-center gap-2">
                  <span class="material-symbols-outlined text-[18px] text-primary">add</span>
                  <span>Ir a metas</span>
                </a>
              </div>
            @endif

            {{-- E.3 Notificaciones recientes · TOUR: data-tour="notificaciones" --}}
            <div data-tour="notificaciones"
              class="bg-surface-container-lowest rounded-xl p-space-md sm:p-space-lg shadow-sm flex flex-col gap-space-md">
              <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary text-[22px]">notifications</span>
                  <h2 class="font-headline-sm text-headline-sm text-on-surface">Notificaciones</h2>
                </div>
                @if ($notificaciones->isNotEmpty())
                  <a href="{{ route('notificaciones.index') }}"
                    class="inline-flex items-center gap-1 shrink-0 font-label-sm text-label-sm text-secondary hover:text-primary transition-colors">
                    <span>Ver todas</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                  </a>
                @endif
              </div>

              <div class="flex flex-col">
                @forelse ($notificaciones as $notif)
                  <a href="{{ route('notificaciones.index') }}"
                    class="notificacion-item notificacion-item--compacta notificacion-item--{{ $notif->tipo }}"
                    aria-label="Abrir notificaciones">
                    @include('app.notificaciones._icono', ['tipo' => $notif->tipo])

                    <div class="notificacion-cuerpo">
                      <p class="font-body-sm text-body-sm text-on-surface">{{ $notif->mensaje }}</p>
                      <p class="font-label-sm text-label-sm text-on-surface-variant mt-1">
                        {{ $notif->created_at->locale('es')->diffForHumans() }}
                      </p>
                    </div>

                    <span class="notificacion-punto" aria-label="No leída" title="No leída">●</span>
                  </a>
                @empty
                  <p class="font-body-sm text-body-sm text-on-surface-variant py-2">
                    No tienes notificaciones pendientes
                  </p>
                @endforelse
              </div>
            </div>

            {{-- E.4 Atajo al simulador --}}
            <div
              class="bg-surface-container-lowest rounded-xl p-space-md sm:p-space-lg shadow-sm flex flex-col gap-space-sm">
              <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-lg bg-surface-container flex items-center justify-center text-primary shrink-0">
                  <span class="material-symbols-outlined text-[20px]">calculate</span>
                </div>
                <div class="min-w-0">
                  <h3 class="font-headline-sm text-headline-sm text-on-surface">Simulador de Créditos</h3>
                  <p class="font-body-sm text-body-sm text-secondary">Evalúa cuotas y sobreendeudamiento</p>
                </div>
              </div>
              <a href="{{ route('creditos.simulador') }}"
                class="mt-space-xs inline-flex items-center justify-between gap-2 p-2.5 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors font-label-md text-label-md">
                <span>Calcular nueva cuota segura</span>
                <span class="material-symbols-outlined text-[18px] shrink-0">chevron_right</span>
              </a>
            </div>

          </div>
        </div>
      </div>
    </main>

    {{-- TOUR: lanza el recorrido guiado la primera vez que se visita el dashboard. --}}
    <x-onboarding-tour name="dashboard" />

  @push('scripts')
    {{-- Muestra la tarjeta de bienvenida cuando el tour termina (o de inmediato si ya se vio). --}}
    <script>
      (function () {
        var card = document.getElementById('welcome-card');
        if (!card) return;

        var yaVioTour = false;
        try {
          yaVioTour = localStorage.getItem('cc-tour:dashboard') === '1';
        } catch (e) { /* localStorage bloqueado: mostrar la tarjeta */ }

        if (yaVioTour) {
          // El tour ya se vio antes: mostrar la tarjeta inmediatamente, sin animación.
          card.style.display = '';
          return;
        }

        // El tour va a correr: esperar a que termine y luego mostrar la tarjeta.
        document.addEventListener('cc-tour:finished', function onTourDone(e) {
          if (e.detail && e.detail.name !== 'dashboard') return;
          card.style.display = '';
          card.classList.add('welcome-card--reveal');
          document.removeEventListener('cc-tour:finished', onTourDone);
        });
      })();
    </script>
  @endpush
@endsection