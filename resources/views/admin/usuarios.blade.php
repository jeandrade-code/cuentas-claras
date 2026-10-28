@extends('layouts.admin')
@section('titulo', 'Usuarios y roles · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-usuarios.css') }}">
@endpush

@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

      {{-- Cabecera --}}
      <header>
        <h1 class="font-headline-xl text-headline-xl text-on-surface">Usuarios y roles</h1>
        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
          Gestión de cuentas. El administrador no ve datos financieros de nadie.
        </p>
      </header>

      {{-- Aviso --}}
      <section class="card p-space-md admin-aviso" data-tour="admin-usuarios-aviso">
        <div class="flex items-start gap-space-sm">
          <span class="material-symbols-outlined text-[20px] admin-aviso__icono">info</span>
          <p class="text-on-surface-variant font-body-sm text-body-sm leading-relaxed">
            <strong class="text-on-surface">Regla:</strong>
            el sistema nunca se queda sin un administrador activo. Tampoco puedes eliminar tu propia cuenta desde este
            panel.
          </p>
        </div>
      </section>

      {{-- Filtros --}}
      <section class="card p-space-lg" data-tour="admin-usuarios-filtros">
        <h2 class="font-headline-sm text-headline-sm text-on-surface mb-space-md">Buscar y filtrar</h2>

        <form method="GET" action="{{ route('admin.usuarios') }}" class="admin-form-filtros">
          <div class="admin-form-filtros__campo admin-form-filtros__campo--buscar">
            <label for="buscar" class="admin-form-crear__label">Buscar</label>
            <input id="buscar" name="buscar" type="text" class="input" value="{{ $filtros['buscar'] ?? '' }}"
              placeholder="Nombre o correo">
          </div>

          <div class="admin-form-filtros__campo">
            <label for="rol" class="admin-form-crear__label">Rol</label>
            <select id="rol" name="rol" class="input">
              <option value="">Todos</option>
              @foreach (['estandar', 'familiar', 'administrador'] as $r)
                <option value="{{ $r }}" @selected(($filtros['rol'] ?? '') === $r)>
                  {{ ucfirst($r) }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="admin-form-filtros__campo">
            <label for="estado" class="admin-form-crear__label">Estado</label>
            <select id="estado" name="estado" class="input">
              <option value="">Todos</option>
              <option value="activos" @selected(($filtros['estado'] ?? '') === 'activos')>Activos</option>
              <option value="suspendidos" @selected(($filtros['estado'] ?? '') === 'suspendidos')>Suspendidos</option>
            </select>
          </div>

          <div class="admin-form-filtros__acciones">
            <button type="submit" class="btn btn-primary">
              <span class="material-symbols-outlined text-[18px]">search</span>
              <span>Filtrar</span>
            </button>
            <a href="{{ route('admin.usuarios') }}" class="btn btn-secondary">
              Limpiar
            </a>
          </div>
        </form>
      </section>

      {{-- Listado --}}
      <section class="card p-space-lg" data-dinamico="usuarios" data-tour="admin-usuarios-lista">
        <header class="admin-lista__cabecera">
          <h2 class="font-headline-md text-headline-md text-on-surface">
            Usuarios registrados
          </h2>
          <span class="admin-lista__contador">
            {{ $usuarios->total() }} {{ $usuarios->total() === 1 ? 'usuario' : 'usuarios' }}
          </span>
        </header>

        @forelse ($usuarios as $u)
              <article class="admin-usuario">
                <div class="admin-usuario__fila">

                  {{-- Identidad --}}
                  <div class="admin-usuario__identidad">
                    <div class="admin-usuario__avatar">
                      {{ strtoupper(mb_substr($u->nombre, 0, 1)) }}
                    </div>
                    <div class="admin-usuario__datos">
                      <div class="flex flex-wrap items-center gap-2">
                        <span class="font-headline-sm text-headline-sm text-on-surface">
                          {{ $u->nombre }}
                        </span>
                        @if ($u->id === auth()->id())
                          <span class="chip chip-pending">Tú</span>
                        @endif
                      </div>
                      <a href="mailto:{{ $u->email }}" class="admin-usuario__correo">
                        {{ $u->email }}
                      </a>
                      <div class="admin-usuario__chips">
                        <span class="chip {{ match ($u->rol) {
            'administrador' => 'chip-rol-admin',
            'familiar' => 'chip-rol-familiar',
            default => 'chip-rol-estandar',
          } }}">
                          {{ ucfirst($u->rol) }}
                        </span>
                        <span class="chip {{ $u->activo ? 'chip-paid' : 'chip-late' }}">
                          {{ $u->activo ? 'Activo' : 'Suspendido' }}
                        </span>
                      </div>
                    </div>
                  </div>

                  {{-- Acciones --}}
                  <div class="admin-usuario__acciones">

                    {{-- Cambiar rol (details) --}}
                    <details class="admin-usuario__accion">
                      <summary class="btn btn-secondary">
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        <span>Rol</span>
                      </summary>
                      <form method="POST" action="{{ route('admin.usuarios.rol', $u) }}" class="admin-usuario__menu">
                        @csrf
                        @method('PUT')
                        <label class="admin-form-crear__label">Cambiar rol a:</label>
                        <select name="rol" class="input">
                          @foreach (['estandar', 'familiar', 'administrador'] as $r)
                            <option value="{{ $r }}" @selected($u->rol === $r)>{{ ucfirst($r) }}</option>
                          @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary w-full justify-center mt-2">
                          Guardar rol
                        </button>
                      </form>
                    </details>

                    {{-- Suspender / Reactivar --}}
                    <form method="POST" action="{{ route('admin.usuarios.estado', $u) }}">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="activo" value="{{ $u->activo ? 0 : 1 }}">
                      <button type="submit" class="btn {{ $u->activo ? 'btn-secondary' : 'btn-primary' }}"
                        title="{{ $u->activo ? 'Suspender cuenta' : 'Reactivar cuenta' }}">
                        <span class="material-symbols-outlined text-[16px]">
                          {{ $u->activo ? 'block' : 'check_circle' }}
                        </span>
                        <span>{{ $u->activo ? 'Suspender' : 'Activar' }}</span>
                      </button>
                    </form>

                    {{-- Eliminar --}}
                    @if ($u->id !== auth()->id())
                      {{-- Botón que abre el modal --}}
                      <button type="button" class="btn btn-danger"
                        onclick="document.getElementById('modal-eliminar-{{ $u->id }}').showModal()">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        <span>Eliminar</span>
                      </button>

                      {{-- Modal de re-autenticación --}}
                      <dialog id="modal-eliminar-{{ $u->id }}" class="admin-modal">
                        <form method="POST" action="{{ route('admin.usuarios.destroy', $u) }}" class="admin-modal__form">
                          @csrf
                          @method('DELETE')

                          <h3 class="admin-modal__titulo">Eliminar a {{ $u->nombre }}</h3>
                          <p class="admin-modal__texto">
                            Esta acción no se puede deshacer. Confirma con tu contraseña de administrador.
                          </p>

                          <label class="admin-modal__label" for="password-eliminar-{{ $u->id }}">
                            Tu contraseña
                          </label>
                          <input type="password" id="password-eliminar-{{ $u->id }}" name="password" required
                            autocomplete="current-password" class="input w-full">

                          <div class="admin-modal__acciones">
                            <button type="button" class="btn btn-secondary"
                              onclick="document.getElementById('modal-eliminar-{{ $u->id }}').close()">
                              Cancelar
                            </button>
                            <button type="submit" class="btn btn-danger">
                              <span class="material-symbols-outlined text-[16px]">delete_forever</span>
                              <span>Eliminar definitivamente</span>
                            </button>
                          </div>
                        </form>
                      </dialog>
                    @endif
                  </div>
                </div>
              </article>
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant py-space-md">
            No hay usuarios con esos filtros
          </p>
        @endforelse

        @if ($usuarios->hasPages())
          <div class="mt-space-lg">{{ $usuarios->links() }}</div>
        @endif
      </section>

    </div>
  </main>
{{-- ONBOARDING --}}
<x-onboarding-tour name="admin-usuarios" :auto-start="true" />
@endsection