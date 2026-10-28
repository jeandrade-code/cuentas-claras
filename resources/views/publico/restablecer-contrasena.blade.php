@extends('layouts.publico')
@section('titulo', 'Nueva contraseña · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth-forms.css') }}">
  <link rel="stylesheet" href="{{ asset('css/auth-background.css') }}">
@endpush

@section('content')
@php
  $emailPrefijado = old('correo', request('email'));
  $emailSoloLectura = (bool) request('email');
@endphp

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
              <a href="{{ route('login') }}"
                 class="font-label-md text-label-md text-primary hover:text-primary-container underline decoration-2 underline-offset-4 transition-colors">
                Volver a iniciar sesión
              </a>
            </div>
            <div>
              <h1 class="font-headline-xl text-headline-xl text-on-surface leading-tight">Nueva contraseña</h1>
              <p class="font-body-md text-body-md text-secondary mt-1">Elige una contraseña segura que puedas recordar.</p>
            </div>
          </div>

          @if(session('estado'))
            <div class="relative z-10 mb-4 p-3 rounded-lg auth-success font-body-sm text-body-sm">
              {{ session('estado') }}
            </div>
          @endif

          <form class="relative z-10 flex flex-col gap-space-lg" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token ?? '' }}">

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="correo">
                Correo electrónico <span class="text-error">*</span>
              </label>
              <div class="relative flex items-center">
                <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none" aria-hidden="true">mail</span>
                <input
                  class="auth-input w-full h-11 pl-11 pr-4 rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none transition-all duration-150 shadow-xs {{ $emailSoloLectura ? 'bg-surface-container-low text-secondary cursor-not-allowed' : 'bg-surface-container-lowest focus:bg-surface-container-low' }}"
                  id="correo" name="correo" value="{{ $emailPrefijado }}"
                  placeholder="tu@correo.com" required type="email" autocomplete="email"
                  {{ $emailSoloLectura ? 'readonly' : '' }}>
              </div>
              @if($emailSoloLectura)
                <p class="mt-1 font-label-sm text-label-sm text-secondary">No puedes cambiar el correo desde aquí.</p>
              @endif
              @error('correo')
                <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                  <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                </p>
              @enderror
            </div>

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="password">
                Nueva contraseña <span class="text-error">*</span>
              </label>
              <div class="relative flex items-center">
                <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none" aria-hidden="true">lock</span>
                <input
                  class="auth-input w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                  id="password" name="password" placeholder="••••••••••••"
                  required type="password" autocomplete="new-password"
                  oninput="evaluatePasswordStrength(this.value)">
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

            <div>
              <label class="block font-label-md text-label-md text-on-surface mb-1.5" for="password_confirmation">
                Confirmar contraseña <span class="text-error">*</span>
              </label>
              <div class="relative flex items-center">
                <span class="material-symbols-outlined absolute left-3.5 text-outline text-lg pointer-events-none" aria-hidden="true">lock_reset</span>
                <input
                  class="auth-input w-full h-11 pl-11 pr-11 bg-surface-container-lowest rounded-lg font-body-md text-body-md text-on-surface placeholder:text-outline/70 focus:outline-none focus:bg-surface-container-low transition-all duration-150 shadow-xs"
                  id="password_confirmation" name="password_confirmation" placeholder="••••••••••••"
                  required type="password" autocomplete="new-password">
                <button
                  class="absolute right-3 text-secondary hover:text-on-surface p-1 rounded transition-colors"
                  onclick="togglePasswordVisibility('password_confirmation', 'icon-password_confirmation')"
                  title="Mostrar u ocultar" type="button" aria-label="Mostrar u ocultar confirmación">
                  <span class="material-symbols-outlined text-lg" id="icon-password_confirmation">visibility</span>
                </button>
              </div>
              @error('password_confirmation')
                <p class="mt-1 font-body-sm text-body-sm text-error flex items-center gap-1">
                  <span class="material-symbols-outlined text-[16px]">error</span>{{ $message }}
                </p>
              @enderror
            </div>

            <div class="p-3 bg-surface-container-low rounded-lg space-y-2">
              <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Seguridad de la clave</span>
                <span class="font-label-sm text-label-sm text-secondary font-bold" id="strength-label">Mínima</span>
              </div>
              <div class="w-full h-1.5 bg-surface-container-highest rounded-full overflow-hidden flex gap-1">
                <div class="h-full w-1/3 bg-error rounded-full transition-all duration-300" id="bar-1"></div>
                <div class="h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300" id="bar-2"></div>
                <div class="h-full w-1/3 bg-surface-container-highest rounded-full transition-all duration-300" id="bar-3"></div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-1 pt-1 text-on-surface-variant font-label-sm text-label-sm">
                <span class="flex items-center gap-1" id="req-len">
                  <span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span>Mín. 8 caracteres
                </span>
                <span class="flex items-center gap-1" id="req-num">
                  <span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span>Al menos 1 número
                </span>
                <span class="flex items-center gap-1" id="req-sym">
                  <span class="material-symbols-outlined text-xs text-secondary">radio_button_unchecked</span>Símbolo o mayúscula
                </span>
              </div>
            </div>

            <button
              class="auth-btn-primary w-full h-12 bg-primary text-on-primary font-headline-sm text-headline-sm rounded-lg shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
              type="submit">
              <span>Guardar nueva contraseña</span>
              <span class="material-symbols-outlined text-xl">check</span>
            </button>
          </form>
        </div>
      </section>

      <aside class="lg:col-span-5 xl:col-span-5 flex flex-col gap-6">
        <div class="bg-surface-container-lowest shadow-md rounded-xl p-6 sm:p-8">
          <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-primary text-2xl icon-fill" aria-hidden="true">shield_lock</span>
            <span class="font-label-md text-label-md text-primary uppercase tracking-wider">Contraseña segura</span>
          </div>
          <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-3">Tres reglas simples para una clave fuerte.</h2>

          <ul class="space-y-3 mt-5">
            <li class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
              <div class="w-8 h-8 rounded-md bg-primary/10 text-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">straighten</span>
              </div>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Mínimo 8 caracteres</h3>
                <p class="font-body-sm text-body-sm text-secondary">Cuanto más larga, mejor. Una frase corta es más fuerte que una palabra rara.</p>
              </div>
            </li>
            <li class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
              <div class="w-8 h-8 rounded-md bg-tertiary-fixed text-tertiary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">shuffle</span>
              </div>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Mezcla de todo</h3>
                <p class="font-body-sm text-body-sm text-secondary">Mayúsculas, minúsculas, números y al menos un símbolo.</p>
              </div>
            </li>
            <li class="flex items-start gap-3.5 p-3 rounded-lg bg-surface-container-low">
              <div class="w-8 h-8 rounded-md bg-secondary-container text-on-secondary-fixed flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">block</span>
              </div>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Única para Cuentas Claras</h3>
                <p class="font-body-sm text-body-sm text-secondary">No reutilices la clave de tu correo, banco o redes sociales.</p>
              </div>
            </li>
          </ul>
        </div>

        <div class="auth-aside-note p-4 rounded-lg bg-surface-container-low flex items-start gap-3">
          <span class="material-symbols-outlined text-secondary text-xl shrink-0 mt-0.5" aria-hidden="true">lock</span>
          <p class="font-label-sm text-label-sm text-secondary leading-relaxed">
            Tu contraseña se guarda cifrada con <strong class="text-on-surface">bcrypt</strong>.
            Ni siquiera nosotros podemos leerla o recuperarla.
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