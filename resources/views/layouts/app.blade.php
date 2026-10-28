{{--
  ==============================================================================
  ARCHIVO : resources/views/layouts/app.blade.php
  QUÉ ES  : Plantilla de la zona privada. Decide el sidebar según el rol.
            Los admin que naveguen a Ajustes conservan su menú.
  ACTUALIZADO 2026-10-16.
  ==============================================================================
--}}

@extends('layouts.base')
@section('meta_robots', 'noindex, nofollow')
@section('body')
  @if (auth()->check() && auth()->user()->rol === 'administrador')
    <x-sidebar-admin />
  @else
    <x-sidebar />
  @endif

  <div class="nav-backdrop" data-nav-close></div>

  <div class="pl-72 pt-16 min-h-screen">
    <x-topbar />
    @yield('content')
  </div>
@endsection