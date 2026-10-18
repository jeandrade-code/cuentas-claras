@extends('layouts.app')

@section('titulo', 'Notificaciones · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/notificaciones.css') }}">
@endpush

@section('content')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
        @if (session('exito'))
        <div
            role="status"
            class="w-full max-w-7xl mx-auto mt-4 px-4 py-3 rounded-lg"
            style="background: var(--surface-container-lowest); color: var(--primary); border: 1px solid var(--outline-variant);"
        >
            {{ session('exito') }}
        </div>
    @endif
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">

        <header class="flex flex-col gap-2">
            <h1 class="font-headline-xl text-headline-xl text-on-surface">
                Notificaciones
            </h1>

            <p class="font-body-md text-body-md text-on-surface-variant">
                Consulta tus alertas y mantente al día con tus gastos y metas.
            </p>

            @if ($pendientes->total() > 0)
                <form action="{{ route('notificaciones.leerTodas') }}" method="POST">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg px-4 py-2 mt-3"
                        style="background: var(--primary); color: white;"
                    >
                        Marcar todas como leídas
                    </button>
                </form>
            @endif
        </header>

        {{-- NOTIFICACIONES PENDIENTES --}}
        <section class="card p-space-lg" aria-labelledby="titulo-pendientes">
            <h2
                id="titulo-pendientes"
                class="font-headline-md text-headline-md text-on-surface mb-space-md"
            >
                Pendientes
                <span>({{ $pendientes->total() }})</span>
            </h2>

            @forelse ($pendientes as $item)
                @php
                    $icono = match ($item->tipo) {
                        'por_vencer' => '⏰',
                        'vencido' => '⚠️',
                        'invitacion_meta',
                        'invitacion_meta_requiere_familiar' => '✉️',
                        'respuesta_meta' => '💬',
                        'meta_finalizada' => '🎉',
                        'meta_abandonada' => '📌',
                        default => '🔔',
                    };
                @endphp

               <article
                 class="notificacion-item notificacion-tipo-{{ $item->tipo }} mb-4 rounded-xl border p-4">
                    <div class="flex items-start gap-3">
                        <span aria-hidden="true" class="text-2xl">
                            {{ $icono }}
                        </span>

                        <div class="flex-1 min-w-0">
                            <p class="font-body-md text-on-surface">
                                {{ $item->mensaje }}
                            </p>

                            <p class="font-body-sm text-on-surface-variant mt-2">
                                {{ $item->created_at->locale('es')->diffForHumans() }}
                            </p>

                            <form
                                action="{{ route('notificaciones.leer') }}"
                                method="POST"
                                class="mt-3"
                            >
                                @csrf

                                <input
                                    type="hidden"
                                    name="notificacion_id"
                                    value="{{ $item->id }}"
                                >

                                <button
                                    type="submit"
                                    class="font-body-md underline"
                                    style="color: var(--primary);"
                                >
                                    Ver notificación
                                </button>
                            </form>
                        </div>

                        <span
                            class="notificacion-punto"
                            aria-label="No leída"
                            title="No leída"
                        >●</span>
                    </div>
                </article>
            @empty
                <p class="font-body-md text-on-surface-variant py-4">
                    No tienes notificaciones pendientes.
                </p>
            @endforelse

            @if ($pendientes->hasPages())
                <div class="mt-4">
                    {{ $pendientes->links() }}
                </div>
            @endif
        </section>

        {{-- NOTIFICACIONES ANTERIORES --}}
        <section class="card p-space-lg" aria-labelledby="titulo-anteriores">
            <h2
                id="titulo-anteriores"
                class="font-headline-md text-headline-md text-on-surface mb-space-md"
            >
                Anteriores
                <span>({{ $anteriores->total() }})</span>
            </h2>

            @forelse ($anteriores as $item)
                @php
                    $icono = match ($item->tipo) {
                        'por_vencer' => '⏰',
                        'vencido' => '⚠️',
                        'invitacion_meta',
                        'invitacion_meta_requiere_familiar' => '✉️',
                        'respuesta_meta' => '💬',
                        'meta_finalizada' => '🎉',
                        'meta_abandonada' => '📌',
                        default => '🔔',
                    };
                @endphp
               <article
    class="notificacion-item notificacion-tipo-{{ $item->tipo }} mb-4 rounded-xl border p-4"
    style="background: var(--surface-container-lowest); border-color: var(--outline-variant);"
>
                    <div class="flex items-start gap-3">
                        <span aria-hidden="true" class="text-2xl">
                            {{ $icono }}
                        </span>

                        <div class="flex-1 min-w-0">
                            <p class="font-body-md text-on-surface">
                                {{ $item->mensaje }}
                            </p>

                            <p class="font-body-sm text-on-surface-variant mt-2">
                                {{ $item->created_at->locale('es')->diffForHumans() }}
                            </p>

                            <span class="inline-block mt-2 font-body-sm text-on-surface-variant">
                                {{ $item->estado === 'leida' ? 'Leída' : 'Cancelada' }}
                            </span>
                        </div>
                    </div>
                </article>
            @empty
                <p class="font-body-md text-on-surface-variant py-4">
                    Sin notificaciones anteriores.
                </p>
            @endforelse

            @if ($anteriores->hasPages())
                <div class="mt-4">
                    {{ $anteriores->links() }}
                </div>
            @endif
        </section>

    </div>
</main>
@endsection
