@extends('layouts.publico')
@section('titulo', 'Iniciar sesión · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth-forms.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth-background.css') }}">
@endpush

@section('content')
<main class="relative min-h-screen flex justify-center items-center p-margin-mobile md:p-margin pt-20 pb-0 bg-surface">

  <x-auth-background />
  <div class="relative z-10 w-full max-w-7xl mx-auto">
    <div class="auth-grid grid grid-cols-1 lg:grid-cols-12 gap-gutter items-center">

      <section class="lg:col-span-7 xl:col-span-7">
        <div class="bg-surface-container-lowest shadow-xl rounded-xl p-6 sm:p-10 md:p-12 relative overflow-hidden">
          <div class="absolute -top-24 -right-24 w-80 h-80 bg-primary-fixed/25 rounded-full blur-3xl pointer-events-none"></div>

          <div class="relative z-10 flex flex-col gap-4 mb-space-lg">
            <div class="flex items-center justify-between gap-4">
              <x-logo :alto="36" />
              <a href="{{ route('registro') }}"
                 class="font-label-md text-label-md text-primary hover:text-primary-container underline decoration-2 underline-offset-4 transition-colors">
                Crear cuenta
              </a>
            </div>
            <div>
              <h1 class="font-headline-xl text-headline-xl text-on-surface leading-tight">Bienvenido de vuelta</h1>
              <p class="font-body-md text-body-md text-secondary mt-1">Entra a tu panel de Cuentas Claras.</p>
            </div>
          </div>

          @if(session('estado'))
            <div class="relative z-10 mb-4 p-3 rounded-lg auth-success font-body-sm text-body-sm">
              {{ session('estado') }}
            </div>
          @endif

          <form class="relative z-10 flex flex-col gap-space-lg" method="POST" action="{{ route('login.entrar') }}">
            @csrf

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="correo">
                Correo electrónico <span class="text-error">*</span>
              </label>
              <div class="relative flex items-center">
                <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none" aria-hidden="true">mail</span>
                <input
                  class="auth-input w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                  id="correo" name="correo" value="{{ old('correo') }}"
                  placeholder="tu@correo.com" required type="email" autocomplete="email">
              </div>
              @error('correo')
                <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                  <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                </p>
              @enderror
            </div>

            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="font-label-md text-label-md text-on-surface" for="password">
                  Contraseña <span class="text-error">*</span>
                </label>
                <a href="{{ route('password.request') }}"
                   class="font-label-sm text-label-sm text-primary hover:text-primary-container underline underline-offset-2">
                  ¿La olvidaste?
                </a>
              </div>
              <div class="relative flex items-center">
                <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none" aria-hidden="true">lock</span>
                <input
                  class="auth-input w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                  id="password" name="password" placeholder="••••••••••••"
                  required type="password" autocomplete="current-password">
                <button
                  class="absolute right-3 text-secondary hover:text-on-surface p-1 rounded transition-colors"
                  onclick="togglePasswordVisibility('password', 'icon-password')"
                  title="Mostrar u ocultar" type="button" aria-label="Mostrar u ocultar contraseña">
                  <span class="material-symbols-outlined text-lg" id="icon-password">visibility</span>
                </button>
              </div>
              @error('password')
                <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                  <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                </p>
              @enderror
            </div>

            <button
              class="auth-btn-primary w-full h-12 bg-primary text-on-primary font-headline-sm text-headline-sm rounded-lg shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
              type="submit">
              <span>Iniciar sesión</span>
              <span class="material-symbols-outlined text-xl">arrow_forward</span>
            </button>
          </form>

          <div class="relative z-10 mt-6 text-center font-body-sm text-body-sm text-on-surface-variant">
            ¿Sin cuenta?
            <a class="font-label-md text-label-md text-primary hover:text-primary-container underline decoration-2 underline-offset-4" href="{{ route('registro') }}">
              Crear cuenta gratis
            </a>
          </div>
        </div>
      </section>

      <aside class="lg:col-span-5 xl:col-span-5 flex flex-col gap-6">
        <div class="bg-surface-container-lowest shadow-md rounded-xl p-6 sm:p-8">
          <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-primary text-2xl icon-fill" aria-hidden="true">verified_user</span>
            <span class="font-label-md text-label-md text-primary uppercase tracking-wider">Tu cuenta, protegida</span>
          </div>
          <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-3">
            Seguridad de nivel financiero sin complicaciones.
          </h2>
          <p class="font-body-md text-body-md text-secondary mb-6 leading-relaxed">
            Cuentas Claras protege tu cuenta con contraseñas cifradas, sesión con expiración
            y avisos de accesos sospechosos.
          </p>

          <div class="space-y-3">
            <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
              <div class="w-8 h-8 rounded-md bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">lock</span>
              </div>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Contraseña cifrada con bcrypt</h3>
                <p class="font-body-sm text-body-sm text-secondary">Nunca la vemos ni podemos recuperarla. Solo tú la conoces.</p>
              </div>
            </div>
            <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
              <div class="w-8 h-8 rounded-md bg-tertiary-fixed text-tertiary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">timer</span>
              </div>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Sesión con expiración</h3>
                <p class="font-body-sm text-body-sm text-secondary">15 minutos de inactividad y la sesión se cierra automáticamente.</p>
              </div>
            </div>
            <div class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
              <div class="w-8 h-8 rounded-md bg-secondary-container text-on-secondary-fixed flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">notifications_active</span>
              </div>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Avisos de accesos</h3>
                <p class="font-body-sm text-body-sm text-secondary">Te avisamos por correo si alguien entra desde una ubicación nueva.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="auth-aside-note p-4 rounded-lg bg-surface-container-low flex items-start gap-3">
          <span class="material-symbols-outlined text-secondary text-xl shrink-0 mt-0.5" aria-hidden="true">info</span>
          <p class="font-label-sm text-label-sm text-secondary leading-relaxed">
            Cuentas Claras es una herramienta de organización financiera.
            <strong class="text-on-surface">No es una entidad financiera</strong> ni está vigilada por la Superintendencia Financiera de Colombia.
          </p>
        </div>
      </aside>

    </div>
  </div>
</main>
@endsection

@push('scripts')
  <script src="{{ asset('js/auth-forms.js') }}" defer></script>
@endpush