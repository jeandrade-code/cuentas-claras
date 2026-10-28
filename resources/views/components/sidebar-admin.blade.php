{{--
==============================================================================
ARCHIVO : resources/views/components/sidebar-admin.blade.php
QUÉ ES : Menú lateral del administrador. Uso: <x-sidebar-admin />
ACTUALIZADO 2026-10-22:
- Añadido ítem "Auditoría" (admin.auditoria).
ACTUALIZADO (tour): data-tour="sidebar-admin" para el onboarding del admin.
==============================================================================
--}}

{{-- TOUR: data-tour="sidebar-admin" permite que el onboarding resalte este bloque. --}}
<aside data-tour="sidebar-admin"
  class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between py-space-lg px-space-md">
  <div class="flex flex-col gap-space-lg">

    <div class="flex items-center gap-space-sm px-space-sm">
      <x-logo :alto="36" />
    </div>

    <nav class="flex flex-col gap-1"
      data-active-classes="bg-primary-container text-on-primary-container font-semibold rounded-lg shadow-sm">
      <a href="{{ route('admin.panel') }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors {{ request()->routeIs('admin.panel') ? 'nav-active' : '' }}">
        <span class="material-symbols-outlined text-[20px]">dashboard</span>
        <span class="font-label-md text-label-md">Panel</span>
      </a>

      <a class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('admin.usuarios*') ? ' nav-active' : '' }}"
        href="{{ route('admin.usuarios') }}">Usuarios y Roles</a>

      <a class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('admin.categorias*') ? ' nav-active' : '' }}"
        href="{{ route('admin.categorias') }}">Categorías del Sistema</a>

      <a class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('admin.estadisticas*') ? ' nav-active' : '' }}"
        href="{{ route('admin.estadisticas') }}">Estadísticas</a>

      <a class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('admin.auditoria*') ? ' nav-active' : '' }}"
        href="{{ route('admin.auditoria') }}">Auditoría</a>

      <div class="my-2 border-t border-outline-variant/30"></div>

    </nav>
  </div>

  <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
    <div class="flex items-center gap-space-xs text-primary">
      <span class="material-symbols-outlined text-[18px]">verified_user</span>
      <span class="font-label-md text-label-md">Panel admin</span>
    </div>
    <p class="font-body-sm text-body-sm text-on-surface-variant">
      Solo conteos agregados. Nunca datos financieros individuales.
    </p>
  </div>
</aside>