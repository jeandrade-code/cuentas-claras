{{--
==============================================================================
ARCHIVO : resources/views/components/topbar.blade.php
QUÉ ES : Barra superior de las páginas internas. Uso: <x-topbar />
ESTADO : REDISEÑADA (2026-10-06, Juan). Notificaciones, Ajustes y Cerrar sesión viven ahora
dentro del menú del usuario (el avatar de la esquina derecha).
DATOS QUE LLEGAN DEL CONTROLADOR:
- auth()->user()->nombre, ->email y ->rol
- $notificacionesPendientes (entero; lo comparte el View Composer de AppServiceProvider)
CÓMO FUNCIONA EL MENÚ DEL USUARIO:
- Escritorio: se abre al pasar el mouse y también al hacer clic (el clic lo deja fijo).
- Móvil/teclado: se abre con clic o toque; se cierra con clic afuera o con Escape.
- Notificaciones muestra cuántas hay pendientes; el avatar lleva un punto rojo si hay alguna.
- El administrador NO ve "Notificaciones" ni "Nuevo Gasto" (no tiene finanzas personales y
esas rutas le dan 403).
- Cerrar sesión es un formulario POST con @csrf (nunca un enlace GET).
ESTILOS : public/css/menu-usuario.css · SCRIPT: public/js/shell.js (sección 4).
==============================================================================
--}}

@php
  $usuario = auth()->user();
  $esAdministrador = ($usuario->rol ?? '') === 'administrador';
  $pendientes = (int) ($notificacionesPendientes ?? 0);
  $nombreRol = match ($usuario->rol ?? 'estandar') {
    'administrador' => 'Administrador',
    'familiar' => 'Familiar',
    default => 'Estándar',
  };
@endphp

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/menu-usuario.css') }}">
@endpush

{{-- Barra superior: menú móvil, nuevo gasto (no para el administrador) y acciones de cuenta. --}}
<header
  class="fixed top-0 left-72 right-0 h-16 bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 flex items-center justify-between px-margin">
  <div class="flex items-center gap-space-md">
    <button type="button" data-nav-toggle data-tour="menu-movil" aria-label="Abrir menú"
      class="lg:hidden p-space-xs rounded-lg text-on-surface-variant hover:bg-surface-container flex items-center justify-center"><span
        class="material-symbols-outlined text-[24px]">menu</span></button>
  </div>

  <div class="flex items-center gap-3">
    @if(!$esAdministrador && !request()->routeIs('panel'))
        <a class="hidden sm:inline-flex items-center gap-1.5 py-1.5 px-space-md bg-surface-container hover:bg-surface-container-high text-primary rounded-lg font-label-md text-label-md transition-colors"
          href="{{ route('gastos.create') }}">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>Nuevo Gasto</span>
        </a>
    @endif

    {{-- Píldora de acciones: botón de tema + menú del usuario. --}}
    <div class="topbar-acciones">

      {{-- Botón de tema claro/oscuro (ver public/js/theme.js) --}}
      <button type="button" class="boton-tema" data-theme-toggle aria-label="Cambiar tema" title="Cambiar tema">
        <span class="material-symbols-outlined" data-theme-icon aria-hidden="true">dark_mode</span>
      </button>

      {{-- Menú del usuario: el avatar abre Notificaciones, Ajustes y Cerrar sesión. --}}
      <div class="menu-usuario" data-user-menu>
        <button type="button" class="menu-usuario__boton" data-user-menu-button aria-haspopup="menu"
          aria-expanded="false" aria-controls="menu-usuario-panel"
          aria-label="Menú de {{ $usuario->nombre ?? 'usuario' }}">
          <span class="menu-usuario__datos">
            <span class="menu-usuario__nombre font-label-md text-label-md">{{ $usuario->nombre ?? 'Usuario' }}</span>
            <span class="menu-usuario__rol font-label-sm text-label-sm">{{ $nombreRol }}</span>
          </span>

          <span class="relative inline-flex shrink-0">
            <x-user-avatar :user="$usuario" :size="36" />
            @if($pendientes > 0)
              <span class="menu-usuario__punto" aria-hidden="true"></span>
            @endif
          </span>

          <span class="material-symbols-outlined menu-usuario__flecha" aria-hidden="true">expand_more</span>
        </button>

        <div class="menu-usuario__panel" id="menu-usuario-panel" role="menu">
          <div class="menu-usuario__tarjeta">
            <div class="menu-usuario__cabecera">
              <span class="menu-usuario__avatar--grande">
                <x-user-avatar :user="$usuario" :size="48" />
              </span>
              <span class="menu-usuario__identidad">
                <span
                  class="menu-usuario__nombre font-label-md text-label-md">{{ $usuario->nombre ?? 'Usuario' }}</span>
                <span class="menu-usuario__correo font-body-sm text-body-sm">{{ $usuario->email ?? '' }}</span>
                <span class="menu-usuario__etiqueta font-label-sm text-label-sm">{{ $nombreRol }}</span>
              </span>
            </div>

            <div class="menu-usuario__lista">
              @unless($esAdministrador)
                <a class="menu-usuario__item font-body-md text-body-md{{ request()->routeIs('notificaciones.*') ? ' menu-usuario__item--activo' : '' }}"
                  href="{{ route('notificaciones.index') }}" role="menuitem">
                  <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                  <span>Notificaciones</span>
                  @if($pendientes > 0)
                    <span class="menu-usuario__contador"
                      aria-label="{{ $pendientes }} pendientes">{{ $pendientes > 9 ? '9+' : $pendientes }}</span>
                  @endif
                </a>
              @endunless

              <a class="menu-usuario__item font-body-md text-body-md{{ request()->routeIs('ajustes.*') ? ' menu-usuario__item--activo' : '' }}"
                href="{{ route('ajustes.perfil') }}" role="menuitem">
                <span class="material-symbols-outlined" aria-hidden="true">settings</span>
                <span>Ajustes</span>
              </a>

              <div class="menu-usuario__separador" role="separator"></div>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="menu-usuario__item menu-usuario__item--salir font-body-md text-body-md"
                  role="menuitem">
                  <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                  <span>Cerrar sesión</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>