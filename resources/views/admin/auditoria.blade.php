@extends('layouts.admin')
@section('titulo', 'Auditoría · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-usuarios.css') }}">
@endpush

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
  <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

    <header>
      <h1 class="font-headline-xl text-headline-xl text-on-surface">Auditoría</h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">
        Historial de acciones del administrador. Solo lectura. Los registros no se pueden editar ni eliminar.
      </p>
    </header>

    {{-- Filtros --}}
    <section class="card p-space-lg" data-tour="admin-aud-tabla">
      <h2 class="font-headline-sm text-headline-sm text-on-surface mb-space-md">Filtrar</h2>
      <form method="GET" action="{{ route('admin.auditoria') }}" class="admin-form-filtros">
        <div class="admin-form-filtros__campo">
          <label for="accion" class="admin-form-crear__label">Acción</label>
          <select id="accion" name="accion" class="input">
            <option value="">Todas</option>
            @foreach ($acciones as $a)
              <option value="{{ $a }}" @selected(($filtros['accion'] ?? '') === $a)>{{ $a }}</option>
            @endforeach
          </select>
        </div>

        <div class="admin-form-filtros__campo">
          <label for="admin" class="admin-form-crear__label">Administrador</label>
          <select id="admin" name="admin" class="input">
            <option value="">Todos</option>
            @foreach ($admins as $ad)
              <option value="{{ $ad->id }}" @selected((string)($filtros['admin'] ?? '') === (string)$ad->id)>
                {{ $ad->nombre }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="admin-form-filtros__campo">
          <label for="desde" class="admin-form-crear__label">Desde</label>
          <input type="date" id="desde" name="desde" class="input" value="{{ $filtros['desde'] ?? '' }}">
        </div>

        <div class="admin-form-filtros__campo">
          <label for="hasta" class="admin-form-crear__label">Hasta</label>
          <input type="date" id="hasta" name="hasta" class="input" value="{{ $filtros['hasta'] ?? '' }}">
        </div>

        <div class="admin-form-filtros__acciones">
          <button type="submit" class="btn btn-primary">
            <span class="material-symbols-outlined text-[18px]">search</span>
            <span>Filtrar</span>
          </button>
          <a href="{{ route('admin.auditoria') }}" class="btn btn-secondary">Limpiar</a>
        </div>
      </form>
    </section>

    {{-- Tabla --}}
    <section class="card p-space-lg" data-tour="admin-aud-tabla">
      <header class="admin-lista__cabecera">
        <h2 class="font-headline-md text-headline-md text-on-surface">Registros</h2>
        <span class="admin-lista__contador">
          {{ $auditorias->total() }} {{ $auditorias->total() === 1 ? 'registro' : 'registros' }}
        </span>
      </header>

      <div class="overflow-x-auto mt-space-md">
        <table class="w-full">
          <thead>
            <tr class="border-b border-outline-variant/40">
              <th class="text-left font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider py-space-sm px-space-xs">Fecha</th>
              <th class="text-left font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider py-space-sm px-space-xs">Admin</th>
              <th class="text-left font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider py-space-sm px-space-xs">Acción</th>
              <th class="text-left font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider py-space-sm px-space-xs">Modelo</th>
              <th class="text-left font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider py-space-sm px-space-xs">Antes → Después</th>
              <th class="text-left font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider py-space-sm px-space-xs">IP</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($auditorias as $a)
              @php
                $chip = match (true) {
                  str_starts_with($a->accion, 'usuario.eliminar')      => 'chip-late',
                  str_starts_with($a->accion, 'usuario.suspender')     => 'chip-soon',
                  str_starts_with($a->accion, 'usuario.reactivar')     => 'chip-paid',
                  str_starts_with($a->accion, 'categoria.desactivar')  => 'chip-soon',
                  str_starts_with($a->accion, 'categoria.reactivar')   => 'chip-paid',
                  default                                               => 'chip-pending',
                };

                $formato = function (?array $data): string {
                  if (empty($data)) return '—';
                  return collect($data)
                    ->map(fn ($v, $k) => "{$k}: " . (is_bool($v) ? ($v ? 'true' : 'false') : (string) $v))
                    ->implode(', ');
                };
              @endphp
              <tr class="border-b border-outline-variant/20">
                <td class="py-space-sm px-space-xs font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap">
                  {{ $a->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="py-space-sm px-space-xs font-body-sm text-body-sm text-on-surface">
                  {{ $a->user?->nombre ?? '—' }}
                  <div class="font-label-sm text-label-sm text-on-surface-variant">{{ $a->user?->email ?? '' }}</div>
                </td>
                <td class="py-space-sm px-space-xs">
                  <span class="chip {{ $chip }}">{{ $a->accion }}</span>
                </td>
                <td class="py-space-sm px-space-xs font-body-sm text-body-sm text-on-surface-variant">
                  @if ($a->modelo)
                    {{ $a->modelo }}#{{ $a->modelo_id }}
                  @else
                    —
                  @endif
                </td>
                <td class="py-space-sm px-space-xs font-body-sm text-body-sm text-on-surface-variant">
                  <div><strong class="text-on-surface">Antes:</strong> {{ $formato($a->antes) }}</div>
                  <div><strong class="text-on-surface">Después:</strong> {{ $formato($a->despues) }}</div>
                </td>
                <td class="py-space-sm px-space-xs font-body-sm text-body-sm text-on-surface-variant whitespace-nowrap">
                  {{ $a->ip ?? '—' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="py-space-lg text-center font-body-md text-body-md text-on-surface-variant">
                  No hay registros de auditoría con esos filtros.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if ($auditorias->hasPages())
        <div class="mt-space-lg">{{ $auditorias->links() }}</div>
      @endif
    </section>

  </div>
</main>
@endsection