@extends('layouts.app')

@section('titulo', 'Ajustes · Perfil · Cuentas Claras')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ajustes.css') }}">
@endpush

@section('content')
@php($esAdmin = $usuario->rol === 'administrador')
<main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="w-full max-w-7xl mx-auto py-space-lg flex flex-col gap-space-lg">
        <header>
            <h1 class="font-headline-xl text-headline-xl text-on-surface">
                Ajustes · Perfil
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                @if($esAdmin)
                    Actualiza tus datos personales y tu contraseña.
                @else
                    Actualiza tus datos personales, tu ingreso mensual y tu contraseña.
                @endif
            </p>
        </header>

        @include('app.ajustes._tabs')

        {{-- Aviso: Cambio de correo pendiente --}}
        @if ($usuario->pending_email)
            <div class="ajustes-aviso" role="alert">
                <span class="material-symbols-outlined text-[20px]">forward_to_inbox</span>
                <div>
                    <strong>Tienes un cambio de correo pendiente.</strong>
                    Hemos enviado un enlace de confirmación a
                    <strong>{{ $usuario->pending_email }}</strong>.
                    Revisa tu bandeja de entrada (o la carpeta de spam) para verificar el
                    nuevo correo. Tu correo actual seguirá siendo
                    <strong>{{ $usuario->email }}</strong> hasta que confirmes el cambio.

                    <form method="POST" action="{{ route('ajustes.confirmar-correo.reenviar') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            Reenviar correo de confirmación
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Mensajes globales --}}
        @if (session('exito'))
            <div class="ajustes-exito" role="status">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                <span>{{ session('exito') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="ajustes-error" role="alert">
                <span class="ajustes-error__titulo">Revisa los siguientes datos:</span>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="card p-space-lg">
            <form method="POST" action="{{ route('ajustes.perfil.update') }}" class="ajustes-form"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Datos del perfil --}}
                <div class="ajustes-seccion-header">
                    <h2 class="ajustes-seccion-titulo">Datos del perfil</h2>
                    <p class="ajustes-seccion-desc">
                        Tu nombre y correo aparecen en toda la aplicación.
                        Si cambias el correo, te pediremos tu contraseña actual y te
                        enviaremos un enlace de confirmación.
                    </p>
                </div>

                <div class="ajustes-form__grid ajustes-form__grid--2" style="align-items: start;">

                    {{-- Nombre completo con avatar al inicio (clicable para cambiar foto) --}}
                    <div class="ajustes-campo">
                        <div class="ajustes-nombre-avatar-row">

                            {{-- Avatar redondo clicable --}}
                            <label for="foto_perfil" class="ajustes-avatar-uploader" title="Cambiar ícono de perfil">
                                <span class="ajustes-avatar-uploader__avatar" data-avatar-preview>
                                    <x-user-avatar :user="$usuario" :size="72" />
                                </span>
                                <span class="ajustes-avatar-uploader__overlay" aria-hidden="true">
                                    <span class="material-symbols-outlined">photo_camera</span>
                                    <span class="ajustes-avatar-uploader__texto">Cambiar ícono</span>
                                </span>
                            </label>

                            {{-- Input file oculto, activado por el label --}}
                            <input type="file" name="foto_perfil" id="foto_perfil"
                                accept="image/jpeg,image/png,image/webp" class="ajustes-avatar-input">

                            {{-- Columna derecha: label + input del nombre --}}
                            <div class="ajustes-nombre-avatar-campos">
                                <label for="nombre" class="ajustes-campo__label">Nombre completo</label>
                                <input type="text" id="nombre" name="nombre"
                                    value="{{ old('nombre', $usuario->nombre) }}" maxlength="100" required
                                    autocomplete="name" class="ajustes-campo__input">
                            </div>

                        </div>

                        @error('nombre')
                            <p class="ajustes-campo__error">{{ $message }}</p>
                        @enderror
                        @error('foto_perfil')
                            <p class="ajustes-campo__error">{{ $message }}</p>
                        @enderror

                        @if ($usuario->foto_perfil)
                            <label class="ajustes-avatar-eliminar">
                                <input type="checkbox" name="eliminar_foto" value="1">
                                <span>Quitar foto actual</span>
                            </label>
                        @endif
                    </div>

                    {{-- Correo electrónico --}}
                    <div class="ajustes-campo">
                        <label for="email" class="ajustes-campo__label">Correo electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $usuario->email) }}"
                            maxlength="150" required autocomplete="email" class="ajustes-campo__input">
                        @error('email')
                            <p class="ajustes-campo__error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ingreso mensual (solo no-admin) --}}
                    @if(!$esAdmin)
                        <div class="ajustes-campo ajustes-campo--full">
                            <label for="ingreso_mensual" class="ajustes-campo__label">Ingreso mensual</label>
                            <div class="ajustes-campo__sufijo">
                                <input type="text" inputmode="numeric" id="ingreso_mensual" name="ingreso_mensual"
                                    value="{{ old('ingreso_mensual', $usuario->ingreso_mensual !== null ? number_format($usuario->ingreso_mensual, 0, ',', '.') : '') }}"
                                    autocomplete="off" placeholder="Ej: 3.500.000" class="ajustes-campo__input">
                                <span class="ajustes-campo__sufijo-texto">COP</span>
                            </div>
                            <p class="ajustes-campo__hint">
                                Se usa en el simulador de créditos y en el informe mensual.
                                Déjalo vacío si aún no lo tienes.
                            </p>
                            @error('ingreso_mensual')
                                <p class="ajustes-campo__error">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif

                </div>

                <hr class="ajustes-separador">

                {{-- Cambiar contraseña --}}
                <div class="ajustes-seccion-header">
                    <h2 class="ajustes-seccion-titulo">Cambiar contraseña</h2>
                    <p class="ajustes-seccion-desc">
                        Si no deseas cambiarla, deja estos tres campos vacíos.
                        Si vas a cambiar el correo, también necesitamos tu contraseña actual
                        como medida de seguridad.
                    </p>
                </div>

                <div class="ajustes-form__grid">
                    <div class="ajustes-campo">
                        <label for="contrasena_actual" class="ajustes-campo__label">
                            Contraseña actual
                        </label>
                        <input type="password" id="contrasena_actual" name="contrasena_actual"
                            autocomplete="current-password" class="ajustes-campo__input">
                        @error('contrasena_actual')
                            <p class="ajustes-campo__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="ajustes-campo">
                        <label for="nueva_contrasena" class="ajustes-campo__label">
                            Nueva contraseña
                        </label>
                        <input type="password" id="nueva_contrasena" name="nueva_contrasena" autocomplete="new-password"
                            class="ajustes-campo__input">
                        <p class="ajustes-campo__hint">Mínimo 8 caracteres.</p>
                        @error('nueva_contrasena')
                            <p class="ajustes-campo__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="ajustes-campo">
                        <label for="nueva_contrasena_confirmation" class="ajustes-campo__label">
                            Confirmar nueva contraseña
                        </label>
                        <input type="password" id="nueva_contrasena_confirmation" name="nueva_contrasena_confirmation"
                            autocomplete="new-password" class="ajustes-campo__input">
                    </div>
                </div>

                <div class="ajustes-acciones">
                    <button type="submit" class="btn btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </section>

        {{-- TOUR: tarjeta para relanzar el recorrido guiado cuando el usuario quiera. --}}
        <section class="card p-space-lg" id="tour-section">
            <div class="flex items-center gap-space-md"> <!-- <-- AQUÍ EL CAMBIO -->
                <div class="p-2.5 rounded-xl bg-surface-container text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">explore</span>
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface mb-1" id="tour-title">
                        Recorrido guiado
                    </h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mb-space-md" id="tour-desc">
                        Vuelve a ver el tour que te enseña cada sección de la plataforma cuando quieras.
                    </p>
                    
                    <button type="button" id="btn-reset-tours"
                        class="inline-flex items-center gap-2 px-space-md py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:opacity-95 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                        <span id="btn-tour-text">Reiniciar todos los tours</span>
                    </button>
                </div>
            </div>
        </section>
    </div>
