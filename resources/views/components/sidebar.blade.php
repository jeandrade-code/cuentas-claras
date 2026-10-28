{{--
  ==============================================================================
  ARCHIVO : resources/views/components/sidebar.blade.php
  QUÉ ES  : Menú lateral del usuario estándar/familiar. Uso: <x-sidebar />
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - auth()->user()->rol
  ESTADO  : Notificaciones y Ajustes se MOVIERON al menú del usuario de la barra superior
            (components/topbar.blade.php); ya no aparecen aquí (2026-10-06, Juan).
  QUÉ HAY QUE HACER (en orden):
    1. Nombrar las rutas exactamente como aparecen aquí (panel, gastos.index, gastos.create,
       gastos-fijos.index, creditos.simulador, metas.index, informes.actual, categorias.index).
    2. "Metas" se dibuja para estandar y familiar (2026-10-08): el estándar puede tener metas
       individuales y recibir invitaciones; el familiar además puede crear compartidas.
       El administrador usa sidebar-admin.blade.php y no ve Metas.
    3. Reemplazar la comprobación de rol por una Gate/Policy cuando exista (ej.
       @can('usar-metas')).
  CÓMO HACERLO / NOTAS:
    - El enlace activo se marca con request()->routeIs('patron.*').
  ==============================================================================
--}}

{{-- Menú lateral de usuario (estándar / familiar). Metas visible para ambos roles. --}}
{{-- TOUR: data-tour="menu-lateral" permite que el onboarding resalte este bloque. --}}
<aside data-tour="menu-lateral" class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between py-space-lg px-space-md">
  <div class="flex flex-col gap-space-lg">
    <div class="flex items-center gap-space-sm px-space-sm">
    <x-logo :alto="36" />
    </div>
    <nav
      class="flex flex-col gap-1"
      data-active-classes="bg-primary-container text-on-primary-container font-semibold rounded-lg shadow-sm"
    >
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('panel') ? ' nav-active' : '' }}"
        href="{{ route('panel') }}"
      >Dashboard Principal</a>
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('gastos.*') ? ' nav-active' : '' }}"
        href="{{ route('gastos.index') }}"
      >Gastos y Transacciones</a>
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('gastos-fijos.*') ? ' nav-active' : '' }}"
        href="{{ route('gastos-fijos.index') }}"
      >Gastos Fijos y Vencimientos</a>
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('creditos.*') ? ' nav-active' : '' }}"
        href="{{ route('creditos.simulador') }}"
      >Simulador de Créditos</a>
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('metas.*') ? ' nav-active' : '' }}"
        href="{{ route('metas.index') }}"
      >Metas</a>
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('informes.*') ? ' nav-active' : '' }}"
        href="{{ route('informes.actual') }}"
      >Informes 50/30/20</a>
      <a
        class="flex items-center justify-between px-space-md py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors font-body-md text-body-md{{ request()->routeIs('categorias.*') ? ' nav-active' : '' }}"
        href="{{ route('categorias.index') }}"
      >Categorías</a>
    </nav>
  </div>
  <div class="p-space-md rounded-xl bg-surface-container-low flex flex-col gap-space-xs">
    <div class="flex items-center gap-space-xs text-primary">
      <span class="material-symbols-outlined text-[18px]">verified_user</span>
      <span class="font-label-md text-label-md">Finanzas Claras</span>
    </div>
    <p class="font-body-sm text-body-sm text-on-surface-variant">
      Organiza tu hogar sin deudas innecesarias y con total privacidad.
    </p>
  </div>
</aside>