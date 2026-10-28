{{--
==============================================================================
ARCHIVO : resources/views/admin/panel.blade.php
QUÉ ES  : Dashboard principal del administrador (admin.panel).
RUTA    : GET /admin/panel
LAYOUT  : layouts.admin
REGLA   : solo COUNT y agregados. NUNCA datos financieros de usuarios.

NOTA SOBRE style="" inline:
  Se usa únicamente para los anchos dinámicos de la barra de distribución
  (<div class="savings" style="width: X%">). El resto usa clases del sistema.
  Si el equipo prefiere cero inline, se puede mover a un data-attribute +
  JS, pero eso implica tocar theme.js (de Juan).

TOUR:
  data-tour="kpis", "distribucion-roles", "ultimos-usuarios", "ultimas-categorias"
  + <x-onboarding-tour name="admin-panel" /> al final del contenido.
==============================================================================
--}}
@extends('layouts.admin')

@section('titulo', 'Panel · Cuentas Claras')

@section('content')
  <div class="max-w-7xl mx-auto px-margin flex flex-col gap-6">

    {{-- Encabezado --}}
    <header class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
      <div class="flex flex-col gap-1">
        <h1 class="font-headline-xl text-headline-xl text-on-surface font-bold">Panel de administración</h1>
        <p class="font-body-md text-body-md text-secondary">
          Resumen del sistema. Solo datos agregados: nunca verás montos ni información financiera de nadie.
        </p>
      </div>
    </header>

    {{-- KPIs  ·  TOUR: data-tour="kpis" --}}
    <section data-tour="kpis" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" aria-label="Indicadores generales">
      <article class="card p-5 flex flex-col gap-1">
        <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Usuarios totales</span>
        <span class="font-numeric-lg text-numeric-lg text-on-surface font-bold tnum">{{ number_format($totalUsuarios, 0, ',', '.') }}</span>
        <span class="font-label-sm text-label-sm text-secondary">todos los roles</span>
      </article>

      <article class="card p-5 flex flex-col gap-1">
        <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Activos</span>
        <span class="font-numeric-lg text-numeric-lg cat-savings font-bold tnum inline-flex items-center gap-2">
          {{ number_format($usuariosActivos, 0, ',', '.') }}
        </span>
        <span class="font-label-sm text-label-sm text-secondary">con acceso</span>
      </article>

      <article class="card p-5 flex flex-col gap-1">
        <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Suspendidos</span>
        <span class="font-numeric-lg text-numeric-lg cat-wants font-bold tnum inline-flex items-center gap-2">
          {{ number_format($usuariosSuspendidos, 0, ',', '.') }}
        </span>
        <span class="font-label-sm text-label-sm text-secondary">sin acceso</span>
      </article>

      <article class="card p-5 flex flex-col gap-1">
        <span class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">Categorías del sistema</span>
        <span class="font-numeric-lg text-numeric-lg text-on-surface font-bold tnum">{{ number_format($totalCategoriasSistema, 0, ',', '.') }}</span>
        <span class="font-label-sm text-label-sm text-secondary">
          {{ $categoriasSistemaActivas }} activas · {{ $categoriasSistemaInactivas }} inactivas
        </span>
      </article>
    </section>

    {{-- Distribución por rol + recordatorios --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6" aria-label="Distribución y recordatorios">

      {{-- TOUR: data-tour="distribucion-roles" --}}
      <article data-tour="distribucion-roles" class="card p-6 flex flex-col gap-4 lg:col-span-2">
        <div class="flex items-center justify-between gap-2">
          <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Distribución por rol</h2>
          <span class="font-label-sm text-label-sm text-secondary tnum">
            {{ $totalUsuarios }} {{ $totalUsuarios === 1 ? 'usuario' : 'usuarios' }}
          </span>
        </div>

        @php
          $base      = max($totalUsuarios, 1);
          $pctEst    = round(($porRol['estandar'] / $base) * 100);
          $pctFam    = round(($porRol['familiar'] / $base) * 100);
          $pctAdmin  = max(100 - $pctEst - $pctFam, 0);
        @endphp

        <div class="budget-bar" role="img"
             aria-label="Distribución de usuarios: estándar {{ $porRol['estandar'] }}, familiar {{ $porRol['familiar'] }}, administrador {{ $porRol['administrador'] }}">
          <div class="savings" style="width: {{ $pctEst }}%"></div>
          <div class="wants"   style="width: {{ $pctFam }}%"></div>
          <div class="needs"   style="width: {{ $pctAdmin }}%"></div>
        </div>

        <ul class="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-1">
          <li class="flex items-center justify-between gap-2">
            <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-md cat-savings font-label-sm text-label-sm font-bold">
              <span class="material-symbols-outlined text-[14px]" aria-hidden="true">person</span>
              Estándar
            </span>
            <span class="font-numeric-md text-numeric-md text-on-surface font-bold tnum">{{ $porRol['estandar'] }}</span>
          </li>
          <li class="flex items-center justify-between gap-2">
            <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-md cat-wants font-label-sm text-label-sm font-bold">
              <span class="material-symbols-outlined text-[14px]" aria-hidden="true">group</span>
              Familiar
            </span>
            <span class="font-numeric-md text-numeric-md text-on-surface font-bold tnum">{{ $porRol['familiar'] }}</span>
          </li>
          <li class="flex items-center justify-between gap-2">
            <span class="inline-flex items-center gap-2 px-2 py-0.5 rounded-md cat-needs font-label-sm text-label-sm font-bold">
              <span class="material-symbols-outlined text-[14px]" aria-hidden="true">shield_person</span>
              Administrador
            </span>
            <span class="font-numeric-md text-numeric-md text-on-surface font-bold tnum">{{ $porRol['administrador'] }}</span>
          </li>
        </ul>
      </article>

      <aside class="card p-6 flex flex-col gap-3" aria-label="Recordatorios de reglas">
        <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Recordatorios</h2>
        <ul class="flex flex-col gap-2 font-body-sm text-body-sm text-secondary">
          <li class="flex gap-2">
            <span class="material-symbols-outlined text-primary text-[18px] shrink-0" aria-hidden="true">info</span>
            <span>No puedes eliminar al último administrador activo.</span>
          </li>
          <li class="flex gap-2">
            <span class="material-symbols-outlined text-primary text-[18px] shrink-0" aria-hidden="true">info</span>
            <span>Las categorías del sistema se desactivan, no se borran.</span>
          </li>
          <li class="flex gap-2">
            <span class="material-symbols-outlined text-primary text-[18px] shrink-0" aria-hidden="true">info</span>
            <span>No puedes eliminarte a ti mismo desde Usuarios.</span>
          </li>
          <li class="flex gap-2">
            <span class="material-symbols-outlined text-primary text-[18px] shrink-0" aria-hidden="true">info</span>
            <span>Estadísticas solo muestra COUNT y agregados.</span>
          </li>
        </ul>
      </aside>

    </section>

    {{-- Últimos usuarios registrados  ·  TOUR: data-tour="ultimos-usuarios" --}}
    <section data-tour="ultimos-usuarios" class="card overflow-hidden" aria-label="Últimos usuarios registrados">
      <header class="p-5 border-b border-outline-variant/30 flex items-center justify-between gap-2">
        <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Últimos usuarios registrados</h2>
        <a href="{{ route('admin.usuarios') }}" class="font-label-md text-label-md text-primary font-bold">Ver todos</a>
      </header>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-surface-container-low">
            <tr>
              <th class="px-5 py-3 text-left font-label-sm text-label-sm text-secondary uppercase tracking-wider">Nombre</th>
              <th class="px-5 py-3 text-left font-label-sm text-label-sm text-secondary uppercase tracking-wider">Correo</th>
              <th class="px-5 py-3 text-left font-label-sm text-label-sm text-secondary uppercase tracking-wider">Rol</th>
              <th class="px-5 py-3 text-left font-label-sm text-label-sm text-secondary uppercase tracking-wider">Estado</th>
              <th class="px-5 py-3 text-left font-label-sm text-label-sm text-secondary uppercase tracking-wider">Alta</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-outline-variant/20">
            @forelse ($ultimosUsuarios as $u)
              <tr>
                <td class="px-5 py-3 font-body-sm text-body-sm text-on-surface font-medium">{{ $u->nombre }}</td>
                <td class="px-5 py-3 font-body-sm text-body-sm text-secondary">{{ $u->email }}</td>
                <td class="px-5 py-3">
                  @php
                    $chipRol = match ($u->rol) {
                        'administrador' => 'chip-pending',
                        'familiar'      => 'chip-soon',
                        default         => 'chip-paid',
                    };
                  @endphp
                  <span class="chip {{ $chipRol }}">{{ $u->rol }}</span>
                </td>
                <td class="px-5 py-3">
                  @if ($u->activo)
                    <span class="chip chip-paid">Activo</span>
                  @else
                    <span class="chip chip-pending">Suspendido</span>
                  @endif
                </td>
                <td class="px-5 py-3 font-body-sm text-body-sm text-secondary tnum">
                  {{ $u->created_at?->format('Y-m-d') }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-5 py-8 text-center font-body-sm text-body-sm text-secondary">
                  No hay usuarios registrados.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    {{-- Últimas categorías del sistema  ·  TOUR: data-tour="ultimas-categorias" --}}
    <section data-tour="ultimas-categorias" class="card p-6" aria-label="Últimas categorías del sistema">
      <header class="flex items-center justify-between gap-2 mb-4">
        <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Categorías del sistema recientes</h2>
        <a href="{{ route('admin.categorias') }}" class="font-label-md text-label-md text-primary font-bold">Gestionar</a>
      </header>

      @if ($ultimasCategorias->isEmpty())
        <p class="font-body-sm text-body-sm text-secondary">Aún no hay categorías del sistema.</p>
      @else
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          @foreach ($ultimasCategorias as $c)
            <li class="flex items-center gap-3 bg-surface-container-low rounded-xl p-3">
              <span class="categoria-icono--{{ $c->bloque }} flex-shrink-0">
                <x-categoria-icono :categoria="$c" :size="20" />
              </span>
              <div class="flex-1 min-w-0">
                <p class="font-body-md text-body-md text-on-surface font-medium truncate">{{ $c->nombre }}</p>
                <p class="font-label-sm text-label-sm text-secondary capitalize">{{ $c->bloque }}</p>
              </div>
              @if ($c->activa)
                <span class="chip chip-paid">Activa</span>
              @else
                <span class="chip chip-pending">Inactiva</span>
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    </section>

  </div>

  {{-- TOUR: lanza el recorrido guiado del panel admin la primera vez que se visita. --}}
  <x-onboarding-tour name="admin-panel" />
@endsection