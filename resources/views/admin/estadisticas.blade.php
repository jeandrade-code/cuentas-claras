@extends('layouts.admin')
@section('titulo', 'Estadísticas generales · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-estadisticas.css') }}">
@endpush

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
  <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

    <header>
      <h1 class="font-headline-xl text-headline-xl text-on-surface">Estadísticas generales</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">
        Indicadores agregados · nunca mostramos datos financieros individuales.
      </p>
    </header>

    {{-- KPIs principales --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md" data-tour="admin-est-kpis">
      <div class="admin-kpi">
        <div class="admin-kpi__icono admin-kpi__icono--azul">
          <span class="material-symbols-outlined">group</span>
        </div>
        <div class="admin-kpi__cuerpo">
          <p class="admin-kpi__label">Total usuarios</p>
          <p class="admin-kpi__valor">{{ number_format($estadisticas['total_usuarios'], 0, ',', '.') }}</p>
        </div>
      </div>

      <div class="admin-kpi">
        <div class="admin-kpi__icono admin-kpi__icono--verde">
          <span class="material-symbols-outlined">check_circle</span>
        </div>
        <div class="admin-kpi__cuerpo">
          <p class="admin-kpi__label">Activos</p>
          <p class="admin-kpi__valor">{{ number_format($estadisticas['por_estado']['activos'], 0, ',', '.') }}</p>
        </div>
      </div>

      <div class="admin-kpi">
        <div class="admin-kpi__icono admin-kpi__icono--rojo">
          <span class="material-symbols-outlined">block</span>
        </div>
        <div class="admin-kpi__cuerpo">
          <p class="admin-kpi__label">Suspendidos</p>
          <p class="admin-kpi__valor">{{ number_format($estadisticas['por_estado']['suspendidos'], 0, ',', '.') }}</p>
        </div>
      </div>

      <div class="admin-kpi">
        <div class="admin-kpi__icono admin-kpi__icono--morado">
          <span class="material-symbols-outlined">notifications</span>
        </div>
        <div class="admin-kpi__cuerpo">
          <p class="admin-kpi__label">Notificaciones enviadas</p>
          <p class="admin-kpi__valor">{{ number_format($estadisticas['notificaciones_totales'], 0, ',', '.') }}</p>
        </div>
      </div>
    </section>

    {{-- Grid de dos columnas: por rol y por tipo --}}
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">

      {{-- Usuarios por rol --}}
      <div class="card p-space-lg" data-tour="admin-est-roles">
        <header class="admin-panel__cabecera">
          <div class="flex items-center gap-space-sm">
            <span class="material-symbols-outlined text-[22px] admin-panel__icono">badge</span>
            <h2 class="font-headline-md text-headline-md text-on-surface">
              Usuarios por rol
            </h2>
          </div>
          <span class="admin-panel__contador">
            {{ $estadisticas['total_usuarios'] }} en total
          </span>
        </header>

        @php
          $totalUsuarios = max(1, $estadisticas['total_usuarios']);
        @endphp

        @forelse ($estadisticas['por_rol'] as $rol => $total)
          @php
            $pct = round($total / $totalUsuarios * 100, 1);
            $colorClass = match ($rol) {
                'administrador' => 'chip-rol-admin',
                'familiar' => 'chip-rol-familiar',
                default => 'chip-rol-estandar',
            };
            $barColor = match ($rol) {
                'administrador' => 'var(--admin, #2563eb)',
                'familiar' => 'var(--tertiary)',
                default => 'var(--primary)',
            };
          @endphp
          <div class="admin-fila">
            <div class="admin-fila__cabecera">
              <span class="chip {{ $colorClass }}">{{ ucfirst($rol) }}</span>
              <span class="admin-fila__valores">
                <strong>{{ number_format($total, 0, ',', '.') }}</strong>
                <span class="text-on-surface-variant">{{ $pct }}%</span>
              </span>
            </div>
            <div class="admin-barra">
              <div class="admin-barra__relleno" style="width: {{ $pct }}%; background: {{ $barColor }}"></div>
            </div>
          </div>
        @empty
          <p class="text-on-surface-variant py-space-md">Sin datos</p>
        @endforelse
      </div>

      {{-- Notificaciones por tipo --}}
      <div class="card p-space-lg" data-tour="admin-est-notif">
        <header class="admin-panel__cabecera">
          <div class="flex items-center gap-space-sm">
            <span class="material-symbols-outlined text-[22px] admin-panel__icono">campaign</span>
            <h2 class="font-headline-md text-headline-md text-on-surface">
              Notificaciones por tipo
            </h2>
          </div>
          <span class="admin-panel__contador">
            {{ $estadisticas['notificaciones_totales'] }} en total
          </span>
        </header>

        @php
          $totalNotif = max(1, $estadisticas['notificaciones_totales']);
        @endphp

        @forelse ($estadisticas['notificaciones_por_tipo'] as $tipo => $total)
          @php
            $pct = round($total / $totalNotif * 100, 1);
          @endphp
          <div class="admin-fila">
            <div class="admin-fila__cabecera">
              <span class="chip chip-pending">{{ $tipo }}</span>
              <span class="admin-fila__valores">
                <strong>{{ number_format($total, 0, ',', '.') }}</strong>
                <span class="text-on-surface-variant">{{ $pct }}%</span>
              </span>
            </div>
            <div class="admin-barra">
              <div class="admin-barra__relleno" style="width: {{ $pct }}%; background: var(--accent)"></div>
            </div>
          </div>
        @empty
          <p class="text-on-surface-variant py-space-md">
            Aún no se han generado notificaciones.
          </p>
        @endforelse
      </div>
    </section>

    {{-- Nota final --}}
    <section class="card p-space-md">
      <p class="font-body-sm text-body-sm text-on-surface-variant">
        <strong class="text-on-surface">Privacidad:</strong>
        estas estadísticas son solo conteos. Ninguna consulta une usuarios con montos, gastos ni metas individuales.
      </p>
    </section>

  </div>
</main>
@endsection