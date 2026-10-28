{{--
==============================================================================
ARCHIVO : resources/views/components/auth-background.blade.php
QUÉ ES  : Fondo decorativo sutil para las páginas de autenticación.
USO     : <x-auth-background /> como PRIMER hijo de <main>.
HACE    : patrón de iconos financieros (SVG como máscara) + 2 glows difuminados
          que derivan lentamente. Todo pointer-events-none.
ADAPTA  : al tema (claro/oscuro) y al rol (color de marca vía bg-primary).
==============================================================================
--}}
<div class="auth-bg" aria-hidden="true">
    {{-- Patrón de iconos: SVG como máscara, color = marca del rol --}}
    <div class="auth-bg__doodles bg-primary"></div>

    {{-- Dos glows difuminados que derivan lentamente --}}
    <div class="auth-bg__glow auth-bg__glow--1 bg-primary/10"></div>
    <div class="auth-bg__glow auth-bg__glow--2 bg-tertiary-container/15"></div>
</div>