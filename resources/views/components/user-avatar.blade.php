{{--
==============================================================================
ARCHIVO : resources/views/components/user-avatar.blade.php
QUÉ ES  : Avatar del usuario. Muestra la foto si existe; si no, iniciales.
USO     : <x-user-avatar :size="36" />               (usa el usuario autenticado)
          <x-user-avatar :user="$usuario" :size="64" />
==============================================================================
--}}
@props(['user' => null, 'size' => 36])

@php
  $u = $user ?? auth()->user();
  $size = (int) $size;
  $url = $u?->avatar_url;

  $iniciales = '';
  if ($u && $u->nombre) {
      $partes = preg_split('/\s+/', trim($u->nombre));
      $primera = mb_substr($partes[0] ?? '', 0, 1);
      $segunda = mb_substr($partes[1] ?? '', 0, 1);
      $iniciales = mb_strtoupper($primera . $segunda);
  }
@endphp

@if ($url)
  <img
    src="{{ $url }}"
    alt="Foto de perfil de {{ $u->nombre }}"
    width="{{ $size }}"
    height="{{ $size }}"
    class="rounded-full object-cover bg-surface-container ring-1 ring-outline-variant/40"
    style="width: {{ $size }}px; height: {{ $size }}px;"
    loading="lazy"
  >
@else
  <span
    class="inline-flex items-center justify-center rounded-full bg-primary text-on-primary font-label-md select-none"
    style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ max(11, round($size * 0.4)) }}px;"
    aria-hidden="true"
  >{{ $iniciales ?: '?' }}</span>
@endif