@extends('layouts.admin')
@section('titulo', 'Categorías del sistema · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-categorias.css') }}">
@endpush

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
  <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

    {{-- Cabecera --}}
    <header>
      {{-- Errores globales (incluye el de "categoría en uso" al intentar eliminar) --}}
      @if ($errors->any())
        <div class="card p-space-md" style="background: var(--error-container, #fee2e2); color: var(--on-error-container, #7f1d1d);" role="alert">
          <div class="flex items-start gap-space-sm">
            <span class="material-symbols-outlined text-[20px]">error</span>
            <ul class="list-disc pl-5 space-y-1 font-body-sm text-body-sm">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      @endif
      <h1 class="font-headline-xl text-headline-xl text-on-surface">
        Categorías del sistema
      </h1>
      <p class="font-body-md text-body-md text-on-surface-variant mt-1">
        Estas son las categorías globales que todos los usuarios ven al registrar sus gastos.
      </p>
    </header>

    {{-- Aviso contextual --}}
    <section class="card p-space-md admin-aviso" data-tour="admin-cat-aviso">
      <div class="flex items-start gap-space-sm">
        <span class="material-symbols-outlined text-[20px] admin-aviso__icono">info</span>
        <div class="text-on-surface-variant font-body-sm text-body-sm leading-relaxed">
          <p>
            <strong class="text-on-surface">¿Para qué sirven?</strong>
            Cada categoría pertenece a uno de los tres bloques del 50/30/20. El bloque determina
            cómo se clasifica el gasto en el informe mensual del usuario.
          </p>
          <p class="mt-2">
            <strong class="text-on-surface">¿Por qué no se borran?</strong>
            Las categorías <em>se desactivan</em>, no se eliminan. Al desactivar una, deja de
            aparecer al registrar gastos nuevos, pero los gastos históricos que la usan siguen
            mostrándola sin problema.
          </p>
        </div>
      </div>
    </section>

    {{-- Resumen --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-space-md" data-tour="admin-cat-resumen">
      <div class="card p-space-md">
        <p class="text-on-surface-variant font-label-md text-label-md">Total categorías</p>
        <p class="font-numeric-lg text-numeric-lg text-on-surface mt-2">{{ $resumen['total'] }}</p>
      </div>
      <div class="card p-space-md">
        <p class="text-on-surface-variant font-label-md text-label-md">Activas</p>
        <p class="font-numeric-lg text-numeric-lg text-on-surface mt-2">{{ $resumen['activas'] }}</p>
      </div>
      <div class="card p-space-md">
        <p class="text-on-surface-variant font-label-md text-label-md">Inactivas</p>
        <p class="font-numeric-lg text-numeric-lg text-on-surface mt-2">{{ $resumen['inactivas'] }}</p>
      </div>
    </section>

    {{-- Crear categoría --}}
    <section class="card p-space-lg" data-tour="admin-cat-crear">
      <h2 class="font-headline-md text-headline-md text-on-surface mb-space-xs">
        Crear nueva categoría
      </h2>
      <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md">
        Nombre único. El bloque determina en qué porcentaje del 50/30/20 entra esta categoría.
      </p>

      <form method="POST" action="{{ route('admin.categorias.store') }}"
            class="admin-form-crear">
        @csrf

        <div class="admin-form-crear__campo admin-form-crear__campo--nombre">
          <label for="nombre" class="admin-form-crear__label">Nombre</label>
          <input id="nombre" name="nombre" type="text" class="input"
                 value="{{ old('nombre') }}" required
                 placeholder="Ej. Colegiatura, Seguros, Crypto">
          @error('nombre')
            <p class="text-error text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="admin-form-crear__campo admin-form-crear__campo--bloque">
          <label for="bloque" class="admin-form-crear__label">Bloque del 50/30/20</label>
          <select id="bloque" name="bloque" class="input" required>
            @foreach ($bloques as $clave => $info)
              <option value="{{ $clave }}" @selected(old('bloque') === $clave)>
                {{ $info['nombre'] }} ({{ $info['porcentaje'] }}%)
              </option>
            @endforeach
          </select>
          @error('bloque')
            <p class="text-error text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="admin-form-crear__campo admin-form-crear__campo--peso">
          <label for="peso" class="admin-form-crear__label">
            Peso recomendado
            <span class="admin-form-crear__opcional">(opcional)</span>
          </label>
          <div class="admin-form-crear__input-sufijo">
            <input id="peso" name="peso_recomendado" type="number" min="0" max="100"
                   class="input" value="{{ old('peso_recomendado') }}"
                   placeholder="—">
            <span class="admin-form-crear__sufijo">%</span>
          </div>
        </div>

                {{-- SELECTOR DE ICONO (crear) --}}
        <div class="admin-form-crear__campo" style="grid-column: 1 / -1;">
          <label class="admin-form-crear__label">
            Icono
            <span class="admin-form-crear__opcional">(opcional)</span>
          </label>
          <div class="categoria-icono-selector">
            <label class="categoria-icono-label" title="Icono automático (por nombre/bloque)">
              <input type="radio" name="icono" value="" class="sr-only"
                     {{ old('icono') === '' || old('icono') === null ? 'checked' : '' }}>
              <span class="material-symbols-outlined text-[20px]">magic_button</span>
            </label>
            @foreach(\App\Models\Categoria::ICONOS as $key => $label)
              <label class="categoria-icono-label" title="{{ $label }}">
                <input type="radio" name="icono" value="{{ $key }}" class="sr-only"
                       {{ old('icono') === $key ? 'checked' : '' }}>
                <x-categoria-icono :icono="$key" :size="20" />
              </label>
            @endforeach
          </div>
          @error('icono')
            <p class="text-error text-sm mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="admin-form-crear__campo admin-form-crear__campo--accion">
          <button type="submit" class="btn btn-primary admin-form-crear__boton">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>Crear</span>
          </button>
        </div>
      </form>
    </section>
    
    {{-- Categorías agrupadas por bloque --}}
    <div data-tour="admin-cat-lista">
    @foreach ($bloques as $clave => $info)
      @php
        $categoriasDelBloque = $categorias->where('bloque', $clave);
      @endphp

      <section class="card p-space-lg admin-bloque">
        {{-- Cabecera del bloque --}}
        <header class="admin-bloque__cabecera">
          <div class="flex items-center gap-space-sm">
            <span class="admin-bloque__punto" style="background: {{ $info['color'] }}"></span>
            <h2 class="font-headline-md text-headline-md text-on-surface">
              {{ $info['nombre'] }}
            </h2>
            <span class="admin-bloque__porcentaje" style="background: {{ $info['bg'] }}; color: {{ $info['color'] }}">
              {{ $info['porcentaje'] }}% del ingreso
            </span>
          </div>
          <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">
            {{ $info['descripcion'] }}
          </p>
          <p class="font-label-sm text-label-sm text-on-surface-variant mt-2">
            {{ $categoriasDelBloque->count() }} {{ $categoriasDelBloque->count() === 1 ? 'categoría' : 'categorías' }}
          </p>
        </header>

        {{-- Listado de categorías del bloque --}}
        @forelse ($categoriasDelBloque as $cat)
          <article class="admin-categoria">
            <div class="admin-categoria__fila">
              <div class="admin-categoria__info">
                <div class="flex flex-wrap items-center gap-2">
                  <x-categoria-icono :categoria="$cat" :size="20" class="admin-categoria__icono" />
                  <span class="font-headline-sm text-headline-sm text-on-surface">
                    {{ $cat->nombre }}
                  </span>
                  <span class="chip {{ $cat->activa ? 'chip-paid' : 'chip-late' }}">
                    {{ $cat->activa ? 'Activa' : 'Inactiva' }}
                  </span>
                </div>

                <div class="admin-categoria__meta">
                  <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                  <span>
                    Usada en
                    <strong class="text-on-surface">{{ $cat->gastos_count }}</strong>
                    {{ $cat->gastos_count === 1 ? 'gasto' : 'gastos' }}
                  </span>
                  <span class="text-outline-variant">·</span>
                  <span class="material-symbols-outlined text-[14px]">event_repeat</span>
                  <span>
                    <strong class="text-on-surface">{{ $cat->gastos_fijos_count }}</strong>
                    {{ $cat->gastos_fijos_count === 1 ? 'gasto fijo' : 'gastos fijos' }}
                  </span>
                </div>

                <div class="admin-categoria__submeta">
                  <span>
                    Peso recomendado:
                    <strong>{{ $cat->peso_recomendado ?? '—' }}{{ $cat->peso_recomendado !== null ? '%' : '' }}</strong>
                  </span>
                  <span class="text-outline-variant">·</span>
                  <span>Creada el {{ $cat->created_at->format('d/m/Y') }}</span>
                </div>
              </div>

              <div class="admin-categoria__acciones">
              {{-- Desactivar / Activar con re-auth --}}
              <button type="button"
                      class="btn {{ $cat->activa ? 'btn-secondary' : 'btn-primary' }}"
                      onclick="document.getElementById('modal-desactivar-{{ $cat->id }}').showModal()">
                {{ $cat->activa ? 'Desactivar' : 'Activar' }}
              </button>

              <dialog id="modal-desactivar-{{ $cat->id }}" class="admin-modal">
                <form method="POST" action="{{ route('admin.categorias.desactivar', $cat) }}" class="admin-modal__form">
                  @csrf
                  @method('PATCH')

                  <h3 class="admin-modal__titulo">
                    {{ $cat->activa ? 'Desactivar' : 'Activar' }} "{{ $cat->nombre }}"
                  </h3>
                  <p class="admin-modal__texto">
                    @if ($cat->activa)
                      La categoría dejará de aparecer al registrar gastos nuevos. Los gastos
                      históricos que la usan no se modifican. Confirma con tu contraseña.
                    @else
                      La categoría volverá a aparecer al registrar gastos nuevos. Confirma con tu contraseña.
                    @endif
                  </p>

                  <label class="admin-modal__label" for="password-cat-{{ $cat->id }}">Tu contraseña</label>
                  <input type="password"
                        id="password-cat-{{ $cat->id }}"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="input w-full">

                  <div class="admin-modal__acciones">
                    <button type="button"
                            class="btn btn-secondary"
                            onclick="document.getElementById('modal-desactivar-{{ $cat->id }}').close()">
                      Cancelar
                    </button>
                    <button type="submit" class="btn {{ $cat->activa ? 'btn-secondary' : 'btn-primary' }}">
                      {{ $cat->activa ? 'Desactivar' : 'Activar' }}
                    </button>
                  </div>
                </form>
              </dialog>
                {{-- Detalles / Editar (details-accordion nativo) --}}
                <details class="admin-categoria__editar">
                  <summary class="btn btn-secondary">Editar</summary>
                  <form method="POST" action="{{ route('admin.categorias.update', $cat) }}"
                        class="admin-categoria__form">
                    @csrf
                    @method('PUT')

                    <div class="flex-1 min-w-[10rem]">
                      <label class="block mb-1 font-label-sm text-label-sm">Nombre</label>
                      <input name="nombre" type="text" class="input" value="{{ $cat->nombre }}" required>
                    </div>

                    <div>
                      <label class="block mb-1 font-label-sm text-label-sm">Bloque</label>
                      <select name="bloque" class="input">
                        @foreach ($bloques as $bClave => $bInfo)
                          <option value="{{ $bClave }}" @selected($cat->bloque === $bClave)>
                            {{ $bInfo['nombre'] }} ({{ $bInfo['porcentaje'] }}%)
                          </option>
                        @endforeach
                      </select>
                    </div>

                    <div>
                      <label class="block mb-1 font-label-sm text-label-sm">Peso %</label>
                      <input name="peso_recomendado" type="number" min="0" max="100" class="input"
                             value="{{ $cat->peso_recomendado }}">
                    </div>

                    {{-- SELECTOR DE ICONO (editar) --}}
                    <div class="admin-categoria__form-iconos">
                      <label class="block mb-1 font-label-sm text-label-sm">Icono</label>
                      <div class="categoria-icono-selector">
                        <label class="categoria-icono-label" title="Icono automático">
                          <input type="radio" name="icono" value="" class="sr-only"
                                 {{ old('icono', $cat->icono) === '' || old('icono', $cat->icono) === null ? 'checked' : '' }}>
                          <span class="material-symbols-outlined text-[20px]">magic_button</span>
                        </label>
                        @foreach(\App\Models\Categoria::ICONOS as $key => $label)
                          <label class="categoria-icono-label" title="{{ $label }}">
                            <input type="radio" name="icono" value="{{ $key }}" class="sr-only"
                                   {{ old('icono', $cat->icono) === $key ? 'checked' : '' }}>
                            <x-categoria-icono :icono="$key" :size="20" />
                          </label>
                        @endforeach
                      </div>
                      @error('icono')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                  </form>
                </details>
              </div>
              {{-- Eliminar (solo si no está en uso) --}}
              @php
                  $enUso = ($cat->gastos_count + $cat->gastos_fijos_count) > 0;
              @endphp

              @if ($enUso)
                  <button type="button"
                          class="btn btn-danger"
                          disabled
                          title="No se puede eliminar: está en uso ({{ $cat->gastos_count }} {{ $cat->gastos_count === 1 ? 'gasto' : 'gastos' }}, {{ $cat->gastos_fijos_count }} {{ $cat->gastos_fijos_count === 1 ? 'gasto fijo' : 'gastos fijos' }}). Desactívala en su lugar.">
                      <span class="material-symbols-outlined text-[16px]">delete</span>
                      <span>Eliminar</span>
                  </button>
              @else
                  <button type="button"
                          class="btn btn-danger"
                          onclick="document.getElementById('modal-eliminar-{{ $cat->id }}').showModal()">
                      <span class="material-symbols-outlined text-[16px]">delete</span>
                      <span>Eliminar</span>
                  </button>

                  <dialog id="modal-eliminar-{{ $cat->id }}" class="admin-modal">
                      <form method="POST"
                            action="{{ route('admin.categorias.destroy', $cat) }}"
                            class="admin-modal__form">
                          @csrf
                          @method('DELETE')

                          <h3 class="admin-modal__titulo">
                              Eliminar "{{ $cat->nombre }}"
                          </h3>
                          <p class="admin-modal__texto">
                              Esta acción no se puede deshacer. La categoría se eliminará del sistema y
                              dejará de aparecer en el listado global. Confirma con tu contraseña.
                          </p>

                          <label class="admin-modal__label" for="password-eliminar-{{ $cat->id }}">
                              Tu contraseña
                          </label>
                          <input type="password"
                                id="password-eliminar-{{ $cat->id }}"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="input w-full">

                          <div class="admin-modal__acciones">
                              <button type="button"
                                      class="btn btn-secondary"
                                      onclick="document.getElementById('modal-eliminar-{{ $cat->id }}').close()">
                                  Cancelar
                              </button>
                              <button type="submit" class="btn btn-danger">
                                  <span class="material-symbols-outlined text-[16px]">delete_forever</span>
                                  <span>Eliminar categoría</span>
                              </button>
                          </div>
                      </form>
                  </dialog>
              @endif
            </div>
          </article>
        @empty
          <p class="font-body-md text-body-md text-on-surface-variant py-space-md px-10">
            No hay categorías en este bloque.
          </p>
        @endforelse
      </section>
    @endforeach

    {{-- Nota final --}}
    <section class="card p-space-md">
      <p class="font-body-sm text-body-sm text-on-surface-variant">
        <strong class="text-on-surface">¿Y las categorías personales?</strong>
        Las categorías que cada usuario crea para sí mismo (por ejemplo "Mascota", "Regalos") no
        aparecen aquí. Solo se gestionan desde la cuenta del usuario y no se pueden desactivar
        desde el panel de administración.
      </p>
    </section>

  </div>
  </div>
</main>
@endsection
