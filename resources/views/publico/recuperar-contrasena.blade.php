@extends('layouts.publico')
@section('titulo', 'Recuperar contraseña · Cuentas Claras')

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
              <a href="{{ route('login') }}"
                 class="font-label-md text-label-md text-primary hover:text-primary-container underline decoration-2 underline-offset-4 transition-colors">
                Volver a iniciar sesión
              </a>
            </div>
            <div>
              <h1 class="font-headline-xl text-headline-xl text-on-surface leading-tight">Recuperar contraseña</h1>
              <p class="font-body-md text-body-md text-secondary mt-1">Te enviaremos un enlace para restablecerla.</p>
            </div>
          </div>

          @if(session('estado'))
            <div class="relative z-10 mb-4 p-3 rounded-lg auth-success font-body-sm text-body-sm">
              {{ session('estado') }}
            </div>
          @endif

          <form class="relative z-10 flex flex-col gap-space-lg" method="POST" action="{{ route('password.email') }}">
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

            <button
              class="auth-btn-primary w-full h-12 bg-primary text-on-primary font-headline-sm text-headline-sm rounded-lg shadow-md hover:shadow-lg active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2"
              type="submit">
              <span>Enviar enlace</span>
              <span class="material-symbols-outlined text-xl">arrow_forward</span>
            </button>
          </form>

          <p class="relative z-10 mt-5 font-label-sm text-label-sm text-secondary text-center">
            Por seguridad, te mostraremos el mismo mensaje exista o no el correo en nuestra base.
          </p>
        </div>
      </section>

      <aside class="lg:col-span-5 xl:col-span-5 flex flex-col gap-6">
        <div class="bg-surface-container-lowest shadow-md rounded-xl p-6 sm:p-8">
          <div class="flex items-center gap-2 mb-4">
            <span class="material-symbols-outlined text-primary text-2xl icon-fill" aria-hidden="true">key</span>
            <span class="font-label-md text-label-md text-primary uppercase tracking-wider">Cómo funciona</span>
          </div>
          <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-3">Recupera tu acceso en 3 pasos.</h2>

          <ol class="space-y-4 mt-5">
            <li class="flex items-start gap-3">
              <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center shrink-0 font-bold text-sm">1</span>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Escribe tu correo</h3>
                <p class="font-body-sm text-body-sm text-secondary">El mismo con el que te registraste.</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center shrink-0 font-bold text-sm">2</span>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Revisa tu bandeja</h3>
                <p class="font-body-sm text-body-sm text-secondary">Recibirás un enlace. Si no llega en 5 minutos, revisa spam.</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center shrink-0 font-bold text-sm">3</span>
              <div>
                <h3 class="font-headline-sm text-[14px] text-on-surface">Crea una nueva clave</h3>
                <p class="font-body-sm text-body-sm text-secondary">El enlace vence a los 30 minutos y solo puede usarse una vez.</p>
              </div>
            </li>
          </ol>
        </div>

        <div class="auth-aside-note p-4 rounded-lg bg-surface-container-low flex items-start gap-3">
          <span class="material-symbols-outlined text-secondary text-xl shrink-0 mt-0.5" aria-hidden="true">shield</span>
          <p class="font-label-sm text-label-sm text-secondary leading-relaxed">
            Por seguridad, <strong class="text-on-surface">nunca te enviaremos tu contraseña</strong>.
            Si no recuerdas el correo con el que te registraste, escríbenos desde
            <a href="{{ route('politica') }}" class="text-primary underline underline-offset-2">la política de privacidad</a>.
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