</main>

{{-- TOUR: carga CSS/JS del motor --}}
<x-onboarding-tour :name="$esAdmin ? 'admin-panel' : 'dashboard'" :auto-start="false" />
@endsection

@push('scripts')
    <script>
        (function () {
            /* ── Lógica del formulario ─────────────────────────────────── */
            var ingreso = document.getElementById('ingreso_mensual');
            if (ingreso) {
                ingreso.addEventListener('input', function () {
                    var valor = this.value.replace(/\D/g, '');
                    this.value = valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                });
            }

            var fotoInput = document.getElementById('foto_perfil');
            var preview = document.querySelector('[data-avatar-preview]');
            if (fotoInput && preview) {
                var TIPOS_OK = ['image/jpeg', 'image/png', 'image/webp'];
                var MAX_BYTES = 2 * 1024 * 1024;

                fotoInput.addEventListener('change', function () {
                    var file = this.files && this.files[0];
                    if (!file) return;
                    if (TIPOS_OK.indexOf(file.type) === -1) return;
                    if (file.size > MAX_BYTES) return;

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        preview.innerHTML =
                            '<img src="' + e.target.result + '" alt="Vista previa" ' +
                            'style="width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block;">';
                    };
                    reader.readAsDataURL(file);
                });
            }

            /* ── Lógica para reiniciar TODOS los tours ────────────────── */
            var btnReset = document.getElementById('btn-reset-tours');
            var btnText = document.getElementById('btn-tour-text');
            var tourDesc = document.getElementById('tour-desc');

            // Función para verificar si hay tours pendientes (no vistos)
            function checkPendingTours() {
                if (typeof CCOnboarding === 'undefined' || !CCOnboarding.isDone) return;

                // Listas de tours según el rol
                var toursUsuario = ['dashboard', 'gastos', 'gastos-fijos', 'simulador', 'metas', 'informes', 'categorias'];
                var toursAdmin = ['admin-panel', 'admin-usuarios', 'admin-categorias', 'admin-auditoria', 'admin-estadisticas'];
                
                // Seleccionamos la lista correcta
                var toursLista = "{{ $esAdmin }}" === "1" ? toursAdmin : toursUsuario;
                var pendientes = 0;

                toursLista.forEach(function (tour) {
                    if (!CCOnboarding.isDone(tour)) {
                        pendientes++;
                    }
                });

                // Si hay tours pendientes, le avisamos visualmente al usuario
                if (pendientes > 0) {
                    btnText.textContent = 'Ver tours pendientes (' + pendientes + ')';
                    btnReset.classList.add('ring-2', 'ring-offset-2', 'ring-primary');
                    tourDesc.innerHTML = '<strong>¡Tienes recorridos pendientes!</strong> Haz clic aquí para reiniciarlos y verlos de nuevo en cada sección.';
                } else {
                    btnText.textContent = 'Reiniciar todos los tours';
                    btnReset.classList.remove('ring-2', 'ring-offset-2', 'ring-primary');
                    tourDesc.textContent = 'Vuelve a ver el tour que te enseña cada sección de la plataforma cuando quieras.';
                }
            }

            // Ejecutar la verificación al cargar la página
            document.addEventListener('DOMContentLoaded', checkPendingTours);

            // Acción del botón: Borrar todos los registros y redirigir al panel
            btnReset.addEventListener('click', function () {
                if (confirm('¿Quieres volver a ver todos los recorridos guiados? Se reiniciarán los de todas las secciones (Gastos, Simulador, Metas, etc.).')) {
                    if (typeof CCOnboarding !== 'undefined' && CCOnboarding.resetAll) {
                        CCOnboarding.resetAll();
                        // Redirigimos al usuario a su panel para que empiece el ciclo de nuevo
                        window.location.href = "{{ $esAdmin ? route('admin.panel') : route('panel') }}";
                    } else {
                        alert('Hubo un error al intentar reiniciar los tours. Recarga la página.');
                    }
                }
            });
        })();
    </script>
@endpush