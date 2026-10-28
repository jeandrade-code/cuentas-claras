@extends('layouts.app')

@section('titulo', 'Categorías · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/categorias.css') }}">
@endpush

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

        <header>
            <h1 class="font-headline-xl text-headline-xl text-on-surface">Categorías</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">Organiza tus gastos</p>
        </header>

        <section class="card p-space-lg" data-dinamico="categorias">
            <h2 class="font-headline-md text-headline-md text-on-surface mb-space-md">
                Mis categorías y las del sistema
            </h2>

            @if(session('exito'))
                <div class="categoria-exito" role="status">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                    <span>{{ session('exito') }}</span>
                </div>
            @endif

            {{-- FORMULARIO CREAR --}}
            <form method="POST" action="{{ route('categorias.store') }}" class="categoria-form-crear" data-tour="cat-form">
                @csrf

                <h3 class="font-headline-sm text-headline-sm text-on-surface mb-4">
                    Crear nueva categoría
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nombre" class="block font-label-md text-label-md text-on-surface-variant mb-1">
                            Nombre
                        </label>
                        <input type="text" id="nombre" name="nombre"
                               value="{{ old('nombre') }}" maxlength="50" required
                               placeholder="Ej: Gimnasio" class="input">
                        @error('nombre')
                            <span class="categoria-campo-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="bloque" class="block font-label-md text-label-md text-on-surface-variant mb-1">
                            Bloque
                        </label>
                        <select id="bloque" name="bloque" required class="input">
                            <option value="">Selecciona un bloque</option>
                            <option value="necesidades" {{ old('bloque') === 'necesidades' ? 'selected' : '' }}>Necesidades</option>
                            <option value="discrecionales" {{ old('bloque') === 'discrecionales' ? 'selected' : '' }}>Discrecionales</option>
                            <option value="ahorro" {{ old('bloque') === 'ahorro' ? 'selected' : '' }}>Ahorro</option>
                        </select>
                        @error('bloque')
                            <span class="categoria-campo-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- SELECTOR DE ICONOS (CREAR) --}}
                <div class="mt-4" data-tour="cat-iconos">
                    <label class="block font-label-md text-label-md text-on-surface-variant mb-2">
                        Icono de la categoría (opcional)
                    </label>
                    <div class="categoria-icono-selector">
                        <label class="categoria-icono-label" title="Icono automático">
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
                        <span class="categoria-campo-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn btn-primary categoria-boton-icono">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                            <path d="M12 5v14M5 12h14"/>
                        </svg>
                        <span>Crear categoría</span>
                    </button>
                </div>
            </form>

            {{-- FORMULARIO EDITAR --}}
            @if($categoriaEditar)
                <div class="categoria-form-editar">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface mb-4">
                        Editar categoría
                    </h3>

                    <form method="POST" action="{{ route('categorias.update', $categoriaEditar->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="editar_nombre" class="block font-label-md text-label-md text-on-surface-variant mb-1">
                                    Nombre
                                </label>
                                <input type="text" id="editar_nombre" name="nombre"
                                       value="{{ old('nombre', $categoriaEditar->nombre) }}"
                                       maxlength="50" required class="input">
                                @error('nombre')
                                    <p class="categoria-campo-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="editar_bloque" class="block font-label-md text-label-md text-on-surface-variant mb-1">
                                    Bloque
                                </label>
                                <select id="editar_bloque" name="bloque" required class="input">
                                    <option value="necesidades" {{ old('bloque', $categoriaEditar->bloque) === 'necesidades' ? 'selected' : '' }}>Necesidades</option>
                                    <option value="discrecionales" {{ old('bloque', $categoriaEditar->bloque) === 'discrecionales' ? 'selected' : '' }}>Discrecionales</option>
                                    <option value="ahorro" {{ old('bloque', $categoriaEditar->bloque) === 'ahorro' ? 'selected' : '' }}>Ahorro</option>
                                </select>
                                @error('bloque')
                                    <p class="categoria-campo-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- SELECTOR DE ICONOS (EDITAR) --}}
                        <div class="mb-6">
                            <label class="block font-label-md text-label-md text-on-surface-variant mb-2">
                                Icono de la categoría (opcional)
                            </label>
                            <div class="categoria-icono-selector">
                                <label class="categoria-icono-label" title="Icono automático">
                                    <input type="radio" name="icono" value="" class="sr-only"
                                           {{ old('icono', $categoriaEditar->icono) === '' || old('icono', $categoriaEditar->icono) === null ? 'checked' : '' }}>
                                    <span class="material-symbols-outlined text-[20px]">magic_button</span>
                                </label>

                                @foreach(\App\Models\Categoria::ICONOS as $key => $label)
                                    <label class="categoria-icono-label" title="{{ $label }}">
                                        <input type="radio" name="icono" value="{{ $key }}" class="sr-only"
                                               {{ old('icono', $categoriaEditar->icono) === $key ? 'checked' : '' }}>
                                        <x-categoria-icono :icono="$key" :size="20" />
                                    </label>
                                @endforeach
                            </div>
                            @error('icono')
                                <p class="categoria-campo-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="submit" class="btn btn-primary categoria-boton-icono">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>Guardar cambios</span>
                            </button>

                            <a href="{{ route('categorias.index') }}" class="btn btn-secondary categoria-boton-icono">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                                <span>Cancelar</span>
                            </a>
                        </div>
                    </form>
                </div>
            @endif

            {{-- LISTADO DE CATEGORÍAS --}}
            <div data-tour="cat-lista">
            @forelse(($categorias ?? []) as $item)
                <div class="categoria-item">
                    <div class="categoria-item__info">
                        <div class="categoria-item__titulo">
                            <x-categoria-icono :categoria="$item" :size="20" class="categoria-item__icono" />

                            <h3 class="font-label-md text-label-md font-semibold text-on-surface">
                                {{ $item->nombre }}
                            </h3>

                            @if($item->esDelSistema())
                                <span class="categoria-badge-sistema">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" width="12" height="12" aria-hidden="true">
                                        <rect x="4" y="11" width="16" height="10" rx="2"/>
                                        <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                                    </svg>
                                    <span>Sistema</span>
                                </span>
                            @endif
                        </div>

                        <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                            Bloque: {{ ucfirst($item->bloque) }}
                        </p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            {{ $item->gastos_count }} gasto(s)
                        </p>
                    </div>

                    @if(!$item->esDelSistema())
                        <div class="categoria-item__acciones">
                            <a href="{{ route('categorias.index', ['editar' => $item->id]) }}"
                               class="categoria-accion-link categoria-accion-link--editar">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round" width="16" height="16" aria-hidden="true">
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                </svg>
                                <span>Editar</span>
                            </a>

                            @if($item->gastos_count > 0)
                                <button type="button"
                                        onclick="document.getElementById('modal-eliminar-{{ $item->id }}').classList.remove('hidden')"
                                        class="categoria-accion-link categoria-accion-link--eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                         stroke-linejoin="round" width="16" height="16" aria-hidden="true">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                    </svg>
                                    <span>Eliminar</span>
                                </button>

                                {{-- Modal de traslado de gastos --}}
                                <div id="modal-eliminar-{{ $item->id }}" class="categoria-modal hidden">
                                    <div class="categoria-modal__caja">
                                        <h3 class="font-headline-sm text-headline-sm text-on-surface mb-3">
                                            Eliminar categoría
                                        </h3>

                                        <div class="categoria-modal__aviso">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                 stroke-linejoin="round" width="20" height="20" aria-hidden="true">
                                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                                <line x1="12" y1="9" x2="12" y2="13"/>
                                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                                            </svg>
                                            <span>
                                                Esta categoría tiene {{ $item->gastos_count }} gasto(s).
                                                Selecciona dónde quieres trasladarlos antes de eliminarla.
                                            </span>
                                        </div>

                                        <form method="POST" action="{{ route('categorias.destroy', $item->id) }}">
                                            @csrf
                                            @method('DELETE')

                                            <label for="destino-{{ $item->id }}"
                                                   class="block font-label-md text-label-md text-on-surface-variant mb-2">
                                                Categoría de destino
                                            </label>

                                            <select id="destino-{{ $item->id }}"
                                                    name="categoria_destino_id" required class="input mb-5">
                                                <option value="">Selecciona una categoría</option>
                                                @foreach($categorias as $destino)
                                                    @if($destino->id !== $item->id)
                                                        <option value="{{ $destino->id }}">
                                                            {{ $destino->nombre }} {{ $destino->esDelSistema() ? '(Sistema)' : '' }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>

                                            @error('categoria_destino_id')
                                                <p class="categoria-campo-error mb-3">{{ $message }}</p>
                                            @enderror

                                            <div class="flex flex-wrap justify-end gap-3">
                                                <button type="button"
                                                        onclick="document.getElementById('modal-eliminar-{{ $item->id }}').classList.add('hidden')"
                                                        class="btn btn-secondary categoria-boton-icono">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                         stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                                    </svg>
                                                    <span>Cancelar</span>
                                                </button>

                                                <button type="submit" class="btn btn-danger categoria-boton-icono">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                         stroke-linejoin="round" width="18" height="18" aria-hidden="true">
                                                        <path d="M5 12h14M12 5l7 7-7 7"/>
                                                    </svg>
                                                    <span>Mover y eliminar</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                {{-- Sin gastos: eliminar directo --}}
                                <form method="POST" action="{{ route('categorias.destroy', $item->id) }}"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?');"
                                      class="inline-flex">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="categoria-accion-link categoria-accion-link--eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                             stroke-linejoin="round" width="16" height="16" aria-hidden="true">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6"/>
                                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                        </svg>
                                        <span>Eliminar</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <p class="font-body-md text-body-md text-on-surface-variant">
                    Aún no tienes categorías propias.
                </p>
            @endforelse
            </div>
        </section>
    </div>
</main>
{{-- ONBOARDING TOUR --}}
<x-onboarding-tour name="categorias" :auto-start="true" />
@endsection