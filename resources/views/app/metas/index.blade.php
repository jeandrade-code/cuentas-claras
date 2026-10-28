{{--
  ==============================================================================
  ARCHIVO : resources/views/app/metas/index.blade.php        (J5 HECHO)
  QUÉ ES  : "Metas": lista de mis metas (individuales o compartidas) con su avance
            y formulario para crear una.
  RUTA    : GET /metas (metas.index) · POST /metas (metas.store) · MetaController@index
  DATOS   : $metas (con aportes_sum_monto = total conjunto) · $invitaciones
  PRIVACIDAD: solo se muestran NOMBRES de participantes y el total conjunto; nunca
              aportes individuales.
  CSS     : public/css/metas-compartidas.css
  NOTA 2026-10-20: título y subtítulo cambian según rol:
    · estandar → "Metas"
    · familiar → "Metas compartidas"
  ==============================================================================
--}}

@extends('layouts.app')

@php
  $esFamiliar   = auth()->user()->rol === 'familiar';
  $tituloPagina = $esFamiliar ? 'Metas compartidas' : 'Metas';
  $subtitulo    = $esFamiliar
      ? 'Ahorra junto a otra persona hacia un objetivo común'
      : 'Ahorra hacia tus objetivos personales';
@endphp

@section('titulo', $tituloPagina . ' · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/metas-compartidas.css') }}">
@endpush

@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
      <header class="metas-encabezado" data-tour="metas-header">
        @if(! $esFamiliar)
          <section class="card p-space-md" role="note">
            <p class="font-body-md text-body-md text-on-surface-variant">
              Puedes tener metas individuales. Para <strong>compartir</strong> una meta con otra persona,
              <a href="{{ route('ajustes.tipo-cuenta') }}" class="text-primary">cambia tu tipo de cuenta a familiar</a>.
            </p>
          </section>
        @endif
        <div>
          <h1 class="font-headline-xl text-headline-xl text-on-surface">{{ $tituloPagina }}</h1>
          <p class="font-body-md text-body-md text-on-surface-variant mt-1">{{ $subtitulo }}</p>
        </div>
        <a href="{{ route('metas.invitaciones') }}" class="btn btn-secondary">
          <span class="material-symbols-outlined text-[18px]">mail</span>
          Invitaciones
          @if($invitaciones > 0)<span class="metas-insignia">{{ $invitaciones }}</span>@endif
        </a>
      </header>

      <section class="card p-space-lg" data-dinamico="nueva-meta" data-tour="metas-form">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
          {{ $esFamiliar ? 'Crear una meta' : 'Crear una meta' }}
        </h2>
        <form method="POST" action="{{ route('metas.store') }}" class="metas-formulario" novalidate>
          @csrf
          <div class="metas-campo">
            <label for="nombre" class="font-label-md text-label-md text-on-surface">Nombre de la meta</label>
            <input id="nombre" name="nombre" type="text" maxlength="100" class="input" value="{{ old('nombre') }}" required>
            @error('nombre')<p class="metas-error">{{ $message }}</p>@enderror
          </div>
          <div class="metas-campo">
            <label for="monto_objetivo" class="font-label-md text-label-md text-on-surface">Monto objetivo (COP)</label>
            <input id="monto_objetivo" name="monto_objetivo" type="text" inputmode="numeric" class="input" value="{{ old('monto_objetivo') }}" required>
            @error('monto_objetivo')<p class="metas-error">{{ $message }}</p>@enderror
          </div>
          <div class="metas-campo">
            <label for="fecha_limite" class="font-label-md text-label-md text-on-surface">Fecha límite (opcional)</label>
            <input id="fecha_limite" name="fecha_limite" type="date" class="input" value="{{ old('fecha_limite') }}">
            @error('fecha_limite')<p class="metas-error">{{ $message }}</p>@enderror
          </div>
          @if($esFamiliar)
            <div class="metas-campo metas-campo--ancho metas-compartir">
              <label class="metas-casilla" for="compartida">
                <input id="compartida" name="compartida" type="checkbox" value="1" @checked(old('compartida'))>
                <span>
                  <strong class="font-label-md text-label-md text-on-surface">Compartir esta meta con otra persona</strong>
                  <small class="font-body-sm text-body-sm text-on-surface-variant">Si no la compartes, será una meta solo tuya.</small>
                </span>
              </label>
            </div>
            <div class="metas-campo metas-campo--correo">
              <label for="correo" class="font-label-md text-label-md text-on-surface">Correo de la persona a invitar</label>
              <input id="correo" name="correo" type="email" class="input" value="{{ old('correo') }}">
              @error('correo')<p class="metas-error">{{ $message }}</p>@enderror
            </div>
          @endif
          <div class="metas-acciones">
            <button type="submit" class="btn btn-primary">Crear meta</button>
          </div>
        </form>
      </section>

      <section class="card p-space-lg" data-dinamico="metas" data-tour="metas-lista">
        <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">Mis metas</h2>
        @forelse($metas as $meta)
          @php($porcentaje = $meta->porcentaje())
          <a href="{{ route('metas.show', $meta) }}" class="metas-item">
            <div class="metas-item__cabecera">
              <h3 class="font-headline-sm text-headline-sm text-on-surface">{{ $meta->nombre }}</h3>
              <span class="metas-estado metas-estado--{{ $meta->estado }}">{{ ucfirst($meta->estado) }} · {{ $meta->esCompartida() ? 'Compartida' : 'Individual' }}</span>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant">
              @if($meta->esCompartida())
                Con:
                {{ $meta->participaciones->where('user_id', '!=', auth()->id())->map(fn ($p) => $p->usuario->nombre.($p->estado_invitacion === 'pendiente' ? ' (invitación pendiente)' : ''))->join(', ') ?: 'nadie todavía' }}
              @else
                Meta individual
              @endif
            </p>
            <div class="metas-barra" role="progressbar" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">
              <span class="metas-barra__relleno" style="width: {{ $porcentaje }}%"></span>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant">
              ${{ number_format($meta->totalConjunto(), 0, ',', '.') }} de ${{ number_format($meta->monto_objetivo, 0, ',', '.') }}
              · {{ $porcentaje }}%
              @if($meta->fecha_limite) · hasta {{ $meta->fecha_limite->format('d/m/Y') }} @endif
            </p>
          </a>
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant">
            {{ $esFamiliar ? 'Aún no tienes metas compartidas' : 'Aún no tienes metas' }}
          </p>
        @endforelse
      </section>
    </div>
  </main>
    {{-- ONBOARDING TOUR --}}
  <x-onboarding-tour name="metas" :auto-start="true" />
@endsection