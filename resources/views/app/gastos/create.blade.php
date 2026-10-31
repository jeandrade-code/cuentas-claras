@extends('layouts.app')

@section('content')

@php
    // Si hubo errores de validación en un gasto fijo, o se llega con ?tipo=fijo,
    // la pantalla abre directamente en modo "Gasto fijo".
    $esFijo = old('dia_vencimiento') !== null
        || old('nombre') !== null
        || request('tipo') === 'fijo';
@endphp

<div class="container mx-auto px-4 py-6">

    <div class="max-w-2xl mx-auto">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Nuevo Gasto
            </h1>

            <p class="text-sm text-gray-500">
                Registra un gasto normal o un gasto fijo mensual.
            </p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
                <p class="font-bold">No se pudo guardar el gasto:</p>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">

            <form
                id="formularioGasto"
                method="POST"
                action="{{ $esFijo ? route('gastos-fijos.store') : route('gastos.store') }}"
            >

                @csrf

                <div class="space-y-5">

                    {{-- Tipo de gasto (solo cambia la pantalla y la ruta de envío) --}}
                    <div data-tour="nuevo-tipo">
                        <label for="tipo_gasto" class="block text-xs font-bold text-gray-600 mb-1">
                            ¿Qué tipo de gasto vas a registrar?
                        </label>

                        <select
                            id="tipo_gasto"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >
                            <option value="normal" @selected(! $esFijo)>Gasto normal</option>
                            <option value="fijo" @selected($esFijo)>Gasto fijo (se repite cada mes)</option>
                        </select>
                    </div>

                    {{-- Monto (común) --}}
                    <div data-tour="nuevo-monto">
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Monto ($ COP)
                        </label>

                        <input
                            type="text"
                            name="monto"
                            id="monto"
                            inputmode="numeric"
                            value="{{ old('monto') }}"
                            placeholder="Ej: 45.000"
                            oninput="this.value = this.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >

                        @error('monto')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- Categoría (común) --}}
                    <div data-tour="nuevo-categoria">
                        <label class="block text-xs font-bold text-gray-600 mb-1">
                            Categoría
                        </label>

                        <select
                            name="categoria_id"
                            required
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                        >
                            <option value="">Selecciona categoría</option>

                            @foreach($categorias as $cat)
                                <option
                                    value="{{ $cat->id }}"
                                    {{ old('categoria_id') == $cat->id ? 'selected' : '' }}
                                >
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach

                        </select>

                        @error('categoria_id')
                            <span class="text-xs text-red-500">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div data-tour="nuevo-campos">

                    {{-- ===== Campos del GASTO NORMAL ===== --}}
                    <div id="camposGastoNormal" class="space-y-5 {{ $esFijo ? 'hidden' : '' }}">

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">
                                Fecha
                            </label>

                            <input
                                type="date"
                                name="fecha"
                                value="{{ old('fecha', date('Y-m-d')) }}"
                                @required(! $esFijo)
                                @disabled($esFijo)
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                            >

                            @error('fecha')
                                <span class="text-xs text-red-500">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">
                                Descripción
                            </label>

                            <input
                                type="text"
                                name="descripcion"
                                value="{{ $esFijo ? '' : old('descripcion') }}"
                                maxlength="200"
                                placeholder="Ej: Compra de mercado"
                                @disabled($esFijo)
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                            >

                            @if (! $esFijo)
                                @error('descripcion')
                                    <span class="text-xs text-red-500">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @endif
                        </div>

                    </div>

                    {{-- ===== Campos del GASTO FIJO ===== --}}
                    <div id="camposGastoFijo" class="space-y-5 {{ $esFijo ? '' : 'hidden' }}">

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">
                                Nombre del gasto fijo
                            </label>

                            <input
                                type="text"
                                name="nombre"
                                value="{{ old('nombre') }}"
                                maxlength="100"
                                placeholder="Ej: Internet, arriendo..."
                                @required($esFijo)
                                @disabled(! $esFijo)
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                            >

                            @error('nombre')
                                <span class="text-xs text-red-500">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">
                                Día de vencimiento
                            </label>

                            <input
                                type="number"
                                name="dia_vencimiento"
                                min="1"
                                max="31"
                                value="{{ old('dia_vencimiento') }}"
                                placeholder="Del 1 al 31"
                                @required($esFijo)
                                @disabled(! $esFijo)
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                            >

                            @error('dia_vencimiento')
                                <span class="text-xs text-red-500">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <input
                            type="hidden"
                            name="periodicidad"
                            value="mensual"
                            @disabled(! $esFijo)
                        >

                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">
                                Descripción (opcional)
                            </label>

                            <input
                                type="text"
                                name="descripcion"
                                value="{{ $esFijo ? old('descripcion') : '' }}"
                                maxlength="100"
                                placeholder="Detalles del gasto fijo"
                                @disabled(! $esFijo)
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm"
                            >

                            @if ($esFijo)
                                @error('descripcion')
                                    <span class="text-xs text-red-500">
                                        {{ $message }}
                                    </span>
                                @enderror
                            @endif
                        </div>

                    </div>

                    </div>{{-- /nuevo-campos --}}

                </div>

                <div class="flex justify-end gap-3 mt-6" data-tour="nuevo-guardar">

                    <a href="{{ $esFijo ? route('gastos-fijos.index') : route('gastos.index') }}"
                       id="enlaceCancelar"
                       class="btn btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit" id="botonGuardarGasto" class="btn btn-primary">
                        {{ $esFijo ? 'Guardar Gasto Fijo' : 'Guardar Gasto' }}
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script>
    (function () {
        const tipoGasto = document.getElementById('tipo_gasto');
        const formulario = document.getElementById('formularioGasto');
        const camposNormal = document.getElementById('camposGastoNormal');
        const camposFijo = document.getElementById('camposGastoFijo');
        const botonGuardar = document.getElementById('botonGuardarGasto');
        const enlaceCancelar = document.getElementById('enlaceCancelar');

        const rutas = {
            normal: {
                action: "{{ route('gastos.store') }}",
                cancelar: "{{ route('gastos.index') }}",
                boton: 'Guardar Gasto'
            },
            fijo: {
                action: "{{ route('gastos-fijos.store') }}",
                cancelar: "{{ route('gastos-fijos.index') }}",
                boton: 'Guardar Gasto Fijo'
            }
        };

        function cambiarTipoGasto() {
            const esFijo = tipoGasto.value === 'fijo';
            const cfg = esFijo ? rutas.fijo : rutas.normal;

            formulario.action = cfg.action;
            enlaceCancelar.href = cfg.cancelar;
            botonGuardar.textContent = cfg.boton;

            camposNormal.classList.toggle('hidden', esFijo);
            camposFijo.classList.toggle('hidden', !esFijo);

            // Los campos ocultos se deshabilitan para que no se envíen
            // (así no hay dos "descripcion" en la misma petición).
            camposNormal.querySelectorAll('input, select').forEach(campo => {
                campo.disabled = esFijo;
                campo.required = !esFijo && campo.name === 'fecha';
            });

            camposFijo.querySelectorAll('input, select').forEach(campo => {
                campo.disabled = !esFijo;
                campo.required = esFijo && (campo.name === 'nombre' || campo.name === 'dia_vencimiento');
            });
        }

        tipoGasto.addEventListener('change', cambiarTipoGasto);

        // El monto se muestra con puntos (45.000) pero se envía como entero (45000).
        formulario.addEventListener('submit', function () {
            const monto = document.getElementById('monto');
            monto.value = monto.value.replace(/\./g, '');
        });
    })();
</script>

<x-onboarding-tour name="gastos-nuevo" :auto-start="true" />

@endsection