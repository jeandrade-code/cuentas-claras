{{--
  ==============================================================================
  ARCHIVO : resources/views/components/header.blade.php
  QUÉ ES  : Header público fijo (landing, login, registro, recuperar). Uso: <x-header />
  DATOS QUE LLEGAN DEL CONTROLADOR:
    - Ninguno (usa route()).
  ACTUALIZADO 2026-10-14:
    - Se agregó el botón de tema claro/oscuro (.boton-tema, ver public/js/theme.js).
    - Se corrigió la lógica @auth/@guest (la cabecera ya lo pedía desde J1).
    - Se quitó el <div> con el ícono "person" que estaba fuera de contexto (no hacía nada).
  ==============================================================================
--}}

{{-- Header público (landing, login, registro, recuperar). Los enlaces ancla apuntan a secciones de la landing. --}}
<header 
@if (request()->routeIs('inicio')) data-landing-header @endif
class="fixed top-0 left-0 w-full z-50 bg-surface/85 backdrop-blur-xl shadow-[0_1px_8px_rgba(15,23,42,0.04)]">
  <div class="h-20 max-w-7xl mx-auto px-margin flex items-center justify-between gap-space-md">
    <div class="flex items-center gap-space-md">
      <a
        class="flex items-center gap-space-sm"
        data-path="landing-page"
        href="{{ route('inicio') }}"
      ><x-logo :alto="42" /></a>
    </div>
    <nav
      class="hidden lg:flex items-center gap-space-xs"
      data-active-classes="bg-surface-container-high text-on-surface rounded-lg"
    >
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="como-funciona"
        href="{{ route('inicio') }}#como-funciona"
      >¿Cómo funciona?</a>
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="caracteristicas"
        href="{{ route('inicio') }}#caracteristicas"
      >Características</a>
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="simulador"
        href="{{ route('inicio') }}#simulador"
      >Simulador</a>
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="metas-familiares"
        href="{{ route('inicio') }}#metas-familiares"
      >Metas Familiares</a>
      <a
        class="px-space-sm py-space-xs text-on-surface-variant hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-colors"
        data-path="seguridad"
        href="{{ route('inicio') }}#seguridad"
      >Seguridad</a>
    </nav>
    <div class="flex items-center gap-space-sm">

      {{-- Botón de tema claro/oscuro (ver public/js/theme.js y public/css/theme.css) --}}
      <button
        type="button"
        class="boton-tema"
        data-theme-toggle
        aria-label="Cambiar tema"
        title="Cambiar tema"
      >
        <span class="material-symbols-outlined" data-theme-icon aria-hidden="true">dark_mode</span>
      </button>

      @guest
        <a
          class="hidden sm:inline-flex px-space-md py-space-xs rounded-lg text-on-surface hover:bg-surface-container hover:text-on-surface font-label-md text-label-md whitespace-nowrap transition-all"
          data-path="iniciar-sesion"
          href="{{ route('login') }}"
        >Iniciar Sesión</a>
        <a
          class="hidden sm:inline-flex items-center px-space-md py-3 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md whitespace-nowrap transition-all shadow-[0_1px_3px_rgba(15,23,42,0.04)]"
          data-path="crear-cuenta"
          href="{{ route('registro') }}"
        >Crear Cuenta Gratis</a>
      @endguest

      @auth
        @php
          $rutaPanel = auth()->user()->rol === 'administrador'
              ? route('admin.panel')
              : route('panel');
        @endphp
        <a
          class="inline-flex items-center gap-1.5 px-space-md py-space-xs rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md whitespace-nowrap transition-all shadow-[0_1px_3px_rgba(15,23,42,0.04)]"
          data-path="panel"
          href="{{ $rutaPanel }}"
        >
          <span class="material-symbols-outlined text-[18px]">dashboard</span>
          <span>Ir a mi panel</span>
        </a>
      @endauth

      <button
        type="button"
        data-menu-toggle
        aria-label="Abrir menú"
        class="lg:hidden p-space-xs rounded-lg text-on-surface hover:bg-surface-container flex items-center justify-center"
      ><span class="material-symbols-outlined text-[24px]">menu</span></button>
    </div>
  </div>
  <div
    data-menu-panel
    class="hidden lg:hidden flex flex-col gap-space-xs px-margin py-space-md border-t border-outline-variant/30 bg-surface"
  >
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#como-funciona"
    >¿Cómo funciona?</a>
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#caracteristicas"
    >Características</a>
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#simulador"
    >Simulador</a>
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#metas-familiares"
    >Metas Familiares</a>
    <a
      class="px-space-sm py-space-sm rounded-lg text-on-surface-variant hover:bg-surface-container-low font-label-md text-label-md whitespace-nowrap"
      href="{{ route('inicio') }}#seguridad"
    >Seguridad</a>

    {{-- Acciones de sesión también en el panel móvil --}}
    <div class="pt-space-sm mt-space-xs border-t border-outline-variant/30 flex flex-col gap-space-xs">
      @guest
        <a
          class="px-space-sm py-space-sm rounded-lg text-on-surface hover:bg-surface-container-low font-label-md text-label-md"
          href="{{ route('login') }}"
        >Iniciar Sesión</a>
        <a
          class="px-space-sm py-space-sm rounded-lg bg-primary text-on-primary hover:bg-primary-container font-label-md text-label-md text-center"
          href="{{ route('registro') }}"
        >Crear Cuenta Gratis</a>
      @endguest
      @auth
        @php
          $rutaPanel = auth()->user()->rol === 'administrador'
              ? route('admin.panel')
              : route('panel');
        @endphp
        <a
          class="px-space-sm py-space-sm rounded-lg bg-primary text-on-primary hover:bg-primary-container font-label-md text-label-md text-center"
          href="{{ $rutaPanel }}"
        >Ir a mi panel</a>
      @endauth
    </div>
  </div>
</header>