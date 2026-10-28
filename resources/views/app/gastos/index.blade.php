@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <!-- ... (Mensajes de error y éxito) ... -->
        @if (session('exito'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-xl mb-6">
                {{ session('exito') }}
            </div>
        @endif

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


        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Gastos</h1>
                <p class="text-sm text-gray-500">Administra y visualiza todos tus egresos y consumos.</p>
            </div>
        </div>

<!-- Formulario para Registrar Nuevo Gasto -->
<div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm mb-8" data-tour="gastos-form">
    <h2 class="text-lg font-bold text-gray-800 mb-4">Registrar Nuevo Gasto</h2>

    <form id="formularioGasto"
    method="POST"
    action="{{ route('gastos.store') }}"
    class="grid grid-cols-1 md:grid-cols-2 gap-5">

        @csrf

        <!-- Elegir tipo de gasto -->
        <div class="md:col-span-4">
            <label class="block text-xs font-bold text-gray-600 mb-2">
                ¿Qué tipo de gasto vas a registrar?
            </label>

            <select id="tipo_gasto"
                class="w-full md:w-1/2 px-3 py-2 border border-gray-200 rounded-lg text-sm">

                <option value="normal">Gasto normal</option>
                <option value="fijo">Gasto fijo</option>
            </select>
        </div>

        <!-- Monto -->
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">
                Monto ($ COP)
            </label>

            <input type="text"
                name="monto"
                id="monto"
                inputmode="numeric"
                placeholder="Ej: 45.000"
                oninput="this.value = this.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">

            @error('monto')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <!-- Categoría: compartida por ambos tipos -->
        <div>
            <label class="block text-xs font-bold text-gray-600 mb-1">
                Categoría
            </label>

            <select name="categoria_id"
                required
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">

                <option value="">Selecciona categoría</option>

                @foreach ($categorias as $cat)
                    <option value="{{ $cat->id }}">
                        {{ $cat->nombre }}
                    </option>
                @endforeach
            </select>

            @error('categoria_id')
                <span class="text-xs text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <!-- Campos para gasto normal -->
       <div id="camposGastoNormal" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Fecha
                </label>

                <input type="date"
                    name="fecha"
                    value="{{ date('Y-m-d') }}"
                    required
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">

                @error('fecha')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Descripción
                </label>

                <input type="text"
                    name="descripcion"
                    placeholder="Ej: Compra de mercado"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
        </div>

        <!-- Campos adicionales para gasto fijo -->
        <div id="camposGastoFijo" class="hidden md:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Nombre del gasto fijo
                </label>

                <input type="text"
                    name="nombre"
                    placeholder="Ej: Internet, arriendo..."
                    disabled
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Día de vencimiento
                </label>

                <input type="number"
                    name="dia_vencimiento"
                    min="1"
                    max="31"
                    placeholder="Ej: 15"
                    disabled
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Periodicidad
                </label>

                <select name="periodicidad" disabled
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
                    <option value="mensual">Mensual</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-gray-600 mb-1">
                    Descripción (opcional)
                </label>

                <input type="text"
                    name="descripcion"
                    placeholder="Detalles del gasto fijo"
                    disabled
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm">
            </div>
        </div>

        <!-- Botón para guardar -->
        <div class="md:col-span-4 flex justify-end">
            <button type="submit" id="botonGuardarGasto"
                class="btn btn-primary">
                Guardar Gasto
            </button>
        </div>
    </form>
</div>

<script>
    const tipoGasto = document.getElementById('tipo_gasto');
    const formularioGasto = document.getElementById('formularioGasto');
    const camposNormal = document.getElementById('camposGastoNormal');
    const camposFijo = document.getElementById('camposGastoFijo');
    const botonGuardar = document.getElementById('botonGuardarGasto');

    function cambiarTipoGasto() {
        const esFijo = tipoGasto.value === 'fijo';

        formularioGasto.action = esFijo
            ? "{{ route('gastos-fijos.store') }}"
            : "{{ route('gastos.store') }}";

        camposNormal.classList.toggle('hidden', esFijo);
        camposFijo.classList.toggle('hidden', !esFijo);

        camposNormal.querySelectorAll('input').forEach(campo => {
            campo.disabled = esFijo;
            campo.required = !esFijo && campo.name === 'fecha';
        });

        camposFijo.querySelectorAll('input, select').forEach(campo => {
            campo.disabled = !esFijo;

            if (campo.name === 'nombre' || campo.name === 'dia_vencimiento') {
                campo.required = esFijo;
            }
        });

        botonGuardar.textContent = esFijo
            ? 'Guardar Gasto Fijo'
            : 'Guardar Gasto';
    }

    tipoGasto.addEventListener('change', cambiarTipoGasto);

    formularioGasto.addEventListener('submit', function () {
        const monto = document.getElementById('monto');
        monto.value = monto.value.replace(/\./g, '');
    });
</script>


        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8" data-tour="gastos-resumen">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">TOTAL GASTADO (ESTE MES)</p>
                <h3 class="text-2xl font-extrabold text-red-600 mt-1">$ {{ number_format($totalMes, 0, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Nº DE REGISTROS</p>
                <h3 class="text-2xl font-extrabold text-gray-800 mt-1">{{ $gastos->total() }}</h3>
            </div>
        </div>

        <!-- Tabla de Registros Reales -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" data-tour="gastos-tabla">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">

                            <th class="p-4">FECHA</th>
                            <th class="p-4">DESCRIPCIÓN</th>
                            <th class="p-4">CATEGORÍA</th>
                            <th class="p-4 text-right">MONTO</th>
                            <th class="p-4 text-center min-w-[180px]">ACCIONES</th>

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                        @forelse($gastos as $gasto)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 text-gray-500">
                                    {{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}
                                </td>
                                <td class="p-4 font-medium text-gray-900">
                                    {{ $gasto->descripcion ?? 'Sin descripción' }}
                                </td>
                                <td class="p-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $gasto->categoria->nombre ?? 'General' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right font-bold text-red-600">
                                    $ {{ number_format($gasto->monto, 0, ',', '.') }}
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-end gap-3">

                                        <a href="{{ route('gastos.edit', $gasto->id) }}"
                                            class="text-primary hover:opacity-80 text-xs font-semibold">
                                            Editar</a>
                                <form method="POST"
                                 action="{{ route('gastos.destroy', $gasto->id) }}"
                                 onsubmit="return confirm('¿Estás segura de que deseas eliminar este gasto? Esta acción no se puede deshacer.');">

                                     @csrf
                                     @method('DELETE')

                                <button type="submit"
                                class="text-error hover:opacity-80 text-xs font-semibold">
                                Eliminar
                                 </button>
                               </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">
                                    No hay gastos registrados en este periodo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $gastos->links() }}
            </div>
        </div>

    </div>
    <x-onboarding-tour name="gastos" :auto-start="true" />
@endsection
