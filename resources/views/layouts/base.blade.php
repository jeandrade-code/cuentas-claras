{{--
  ==============================================================================
  ARCHIVO : resources/views/layouts/base.blade.php
  QUÉ ES  : Plantilla raíz: <head>, CSS global, theme (claro/oscuro), toast y scripts.
            Todas las demás la extienden.
  ACTUALIZADO 2026-10-14:
    - Incluye public/css/theme.css (modo oscuro).
    - Incluye public/css/transiciones.css (fade-in + anti-reflow + view transitions).
    - Incluye un script inline en <head> para aplicar el tema ANTES de renderizar
      el body (evita el destello blanco al cargar en oscuro).
    - data-account-type en <html> para el tinte por tipo de cuenta.
    - Preconnect + preload de Google Fonts (evita el salto de fuente).
  ACTUALIZADO 2026-10-21:
    - Se eliminó @stack('scripts') duplicado del <head> (los scripts van al
      final del body, una sola vez).
  ==============================================================================
--}}

@php
  // Tinte por tipo de cuenta (ver theme.css secciones 5 y 8).
  $rol = strtolower((string) optional(auth()->user())->rol);
  $accountType = match ($rol) {
      'familiar'      => 'familiar',
      'administrador' => 'administrador',
      default         => 'estandar',
  };
@endphp

<!DOCTYPE html>
<html
  lang="es"
  data-theme="claro"
  data-account-type="{{ $accountType }}"
>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- ========================================================= --}}
    {{-- OPTIMIZACIÓN SEO                                        --}}
    {{-- ========================================================= --}}
    <title>@yield('titulo', 'Cuentas Claras - Gestión de Finanzas Personales')</title>
    <meta name="description" content="@yield('meta_description', 'Plataforma web para administrar tus finanzas personales. Controla gastos, simula créditos y alcanza tus metas de ahorro.')">
    <link rel="canonical" href="{{ url()->current() }}">
    
    {{-- Control de indexación (por defecto indexa, las vistas privadas lo bloquean) --}}
    <meta name="robots" content="@yield('meta_robots', 'index, follow')">

    {{-- Open Graph (WhatsApp, Facebook, LinkedIn) --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('titulo', 'Cuentas Claras')">
    <meta property="og:description" content="@yield('meta_description', 'Plataforma web para administrar tus finanzas personales.')">
    <meta property="og:image" content="@yield('meta_image', asset('img/hero-main.jpg'))">

    {{-- Twitter Cards (X) --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="@yield('titulo', 'Cuentas Claras')">
    <meta name="twitter:description" content="@yield('meta_description', 'Plataforma web para administrar tus finanzas personales.')">
    <meta name="twitter:image" content="@yield('meta_image', asset('img/hero-main.jpg'))">
    {{-- ========================================================= --}}

    {{-- Color de la barra del navegador en móvil (se actualiza con JS) --}}
    <meta name="theme-color" content="#f8f9ff" id="meta-theme-color">

    {{-- Favicon dinámico: se genera por JS según tema + tipo de cuenta --}}
    <link id="favicon-cc" rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 48'%3E%3Crect width='48' height='48' rx='12' fill='%23059669'/%3E%3C/svg%3E">

    {{-- Preconnect: abre la conexión TLS con Google Fonts antes de pedir nada --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Aplica el tema + favicon + theme-color ANTES de pintar el body (evita flash) --}}
    <script>
      (function () {
        var PALETA = {
          'estandar|claro':      { bg: '#059669', fg: '#ffffff', accent: '#a7f3d0' },
          'estandar|oscuro':     { bg: '#10b981', fg: '#ffffff', accent: '#86efac' },
          'familiar|claro':      { bg: '#4648d4', fg: '#ffffff', accent: '#c0c1ff' },
          'familiar|oscuro':     { bg: '#6063ee', fg: '#ffffff', accent: '#e1e0ff' },
          'administrador|claro': { bg: '#2563eb', fg: '#ffffff', accent: '#bfdbfe' },
          'administrador|oscuro':{ bg: '#3b82f6', fg: '#ffffff', accent: '#dbeafe' }
        };
        var TEMA_COLOR = { claro: '#f8f9ff', oscuro: '#0f1216' };

        function faviconUrl(p) {
          var svg =
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">' +
              '<rect width="48" height="48" rx="12" fill="' + p.bg + '"/>' +
              '<path d="M14 24C14 18.5 18.5 14 24 14C27.5 14 30.5 15.8 32.2 18.5" ' +
                    'stroke="' + p.fg + '" stroke-width="3" stroke-linecap="round" fill="none"/>' +
              '<path d="M30 24C30 29.5 25.5 34 20 34C16.5 34 13.5 32.2 11.8 29.5" ' +
                    'stroke="' + p.accent + '" stroke-width="3" stroke-linecap="round" fill="none"/>' +
              '<circle cx="22" cy="24" r="3" fill="' + p.fg + '"/>' +
            '</svg>';
          return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
        }

        function refreshIcons() {
          var root = document.documentElement;
          var tema = root.getAttribute('data-theme') === 'oscuro' ? 'oscuro' : 'claro';
          var rol  = root.getAttribute('data-account-type') || 'estandar';
          var key  = rol + '|' + tema;

          var p = PALETA[key] || PALETA['estandar|claro'];

          var iconEl = document.getElementById('favicon-cc');
          if (iconEl) iconEl.setAttribute('href', faviconUrl(p));

          var metaEl = document.getElementById('meta-theme-color');
          if (metaEl) metaEl.setAttribute('content', TEMA_COLOR[tema]);
        }

        window.CC = window.CC || {};
        window.CC.refreshIcons = refreshIcons;

        try {
          var t = localStorage.getItem('cc-tema');
          if (t !== 'oscuro' && t !== 'claro') {
            t = (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
              ? 'oscuro' : 'claro';
          }
          document.documentElement.setAttribute('data-theme', t);
          document.documentElement.style.colorScheme = (t === 'oscuro' ? 'dark' : 'light');
        } catch (e) {
          document.documentElement.setAttribute('data-theme', 'claro');
        }

        refreshIcons();
      })();
    </script>

    {{-- display=swap → Plus Jakarta Sans --}}
    <link
      href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    >
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block"
      rel="stylesheet"
    >

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/transiciones.css') }}">

    {{-- Los estilos de cada vista van aquí, después del CSS global --}}
    @stack('styles')
  </head>
  <body class="@yield('body-class', 'bg-surface font-body-md text-body-md text-on-surface antialiased')">
    <a href="#contenido" class="skip-link">Saltar al contenido principal</a>
    @yield('body')
    <x-toast />
    <script>
      (function () {
        function marcarMain() {
          var main = document.querySelector('main');
          if (main && !main.id) main.id = 'contenido';
        }
        if (document.readyState === 'loading') {
          document.addEventListener('DOMContentLoaded', marcarMain);
        } else {
          marcarMain();
        }
      })();
    </script>
    
<script src="{{ asset('js/transiciones.js') }}" defer></script>
<script src="{{ asset('js/theme.js') }}" defer></script>
<script src="{{ asset('js/shell.js') }}" defer></script>

{{-- JavaScript de la aplicación y gráficas Chart.js --}}
@vite('resources/js/app.js')

{{-- Los scripts de cada vista van aquí, al final del body --}}
@stack('scripts')

  </body>
</html>