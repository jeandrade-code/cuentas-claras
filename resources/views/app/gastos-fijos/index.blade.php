@extends('layouts.app')

@section('titulo', 'Gastos fijos y vencimientos · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gastos-fijos.css') }}">
@endpush

@section('content')
    <main class="gastos-fijos-page">
        <div class="gastos-fijos-container">
            <header class="gf-header">
                <div>
                    <p class="gf-eyebrow">CUENTAS CLARAS</p>
                    <h1>Gastos fijos y vencimientos</h1>
                    <p>Controla tus pagos recurrentes y mantente al día.</p>
                </div>
            </header>

            @if (session('exito'))
                <div class="gf-alert gf-alert-success" role="status">
                    {{ session('exito') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="gf-alert gf-alert-error" role="alert">
                    <strong>Revisa los siguientes datos:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- RESUMEN --}}
            <section class="gf-section" data-tour="gf-resumen">
                <div class="gf-section-heading">
                    <div>
                        <h2>Resumen del mes</h2>
                        <p>Estado de tus pagos registrados.</p>
                    </div>
                    <span class="gf-period">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $periodo ?? now()->format('Y-m'))->locale('es')->translatedFormat('F Y') }}
                    </span>
                </div>
                <div class="gf-summary-grid">
                    @foreach ($resumen as $item)
                        <article class="gf-summary-card gf-summary-{{ $item['estado'] }}">
                            <span class="gf-summary-label">{{ $item['titulo'] }}</span>
                            <strong class="gf-summary-number">{{ $item['cantidad'] }}</strong>
                            <span class="gf-summary-caption">
                                {{ $item['cantidad'] === 1 ? 'gasto' : 'gastos' }}
                            </span>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- FORMULARIO PARA CREAR --}}
            <section class="gf-section" id="crear-gasto" data-tour="gf-form">
                <div class="gf-section-heading">
                    <div>
                        <h2>Agregar gasto fijo</h2>
                        <p>Registra un pago que se repite cada mes.</p>
                    </div>
                </div>
                <form action="{{ route('gastos-fijos.store') }}" method="POST" class="gf-form">
                    @csrf
                    <div class="gf-form-grid">
                        <div class="gf-field">
                            <label for="crear_nombre">Nombre del gasto</label>
                            <input id="crear_nombre" type="text" name="nombre" maxlength="100" value="{{ old('nombre') }}"
                                placeholder="Ej. Arriendo" required>
                            @error('nombre')
                                <small class="gf-field-error">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="gf-field">
                            <label for="crear_monto">Monto mensual (COP)</label>
                            <input id="crear_monto" type="number" name="monto" min="1" step="1" value="{{ old('monto') }}"
                                placeholder="Ej. 800000" required>
                            @error('monto')
                                <small class="gf-field-error">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="gf-field">
                            <label for="crear_dia">Día de vencimiento</label>
                            <input id="crear_dia" type="number" name="dia_vencimiento" min="1" max="31"
                                value="{{ old('dia_vencimiento') }}" placeholder="Del 1 al 31" required>
                            @error('dia_vencimiento')
                                <small class="gf-field-error">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="gf-field">
                            <label for="crear_categoria">Categoría</label>
                            <select id="crear_categoria" name="categoria_id" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" @selected((string) old('categoria_id') === (string) $categoria->id)>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <small class="gf-field-error">{{ $message }}</small>
                            @enderror
                        </div>
                        <input type="hidden" name="periodicidad" value="mensual">
                        <div class="gf-field gf-field-full">
                            <label for="crear_descripcion">Descripción (opcional)</label>
                            <input id="crear_descripcion" type="text" name="descripcion" maxlength="100"
                                value="{{ old('descripcion') }}" placeholder="Agrega una nota si lo necesitas">
                            @error('descripcion')
                                <small class="gf-field-error">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <button class="gf-button gf-button-primary gf-button-icono" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"
                            aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                        <span>Agregar gasto fijo</span>
                    </button>
                </form>
            </section>

            {{-- LISTA DE GASTOS --}}
            <section class="gf-section" data-tour="gf-lista">
                <div class="gf-section-heading">
                    <div>
                        <h2>Mis gastos fijos</h2>
                        <p>Consulta los vencimientos y administra tus pagos.</p>
                    </div>
                    <span class="gf-count">{{ $gastosFijos->count() }} en total</span>
                </div>

                @forelse ($gastosFijos as $item)
                    @php
                        $pago = $item->pagos->first();
                        $fechaBase = \Carbon\Carbon::createFromFormat(
                            'Y-m',
                            $periodo ?? now()->format('Y-m')
                        )->startOfMonth();

                        $diaReal = min(
                            (int) $item->dia_vencimiento,
                            $fechaBase->daysInMonth
                        );

                        $fechaVencimiento = $fechaBase->copy()->day($diaReal);

                        if (!$item->activo) {
                            $estado = 'inactivo';
                            $textoEstado = 'Inactivo';
                        } elseif ($pago && $pago->estado === 'pagado') {
                            $estado = 'pagado';
                            $textoEstado = 'Pagado';
                        } elseif ($pago && $pago->estado === 'vencido') {
                            $estado = 'vencido';
                            $textoEstado = 'Vencido';
                        } elseif ($fechaVencimiento->lt(\Carbon\Carbon::today())) {
                            $estado = 'vencido';
                            $textoEstado = 'Vencido';
                        } else {
                            $estado = 'pendiente';
                            $textoEstado = 'Pendiente';
                        }

                        $idFormulario = 'editar-gasto-' . $item->id;
                        $bloque = $item->categoria->bloque ?? 'necesidades';
                    @endphp

                    <article class="gf-expense-card">

                        {{-- Fila principal: icono + info + monto + vencimiento + estado --}}
                        <div class="gf-expense-row">

                            {{-- Icono de categoría --}}
                            <span class="gf-expense-icon gf-expense-icon--{{ $bloque }}">
                                <x-categoria-icono :categoria="$item->categoria" :size="18" />
                            </span>

                            {{-- Info principal --}}
                            <div class="gf-expense-info">
                                <h3>
                                    {{ $item->nombre }}
                                    @if ($item->descripcion)
                                        <span class="gf-expense-desc" title="{{ $item->descripcion }}">·
                                            {{ $item->descripcion }}</span>
                                    @endif
                                </h3>
                                <div class="gf-expense-meta">
                                    <span class="gf-category-label">{{ $item->categoria->nombre ?? 'Sin categoría' }}</span>
                                    <span class="gf-meta-sep">·</span>
                                    <span class="gf-meta-item">
                                        <strong>${{ number_format($item->monto, 0, ',', '.') }}</strong>
                                    </span>
                                    <span class="gf-meta-sep">·</span>
                                    <span class="gf-meta-item">Vence {{ $fechaVencimiento->format('d/m/Y') }}</span>
                                </div>
                            </div>

                            {{-- Estado --}}
                            <span class="gf-status gf-status-{{ $estado }}">
                                {{ $textoEstado }}
                            </span>

                            {{-- Acciones --}}
                            <div class="gf-expense-actions">
                                @if ($item->activo && $estado !== 'pagado')
                                    <form action="{{ route('gastos-fijos.pagar', $item) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="gf-button gf-button-primary gf-button-icono"
                                            title="Marcar como pagado">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round" width="14" height="14" aria-hidden="true">
                                                <polyline points="20 6 9 17 4 12" />
                                            </svg>
                                            <span>Pagar</span>
                                        </button>
                                    </form>
                                @endif

                                <button type="button" class="gf-button gf-button-secondary gf-button-icono"
                                    onclick="document.getElementById('{{ $idFormulario }}').hidden = !document.getElementById('{{ $idFormulario }}').hidden"
                                    title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        width="14" height="14" aria-hidden="true">
                                        <path d="M12 20h9" />
                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                    </svg>
                                    <span>Editar</span>
                                </button>

                                <form action="{{ route('gastos-fijos.destroy', $item) }}" method="POST"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este gasto fijo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="gf-button gf-button-danger gf-button-icono" title="Eliminar">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" width="14" height="14" aria-hidden="true">
                                            <polyline points="3 6 5 6 21 6" />
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                                            <path d="M10 11v6M14 11v6" />
                                        </svg>
                                        <span>Eliminar</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Formulario de edición (oculto por defecto) --}}
                        <form id="{{ $idFormulario }}" action="{{ route('gastos-fijos.update', $item) }}" method="POST"
                            class="gf-form gf-edit-form" hidden>
                            @csrf
                            @method('PUT')

                            <h4>Editar {{ $item->nombre }}</h4>

                            <div class="gf-form-grid">
                                <div class="gf-field">
                                    <label for="nombre_{{ $item->id }}">Nombre</label>
                                    <input id="nombre_{{ $item->id }}" type="text" name="nombre" maxlength="100"
                                        value="{{ $item->nombre }}" required>
                                </div>

                                <div class="gf-field">
                                    <label for="monto_{{ $item->id }}">Monto mensual (COP)</label>
                                    <input id="monto_{{ $item->id }}" type="number" name="monto" min="1" step="1"
                                        value="{{ $item->monto }}" required>
                                </div>

                                <div class="gf-field">
                                    <label for="dia_{{ $item->id }}">Día de vencimiento</label>
                                    <input id="dia_{{ $item->id }}" type="number" name="dia_vencimiento" min="1" max="31"
                                        value="{{ $item->dia_vencimiento }}" required>
                                </div>

                                <div class="gf-field">
                                    <label for="categoria_{{ $item->id }}">Categoría</label>
                                    <select id="categoria_{{ $item->id }}" name="categoria_id" required>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}" @selected((int) $item->categoria_id === (int) $categoria->id)>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <input type="hidden" name="periodicidad" value="mensual">

                                <div class="gf-field gf-field-full">
                                    <label for="descripcion_{{ $item->id }}">Descripción (opcional)</label>
                                    <input id="descripcion_{{ $item->id }}" type="text" name="descripcion" maxlength="100"
                                        value="{{ $item->descripcion }}">
                                </div>

                                <div class="gf-field gf-field-full gf-checkbox-field">
                                    <label>
                                        <input type="checkbox" name="activo" value="1" @checked($item->activo)>
                                        Gasto fijo activo
                                    </label>
                                </div>
                            </div>

                            <div class="gf-expense-actions">
                                <button type="submit" class="gf-button gf-button-primary">
                                    Guardar cambios
                                </button>

                                <button type="button" class="gf-button gf-button-secondary"
                                    onclick="document.getElementById('{{ $idFormulario }}').hidden = true">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </article>
                @empty
                    <div class="gf-empty-state">
                        <span class="gf-empty-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                                <path d="M8 16h.01M12 16h.01M16 16h.01" />
                            </svg>
                        </span>
                        <h3>Aún no tienes gastos fijos</h3>
                        <p>Agrega tu primer gasto fijo en el formulario de arriba.</p>
                    </div>
                @endforelse
            </section>

        </div>
    </main>
    <x-onboarding-tour name="gastos-fijos" :auto-start="true" />
@endsection