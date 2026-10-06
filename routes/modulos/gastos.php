<?php

use Illuminate\Support\Facades\Route;
use App\Models\Gasto;
use App\Models\Categoria;

// 1. Vista principal
Route::get('/gastos', function () {
    $userId = auth()->id() ?? 1; // Toma el ID del usuario en sesión

    $gastos = Gasto::where('user_id', $userId)
        ->with('categoria')
        ->orderByDesc('fecha')
        ->paginate(15);

    $categorias = Categoria::all();

    return view('app.gastos.index', compact('gastos', 'categorias'));
})->name('gastos.index');

// 2. Guardar gasto
Route::post('/gastos', function (\Illuminate\Http\Request $request) {
    $datosValidados = $request->validate([
        'categoria_id' => 'required',
        'monto'        => 'required|numeric',
        'fecha'        => 'required|date',
        'descripcion'  => 'nullable|string',
    ]);

    $datosValidados['user_id'] = auth()->id() ?? 1;

    Gasto::create($datosValidados);

    return redirect()->route('gastos.index')->with('exito', 'Gasto registrado correctamente.');
})->name('gastos.store');