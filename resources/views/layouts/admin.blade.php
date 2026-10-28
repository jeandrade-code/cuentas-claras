{{--
  ==============================================================================
  ARCHIVO : resources/views/layouts/admin.blade.php
  QUÉ ES  : Plantilla de administración: igual a app pero con <x-sidebar-admin />.
  QUÉ HAY QUE HACER (en orden):
    1. Aplicar middleware rol:administrador a todas las rutas que la usen.
  ==============================================================================
--}}

@extends('layouts.base')
@section('meta_robots', 'noindex, nofollow')
@section('body')
  <x-sidebar-admin />
  <div class="nav-backdrop" data-nav-close></div>
  <div class="pl-72 pt-16 min-h-screen">
    <x-topbar />
    @yield('content')
  </div>
@endsection
