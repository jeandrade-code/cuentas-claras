<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Gastos - Cuentas Claras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-800">Mis Gastos</h1>

        <!-- Mensaje de éxito -->
        @if(session('exito'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('exito') }}
            </div>
        @endif

        <!-- Formulario para Registrar Gasto -->
        <form action="{{ route('gastos.store') }}" method="POST">
    @csrf
    
            <div>
                <label class="block text-sm font-medium text-gray-700">Categoría</label>
                <select name="categoria_id" class="w-full border p-2 rounded mt-1" required>
                    <option value="">Seleccione...</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }} ({{ ucfirst($cat->bloque) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Monto ($)</label>
                <input type="number" name="monto" min="1" class="w-full border p-2 rounded mt-1" placeholder="Ej: 50000" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Fecha</label>
                <input type="date" name="fecha" max="{{ date('Y-m-d') }}" class="w-full border p-2 rounded mt-1" required>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Guardar Gasto</button>
            </div>
        </form>

        <!-- Tabla de Gastos -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="p-3">Fecha</th>
                        <th class="p-3">Categoría</th>
                        <th class="p-3">Bloque</th>
                        <th class="p-3 text-right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gastos as $gasto)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3">{{ $gasto->fecha }}</td>
                            <td class="p-3 font-semibold">{{ $gasto->categoria->nombre ?? 'Sin categoría' }}</td>
                            <td class="p-3 text-sm text-gray-600">{{ ucfirst($gasto->categoria->bloque ?? 'N/A') }}</td>
                            <td class="p-3 text-right font-bold text-green-700">$ {{ number_format($gasto->monto, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">No hay gastos registrados aún.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $gastos->links() }}
        </div>
    </div>
</body>
</html>