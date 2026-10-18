{{--
  ==============================================================================
  ARCHIVO : resources/views/app/notificaciones/index.blade.php
  QUÉ ES  : Centro de notificaciones (N4). Pendientes y anteriores del usuario,
            con iconos Material Symbols por tipo (partial _icono).
  RUTA    : GET  /notificaciones             (notificaciones.index)
            POST /notificaciones/leer        (notificaciones.leer)
            POST /notificaciones/leer-todas  (notificaciones.leerTodas)
  CONTROLADOR: NotificacionController
  DATOS   : $pendientes (LengthAwarePaginator), $anteriores (LengthAwarePaginator)
  COLOR   : tokens --accent-* (tinte por rol). NUNCA var(--primary) directo.
  ==============================================================================
--}}
@extends('layouts.app')

@section('titulo', 'Notificaciones · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/notificaciones.css') }}">
@endpush

@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">

    @if (session('exito'))
      <div
        role="status"
        class="w-full max-w-7xl mx-auto mt-4 px-4 py-3 rounded-lg"
        style="background: var(--surface-container-lowest); color: var(--primary); border: 1px solid var(--outline-variant);"
      >
        {{ session('exito') }}
      </div>
    @endif

    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

      {{-- Encabezado --}}
      <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <h1 class="font-headline-xl text-headline-xl text-on-surface">Notificaciones</h1>
          <p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Consulta tus alertas y mantente al día con tus gastos y metas.
          </p>
        </div>
        @if ($pendientes->total() > 0)
          <form action="{{ route('notificaciones.leerTodas') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary">
              Marcar todas como leídas
            </button>
          </form>
        @endif
      </header>

      {{-- PENDIENTES --}}
      <section class="card p-space-lg" aria-labelledby="titulo-pendientes">
        <h2 id="titulo-pendientes" class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Pendientes <span class="text-on-surface-variant">({{ $pendientes->total() }})</span>
        </h2>

        @forelse ($pendientes as $item)
          <article class="notificacion-item notificacion-item--{{ $item->tipo }}">
            @include('app.notificaciones._icono', ['tipo' => $item->tipo])

            <div class="notificacion-cuerpo">
              <p class="font-body-md text-on-surface">{{ $item->mensaje }}</p>
              <p class="font-body-sm text-on-surface-variant mt-1">
                {{ $item->created_at->locale('es')->diffForHumans() }}
              </p>
              <form action="{{ route('notificaciones.leer') }}" method="POST" class="mt-2">
                @csrf
                <input type="hidden" name="notificacion_id" value="{{ $item->id }}">
                <button type="submit" class="notificacion-enlace">
                  Ver notificación
                </button>
              </form>
            </div>

            <span class="notificacion-punto" aria-label="No leída" title="No leída">●</span>
          </article>
        @empty
          <p class="font-body-md text-on-surface-variant py-4">
            No tienes notificaciones pendientes.
          </p>
        @endforelse

        @if ($pendientes->hasPages())
          <div class="mt-4">{{ $pendientes->links() }}</div>
        @endif
      </section>

      {{-- ANTERIORES --}}
      <section class="card p-space-lg" aria-labelledby="titulo-anteriores">
        <h2 id="titulo-anteriores" class="font-headline-md text-headline-md text-on-surface mb-space-md">
          Anteriores <span class="text-on-surface-variant">({{ $anteriores->total() }})</span>
        </h2>

        @forelse ($anteriores as $item)
          <article class="notificacion-item notificacion-item--{{ $item->tipo }}">
            @include('app.notificaciones._icono', ['tipo' => $item->tipo])

            <div class="notificacion-cuerpo">
              <p class="font-body-md text-on-surface">{{ $item->mensaje }}</p>
              <p class="font-body-sm text-on-surface-variant mt-1">
                {{ $item->created_at->locale('es')->diffForHumans() }}
              </p>
              <span class="notificacion-estado">
                {{ $item->estado === 'leida' ? 'Leída' : 'Cancelada' }}
              </span>
            </div>
          </article>
        @empty
          <p class="font-body-md text-on-surface-variant py-4">
            Sin notificaciones anteriores.
          </p>
        @endforelse

        @if ($anteriores->hasPages())
          <div class="mt-4">{{ $anteriores->links() }}</div>
        @endif
      </section>

    </div>
  </main>
@endsection