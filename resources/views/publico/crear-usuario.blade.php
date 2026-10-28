{{--
==============================================================================
ARCHIVO : resources/views/publico/crear-usuario.blade.php
QUÉ ES : Formulario de registro. Pantalla completa sin header/footer.
RUTA : GET /crear-usuario (registro) · POST /crear-usuario (registro.guardar)
CAMBIOS 2026-10-22:
- Eliminado badge AES-256 / Habeas Data (promesa no implementada).
- Eliminado testimonio ficticio (proyecto sin usuarios reales).
- Simplificado el selector de tipo de cuenta.
- Formulario reorganizado en secciones (Identidad, Contacto, Seguridad).
- Sidebar con beneficios reales + 50/30/20 + nota de seguridad honesta.
- Bugfix: -z-0 del glow interior eliminado.
==============================================================================
--}}

@extends('layouts.base')
@section('titulo', 'Crear cuenta · Cuentas Claras')
@section('body-class', 'bg-surface font-body-md text-body-md text-on-surface antialiased')
@push('styles')
  <link rel="stylesheet" href="{{ asset('css/crear-usuario.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth-background.css') }}">
@endpush
@section('body')
  <x-header />
  <main class="min-h-screen flex justify-center items-center p-margin-mobile md:p-margin pt-20 pb-12">
    <x-auth-background />
    <div class="flex flex-col w-full">
      <div class="w-full max-w-7xl mx-auto pt-20 md:pt-space-md">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">

          {{-- ============================================================ --}}
          {{-- FORMULARIO (7/8 columnas) --}}
          {{-- ============================================================ --}}
          <section class="lg:col-span-7 xl:col-span-8 flex flex-col justify-center">
            <div class="bg-surface-container-lowest shadow-xl rounded-xl p-6 sm:p-10 md:p-12 relative overflow-hidden">
              {{-- Glow ambiental (sin z-index negativo para evitar problemas de apilado) --}}
              <div class="absolute -top-24 -right-24 w-80 h-80 bg-primary-fixed/25 rounded-full blur-3xl pointer-events-none"></div>

              {{-- Header del card --}}
              <div class="relative z-10 flex flex-col gap-4 mb-space-lg">
                <div class="flex items-center justify-between gap-4">
                  <x-logo :alto="36" />
                  <a
                    href="{{ route('login') }}"
                    class="font-label-md text-label-md text-primary hover:text-primary-container underline decoration-2 underline-offset-4 transition-colors"
                  >Ya tengo cuenta</a>
                </div>
                <div>
                  <h1 class="font-headline-xl text-headline-xl text-on-surface leading-tight">
                    Crea tu cuenta
                  </h1>
                  <p class="font-body-md text-body-md text-secondary mt-1 max-w-lg">
                    Empieza a organizar tus finanzas en menos de 2 minutos. Sin tarjeta.
                  </p>
                </div>
              </div>

              {{-- Selector de tipo de cuenta --}}
              <div class="relative z-10 mb-space-lg">
                <p class="font-label-md text-label-md text-on-surface mb-2">
                  ¿Cómo vas a usar Cuentas Claras?
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="account-type-group">

                  {{-- Personal --}}
                  <div
                    class="account-type-card tarjeta-tinte--estandar cursor-pointer rounded-xl p-4 bg-surface-container-low transition-all duration-200 relative flex flex-col justify-between"
                    id="card-standard" onclick="selectAccountType('standard')" role="radio" aria-checked="true"
                    tabindex="0" aria-label="Cuenta personal, para uso individual">
                    <div class="flex items-start justify-between gap-3">
                      <div class="flex items-center gap-3">
                        <div
                          class="w-10 h-10 rounded-lg bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                          <span class="material-symbols-outlined text-xl">person</span>
                        </div>
                        <div class="min-w-0">
                          <p class="font-headline-sm text-headline-sm text-on-surface">Personal</p>
                          <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Para manejar tus finanzas solo/a.
                          </p>
                        </div>
                      </div>
                      <div class="w-5 h-5 rounded-full bg-primary flex items-center justify-center shrink-0 mt-0.5"
                        id="radio-standard">
                        <div class="w-2 h-2 rounded-full bg-surface-container-lowest"></div>
                      </div>
                    </div>
                  </div>

                  {{-- Familiar --}}
                  <div
                    class="account-type-card tarjeta-tinte--familiar cursor-pointer rounded-xl p-4 bg-surface-container-low transition-all duration-200 relative flex flex-col justify-between opacity-90 hover:opacity-100"
                    id="card-family" onclick="selectAccountType('family')" role="radio" aria-checked="false" tabindex="0"
                    aria-label="Cuenta familiar, para compartir metas con otra persona">
                    <div class="flex items-start justify-between gap-3">
                      <div class="flex items-center gap-3">
                        <div
                          class="w-10 h-10 rounded-lg bg-tertiary-fixed text-tertiary flex items-center justify-center shrink-0">
                          <span class="material-symbols-outlined text-xl">group</span>
                        </div>
                        <div class="min-w-0">
                          <p class="font-headline-sm text-headline-sm text-on-surface">Familiar</p>
                          <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Para compartir metas con otra persona.
                          </p>
                        </div>
                      </div>
                      <div
                        class="w-5 h-5 rounded-full bg-outline-variant flex items-center justify-center shrink-0 mt-0.5"
                        id="radio-family">
                        <div class="w-2 h-2 rounded-full bg-surface-container-lowest opacity-0 transition-opacity"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              {{-- Formulario --}}
              <form class="relative z-10 flex flex-col gap-space-lg" method="POST"
                action="{{ route('registro.guardar') }}">
                @csrf
                <input type="hidden" name="tipo_cuenta" id="tipo_cuenta"
                  value="{{ old('tipo_cuenta', request('tipo', 'estandar')) }}">

                @if($errors->any())
                  <div
                    class="p-3 rounded-lg bg-error-container text-on-error-container font-body-sm text-body-sm flex items-start gap-2"
                    role="alert">
                    <span class="material-symbols-outlined text-[18px] mt-0.5 shrink-0">error</span>
                    <span>Revisa los campos marcados antes de continuar.</span>
                  </div>
                @endif

                {{-- Sección: Identidad --}}
                <fieldset class="flex flex-col gap-4">
                  <legend class="font-label-sm text-label-sm text-secondary uppercase tracking-wider mb-1">
                    Identidad
                  </legend>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="fullName">
                        Nombre completo <span class="text-error">*</span>
                      </label>
                      <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none"
                          aria-hidden="true">badge</span>
                        <input
                          class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low focus:ring-2 focus:ring-primary/30 transition-all duration-150 shadow-xs"
                          id="fullName" name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Andrea Gómez" required
                          type="text" autocomplete="name">
                      </div>
                      @error('nombre')
                        <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                          <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                        </p>
                      @enderror
                    </div>

                    <div>
                      <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="documentId">
                        Identificación / Cédula <span class="text-error">*</span>
                      </label>
                      <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none"
                          aria-hidden="true">fingerprint</span>
                        <input
                          class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low focus:ring-2 focus:ring-primary/30 transition-all duration-150 shadow-xs"
                          id="documentId" name="identificacion" value="{{ old('identificacion') }}"
                          placeholder="Número de documento" required type="text" inputmode="numeric">
                      </div>
                      @error('identificacion')
                        <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                          <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                        </p>
                      @enderror
                    </div>
                  </div>
                </fieldset>

                {{-- Sección: Contacto --}}
                <fieldset class="flex flex-col gap-4">
                  <legend class="font-label-sm text-label-sm text-secondary uppercase tracking-wider mb-1">
                    Contacto
                  </legend>

                  <div>
                    <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="email">
                      Correo electrónico <span class="text-error">*</span>
                    </label>
                    <div class="relative flex items-center">
                      <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none"
                        aria-hidden="true">mail</span>
                      <input
                        class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low focus:ring-2 focus:ring-primary/30 transition-all duration-150 shadow-xs"
                        id="email" name="correo" value="{{ old('correo') }}" placeholder="andrea@ejemplo.com" required
                        type="email" autocomplete="email">
                    </div>
                    <p class="mt-1 font-label-sm text-label-sm text-secondary">
                      Será tu acceso único. No compartimos tu correo con terceros.
                    </p>
                    @error('correo')
                      <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                      </p>
                    @enderror
                  </div>
                </fieldset>

                {{-- Sección: Seguridad --}}
                <fieldset class="flex flex-col gap-4">
                  <legend class="font-label-sm text-label-sm text-secondary uppercase tracking-wider mb-1">
                    Seguridad
                  </legend>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                      <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="password">
                        Contraseña <span class="text-error">*</span>
                      </label>
                      <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none"
                          aria-hidden="true">lock</span>
                        <input
                          class="w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low focus:ring-2 focus:ring-primary/30 transition-all duration-150 shadow-xs"
                          id="password" name="password" oninput="evaluatePasswordStrength(this.value)"
                          placeholder="••••••••••••" required type="password" autocomplete="new-password">
                        <button
                          class="absolute right-3 text-secondary hover:text-on-surface p-1 rounded transition-colors"
                          onclick="togglePasswordVisibility('password', 'toggle-pwd-icon')"
                          title="Mostrar u ocultar contraseña" type="button" aria-label="Mostrar u ocultar contraseña">
                          <span class="material-symbols-outlined text-lg leading-none"
                            id="toggle-pwd-icon">visibility</span>
                        </button>
                      </div>
                      @error('password')
                        <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                          <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                        </p>
                      @enderror
                    </div>

                    <div>
                      <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="confirmPassword">
                        Confirmar contraseña <span class="text-error">*</span>
                      </label>
                      <div class="relative flex items-center">
                        <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none"
                          aria-hidden="true">lock_reset</span>
                        <input
                          class="w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low focus:ring-2 focus:ring-primary/30 transition-all duration-150 shadow-xs"
                          id="confirmPassword" name="password_confirmation" placeholder="••••••••••••" required
                          type="password" autocomplete="new-password">
                        <button
                          class="absolute right-3 text-secondary hover:text-on-surface p-1 rounded transition-colors"
                          onclick="togglePasswordVisibility('confirmPassword', 'toggle-conf-icon')"
                          title="Mostrar u ocultar confirmación" type="button"
                          aria-label="Mostrar u ocultar confirmación">
                          <span class="material-symbols-outlined text-lg leading-none"
                            id="toggle-conf-icon">visibility</span>
                        </button>
                      </div>
                      @error('password_confirmation')
                        <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                          <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                        </p>
                      @enderror
                    </div>
                  </div>

                  {{-- Medidor de fuerza --}}
                  <div class="p-3 bg-surface-container-low rounded-lg space-y-2">
                    <div class="flex items-center justify-between">
                      <span class="font-label-sm text-label-sm text-on-surface-variant">Seguridad de la clave</span>
                      <span class="font-label-sm text-label-sm text-secondary font-bold" id="strength-label">Mínima</span>
                    </div>
                    <div class="w-full h-1.5 bg-surface-container-highest rounded-full overflow-hidden flex gap-1">
                      <div class="h-full w-1/3 bg-error rounded-full transition-all duration-300" id="bar-1"></div>
                      <div class="h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300"
                        id="bar-2"></div>
                      <div class="h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300"
                        id="bar-3"></div>
                    </div>
                    <div
                      class="grid grid-cols-1 sm:grid-cols-3 gap-1 pt-1 text-on-surface-variant font-label-sm text-label-sm">
                      <span class="flex items-center gap-1" id="req-len">
                        <span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span>
                        Mín. 8 caracteres
                      </span>
                      <span class="flex items-center gap-1" id="req-num">
                        <span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span>
                        Al menos 1 número
                      </span>
                      <span class="flex items-center gap-1" id="req-sym">
                        <span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span>
                        Símbolo o mayúscula
                      </span>
                    </div>
                  </div>

                  <p class="font-label-sm text-label-sm text-secondary flex items-start gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-primary mt-0.5 shrink-0"
                      aria-hidden="true">lock</span>
                    <span>Tu contraseña se guarda cifrada con bcrypt. Nunca la vemos ni podemos recuperarla.</span>
                  </p>
                </fieldset>

                {{-- Consentimiento --}}
                <div class="pt-2 space-y-3">
                  <label class="flex items-start gap-3 cursor-pointer group">
                    <input name="consentimiento" value="1" {{ old('consentimiento') ? 'checked' : '' }}
                      class="mt-1 w-4 h-4 rounded text-primary focus:ring-0 cursor-pointer accent-primary" required
                      type="checkbox">
                    <span class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                      Acepto el tratamiento de mis datos personales de acuerdo y la
                      <a class="text-primary underline hover:text-primary-container"
                        href="{{ route('politica') }}">Política de Privacidad</a>
                      y los <a class="text-primary underline hover:text-primary-container"
                        href="{{ route('terminos') }}">Términos y Condiciones</a>.
                    </span>
                  </label>
                  @error('consentimiento')
                    <p class="font-body-sm text-body-sm text-error flex items-center gap-1">
                      <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                    </p>
                  @enderror
                </div>

                {{-- CTA --}}
                <div class="pt-2">
                  <button
                    class="w-full h-12 bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm rounded-lg shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
                    type="submit">
                    <span>Crear mi cuenta gratis</span>
                    <span class="material-symbols-outlined text-xl">arrow_forward</span>
                  </button>
                </div>
              </form>
            </div>
          </section>

          {{-- ============================================================ --}}
          {{-- SIDEBAR (5/4 columnas) --}}
          {{-- ============================================================ --}}
          <aside class="lg:col-span-5 xl:col-span-4 flex flex-col gap-6">
            <div class="bg-surface-container-lowest shadow-md rounded-xl p-6 sm:p-8">
              <div class="flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-primary text-2xl icon-fill" aria-hidden="true">verified</span>
                <span class="font-label-md text-label-md text-primary uppercase tracking-wider">
                  Lo que puedes hacer
                </span>
              </div>
              <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-3">
                Todo lo que necesitas para entender tu plata.
              </h2>

              <div class="space-y-3 mt-5">
                <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
                  <div class="w-8 h-8 rounded-md bg-primary/10 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-lg">pie_chart</span>
                  </div>
                  <div>
                    <h3 class="font-headline-sm text-[14px] text-on-surface">Regla 50/30/20 automática</h3>
                    <p class="font-body-sm text-body-sm text-secondary">
                      Tu distribución de necesidades, gustos y ahorro sin hojas de cálculo.
                    </p>
                  </div>
                </div>

                <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
                  <div
                    class="w-8 h-8 rounded-md bg-tertiary-fixed text-tertiary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-lg">notifications_active</span>
                  </div>
                  <div>
                    <h3 class="font-headline-sm text-[14px] text-on-surface">Avisos antes de cada pago</h3>
                    <p class="font-body-sm text-body-sm text-secondary">
                      Recordatorios de tus gastos fijos 3 días antes de que venzan.
                    </p>
                  </div>
                </div>

                <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
                  <div
                    class="w-8 h-8 rounded-md bg-secondary-container text-on-secondary-fixed flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-lg">lock_person</span>
                  </div>
                  <div>
                    <h3 class="font-headline-sm text-[14px] text-on-surface">Metas compartidas con privacidad</h3>
                    <p class="font-body-sm text-body-sm text-secondary">
                      Ahorra con otra persona y vean el avance conjunto, sin que cada uno
                      vea el aporte del otro.
                    </p>
                  </div>
                </div>
              </div>

              {{-- Mini viz 50/30/20 --}}
              <div class="mt-6 pt-5 border-t border-surface-container-highest">
                <div class="flex justify-between items-center text-label-sm font-label-sm text-secondary mb-2">
                  <span>Tu distribución ideal</span>
                </div>
                <div class="h-3 w-full bg-surface-container rounded-full overflow-hidden flex">
                  <div class="h-full bg-primary" style="width: 50%" title="50% Necesidades"></div>
                  <div class="h-full bg-tertiary-container" style="width: 30%" title="30% Gustos"></div>
                  <div class="h-full bg-primary-fixed-dim" style="width: 20%" title="20% Ahorro"></div>
                </div>
                <div class="flex justify-between items-center text-label-sm font-label-sm text-secondary mt-2 px-0.5">
                  <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary"></span> 50%
                    Fijos</span>
                  <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-tertiary-container"></span>
                    30% Gustos</span>
                  <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-primary-fixed-dim"></span>
                    20% Ahorro</span>
                </div>
              </div>

              <div class="p-3.5 bg-surface-container-low rounded-lg flex items-start gap-3 my-7">
                <span class="material-symbols-outlined text-primary text-xl mt-0.5 shrink-0 icon-fill"
                  aria-hidden="true">shield</span>
                <div class="text-on-surface-variant font-body-sm text-body-sm leading-snug">
                  <strong class="text-on-surface">Cuentas Claras no mueve tu dinero.</strong>
                  Nunca pedimos claves bancarias, tokens ni datos de tarjetas. Es una herramienta de
                  organización, no una entidad financiera.
                </div>
              </div>
            </div>

            {{-- Nota honesta de seguridad (sin claims de AES-256) --}}
            <div class="p-4 rounded-lg bg-surface-container-low flex items-start gap-3">
              <span class="material-symbols-outlined text-secondary text-xl shrink-0 mt-0.5"
                aria-hidden="true">info</span>
              <p class="font-label-sm text-label-sm text-secondary leading-relaxed">
                Cuentas Claras es una herramienta de organización financiera.
                <strong class="text-on-surface">No es una entidad financiera</strong> ni está
                vigilada por la Superintendencia Financiera de Colombia.
                Nunca pedimos claves bancarias.
              </p>
            </div>
          </aside>

        </div>
      </div>
    </div>
  </main>
@endsection
@push('scripts')
  <script src="{{ asset('js/crear-usuario.js') }}"></script>
@endpush