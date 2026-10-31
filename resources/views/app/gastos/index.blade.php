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

            <div class="flex items-center gap-3">
                <div class="sm:hidden">
                    <a href="{{ route('gastos.create') }}" class="btn btn-primary">+ Nuevo gasto</a>
                </div>
                <a href="{{ route('gastos-fijos.index') }}" class="btn btn-secondary">
                    Ver gastos fijos
                </a>
            </div>
        </div>

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