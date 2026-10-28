{{--
==============================================================================
ARCHIVO : resources/views/publico/landing.blade.php
QUÉ ES : Landing page pública. Presenta el producto y lleva a registro/login.
RUTA : GET / (nombre: inicio) · sin sesión
CSS : public/css/landing-page.css
JS : public/js/landing-page.js (animaciones + demo interactivo)
public/js/landing-header.js (auto-hide del header, solo aquí)
==============================================================================
IMÁGENES (3):
#1 Hero · dos fotos que se alternan a la derecha, volteadas por CSS (.lp-hero-bg)
1264x843 px · según el modo:
- anónimo / estándar : public/img/hero-main.jpg y public/img/hero-main-2.jpg
- familiar : public/img/hero-main-2_morado.jpg y public/img/hero-main_morado.jpg
- admin : public/img/hero-main-2_azul.jpg y public/img/hero-main_azul.jpg
#2 Metas compartidas · pareja con alcancía, se desvanece hacia abajo
1200x800 px · public/img/meta-couple.jpg
#3 CTA final · pareja planeando, con máscara lateral
1200x800 px · public/img/cuenta-familiar.jpg
==============================================================================
--}}

@extends('layouts.publico')
@section('titulo', 'Tus finanzas en orden · Cuentas Claras')

<!-- Seo -->
@section('meta_description', 'Pon en orden tus gastos y alcanza tus metas. Registra gastos, recibe alertas de pagos, simula créditos y ahorra en pareja. 100% gratis y sin conectar tu banco.')
@section('meta_image', asset('img/meta-couple.jpg'))

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/landing-page.css') }}">
@endpush

@section('content')
  @php
    // ------------------------------------------------------------------
    // Modo de la landing: define qué par de imágenes usa el hero.
    //   - anonimo  : nadie logueado      -> imágenes originales (verde)
    //   - estandar : rol 'estandar'      -> imágenes originales (verde)
    //   - familiar : rol 'familiar'      -> imágenes moradas
    //   - admin    : rol 'administrador' -> imágenes azules
    // Roles según §4 de la arquitectura: estandar | familiar | administrador.
    // ------------------------------------------------------------------
    $user = auth()->user();

    $modo = match (true) {
      !$user => 'anonimo',
      $user->rol === 'administrador' => 'admin',
      $user->rol === 'familiar' => 'familiar',
      default => 'estandar',
    };

    // Par de imágenes del hero (la primera es la activa inicial).
    [$heroImgA, $heroImgB] = match ($modo) {
      'familiar' => ['img/hero-main_morado.jpg', 'img/hero-main-2_morado.jpg'],
      'admin' => ['img/hero-main_azul.jpg', 'img/hero-main-2_azul.jpg'],
      default => ['img/hero-main.jpg', 'img/hero-main-2.jpg'],
    };
  @endphp

  <div class="lp-progress bg-primary" aria-hidden="true"></div>

  <main class="w-full pt-20 bg-surface">
    <div class="flex flex-col w-full">

      <!-- ============================================================== -->
      <!-- HERO -->
      <!-- ============================================================== -->
      <section
        class="relative w-full overflow-hidden bg-gradient-to-b from-surface via-surface-container-low/40 to-surface pt-0 pb-24 lg:pt-16 lg:pb-32"
        id="hero">
        <div
          class="lp-drift absolute -top-32 left-1/2 -translate-x-1/2 w-[720px] h-[360px] bg-primary/10 rounded-full blur-[110px] pointer-events-none">
        </div>
        <div
          class="lp-drift absolute top-1/3 -right-24 w-80 h-80 bg-tertiary-container/15 rounded-full blur-[90px] pointer-events-none">
        </div>

        {{-- IMAGEN #1: foto de fondo del hero (lado derecho) --}}
        <div class="absolute inset-y-0 right-0 flex items-center pointer-events-none z-0" id="hero-right">
          <div class="relative w-[800px] max-w-full">
            <div class="lp-hero-bg" aria-hidden="true">
              {{-- Las dos fotos se alternan solas con un fundido (sin botones). Ambas se voltean por CSS. --}}
              <img class="lp-img is-active" src="{{ asset($heroImgA) }}" alt="" width="1264" height="843" decoding="async"
                fetchpriority="high">
              <img class="lp-img" src="{{ asset($heroImgB) }}" alt="" width="1264" height="843" decoding="async">
            </div>

            {{-- Tarjetas flotantes posicionadas en % sobre la foto --}}
            <div class="lp-hero-cards hidden lg:block lp-reveal lp-reveal--zoom">
              {{-- Juego 1: va con la foto 1. Juego 2: va con la foto 2. Se alternan juntos. --}}
              <div class="lp-cards-set is-active">
                <div class="lp-card lp-card--vencimiento w-64 flex items-center gap-3">
                  <div class="w-10 h-10 rounded-xl bg-error/15 text-error flex items-center justify-center shrink-0">
                    <span class="lp-wiggle material-symbols-outlined text-[22px]">notifications_active</span>
                  </div>
                  <div class="min-w-0">
                    <p class="font-headline-sm text-[13px] text-on-surface font-bold truncate">Arriendo de vivienda</p>
                    <p class="font-label-sm text-[11px] text-error font-bold">Vence en 2 días · $1.200.000</p>
                  </div>
                </div>

                <div class="lp-card lp-card--disponible flex flex-col gap-1">
                  <span class="font-label-sm text-[11px] text-secondary uppercase font-bold tracking-wider">Disponible
                    del mes</span>
                  <span class="lp-num font-numeric-lg text-numeric-lg text-primary font-bold" data-count="3850000"
                    data-format="cop">$3.850.000</span>
                </div>

                <div class="lp-card lp-card--meta w-64 flex flex-col gap-2">
                  <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5 font-headline-sm text-[13px] text-on-surface font-bold">
                      <span class="material-symbols-outlined text-[16px] text-primary">flight_takeoff</span>
                      Vacaciones
                    </span>
                    <span class="lp-num font-label-sm text-[12px] text-primary font-bold" data-count="72"
                      data-suffix="%">72%</span>
                  </div>
                  <div class="w-full h-2 rounded-full bg-surface-container-high overflow-hidden">
                    <div class="lp-fill lp-w-72 bg-primary h-full rounded-full"></div>
                  </div>
                </div>
              </div>

              <div class="lp-cards-set">
                <div class="lp-card lp-card--invitacion w-64 flex items-center gap-3">
                  <div
                    class="w-10 h-10 rounded-xl bg-primary-fixed text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">mark_email_unread</span>
                  </div>
                  <div class="min-w-0">
                    <p class="font-headline-sm text-[13px] text-on-surface font-bold truncate">Tu pareja te invitó a una
                      meta</p>
                    <p class="font-label-sm text-[11px] text-primary font-bold">Fondo del apartamento · Responder</p>
                  </div>
                </div>

                <div class="lp-card lp-card--gasto w-60 flex flex-col gap-2">
                  <span class="font-label-sm text-[11px] text-secondary uppercase font-bold tracking-wider">Llevas
                    gastado este mes</span>
                  <span class="lp-num font-numeric-lg text-numeric-lg text-on-surface font-bold" data-count="1925000"
                    data-format="cop">$1.925.000</span>
                  <div class="w-full h-2 rounded-full bg-surface-container-high overflow-hidden">
                    <div class="lp-fill lp-w-50 bg-primary h-full rounded-full"></div>
                  </div>
                  <span class="font-label-sm text-[11px] text-secondary">50% de tu ingreso</span>
                </div>

                <div class="lp-card lp-card--informe w-64 flex flex-col gap-2">
                  <div class="flex items-center justify-between">
                    <span class="flex items-center gap-1.5 font-headline-sm text-[13px] text-on-surface font-bold">
                      <span class="material-symbols-outlined text-[16px] text-primary">insights</span>
                      Tu informe está listo
                    </span>
                    <span class="font-label-sm text-[11px] text-primary font-bold">50/30/20</span>
                  </div>
                  <div class="w-full h-2 rounded-full bg-surface-container-high flex overflow-hidden">
                    <div class="lp-fill lp-w-50 bg-primary h-full"></div>
                    {{-- CAMBIO 1: bg-tertiary-container -> bg-want (bloque gustos) --}}
                    <div class="lp-fill lp-w-30 bg-want h-full"></div>
                    <div class="lp-fill lp-w-20 bg-primary-fixed-dim h-full"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="max-w-7xl mx-auto px-margin relative z-10 pt-14">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-8 items-center pt-12">

            <div class="lg:col-span-6 flex flex-col gap-6 text-left">

              <h1 class="lp-rise lp-d2 font-display-lg text-display-lg text-on-surface tracking-tight leading-[1.12]">
                Pon en orden tus <span
                  class="lp-rotator text-primary underline decoration-primary-fixed decoration-wavy underline-offset-8"
                  data-rotator data-words="gastos|recibos|metas|ahorros|créditos">gastos</span> y alcanza tus metas, solo
                o en pareja.
              </h1>

              <p class="lp-rise lp-d3 font-body-lg text-body-lg text-on-surface-variant max-w-xl leading-relaxed">
                Registra tus gastos en segundos. Te avisamos antes de cada pago. Y si quieres, ahorras con tu pareja
                sin que vea tus números.
              </p>

              <div class="lp-rise lp-d4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
                <a class="lp-shine inline-flex items-center justify-center gap-3 px-7 py-3.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm shadow-md hover:shadow-lg transition-all duration-200 group active:scale-[0.98]"
                  href="{{ route('registro', ['tipo' => 'estandar']) }}">
                  <span>Crear mi cuenta gratis</span>
                  <span
                    class="material-symbols-outlined text-[20px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                </a>
                <a href="#probar"
                  class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-lg text-on-surface-variant hover:text-on-surface font-label-md text-label-md">
                  <span class="material-symbols-outlined text-[18px]">play_circle</span>
                  <span>Probar sin registrarme</span>
                </a>
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- CINTA DE GANCHOS -->
      <!-- ============================================================== -->
      <section class="w-full bg-surface-container-low py-6 border-y border-outline-variant/30"
        aria-label="Lo que puedes hacer con Cuentas Claras">
        <div class="lp-marquee text-on-surface-variant">
          <div class="lp-marquee__track font-label-md text-label-md font-semibold">

            <span class="flex items-center gap-2 whitespace-nowrap">
              <span class="material-symbols-outlined text-[18px] text-primary">link_off</span>
              Sin claves bancarias
            </span>

            <span class="flex items-center gap-2 whitespace-nowrap">
              <span class="material-symbols-outlined text-[18px] text-primary">notifications_active</span>
              Avisos 3 días antes
            </span>

            <span class="flex items-center gap-2 whitespace-nowrap">
              <span class="material-symbols-outlined text-[18px] text-primary">lock</span>
              Metas en pareja, aportes privados
            </span>

            <span class="flex items-center gap-2 whitespace-nowrap">
              <span class="material-symbols-outlined text-[18px] text-primary">pie_chart</span>
              Tu informe automático
            </span>

            <span class="flex items-center gap-2 whitespace-nowrap">
              <span class="material-symbols-outlined text-[18px] text-primary">calculate</span>
              Simula sin miedo a reportes
            </span>

            <span class="flex items-center gap-2 whitespace-nowrap">
              <span class="material-symbols-outlined text-[18px] text-primary">savings</span>
              Gratis, sin tarjeta
            </span>

            <span class="flex items-center gap-2 whitespace-nowrap">
              <span class="material-symbols-outlined text-[18px] text-primary">smartphone</span>
              Funciona en tu celular
            </span>

          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- CÓMO FUNCIONA -->
      <!-- ============================================================== -->
      <section id="como-funciona" class="w-full py-24 bg-surface-container-low/60 relative">
        <div class="max-w-7xl mx-auto px-margin flex flex-col gap-16">
          <div class="lp-reveal flex flex-col items-center text-center gap-3 max-w-2xl mx-auto">
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">Empezar es fácil</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Sin tarjeta. Sin integrar tu banco. Sin
              vueltas.</p>
          </div>

          <div class="lp-reveal relative">
            <div class="lp-line hidden md:block absolute top-[2.9rem] left-[17%] right-[17%] h-0.5 bg-primary/30"></div>
            <div class="lp-stagger grid grid-cols-1 md:grid-cols-3 gap-8 relative">
              <div class="flex flex-col items-center text-center gap-4 p-6">
                <div
                  class="w-16 h-16 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-lg lp-ring text-primary">
                  <span class="material-symbols-outlined text-[30px] text-on-primary">person_add</span>
                </div>
                <h3 class="font-headline-md text-headline-md text-on-surface font-bold">1. Crea tu cuenta</h3>
                <p class="font-body-md text-body-md text-secondary leading-relaxed">Correo, contraseña y listo. Elige
                  cuenta personal o en pareja; puedes cambiarla luego.</p>
              </div>
              <div class="flex flex-col items-center text-center gap-4 p-6">
                <div class="w-16 h-16 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-lg">
                  <span class="material-symbols-outlined text-[30px]">edit_note</span>
                </div>
                <h3 class="font-headline-md text-headline-md text-on-surface font-bold">2. Registra y organiza</h3>
                <p class="font-body-md text-body-md text-secondary leading-relaxed">Anota tus gastos, marca cuáles son
                  fijos y define tus metas. Cada gasto queda en su lugar.</p>
              </div>
              <div class="flex flex-col items-center text-center gap-4 p-6">
                <div class="w-16 h-16 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-lg">
                  <span class="material-symbols-outlined text-[30px]">insights</span>
                </div>
                <h3 class="font-headline-md text-headline-md text-on-surface font-bold">3. Consulta y decide</h3>
                <p class="font-body-md text-body-md text-secondary leading-relaxed">Mira tu informe, simula créditos y
                  recibe avisos antes de cada vencimiento.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- DEMO INTERACTIVO -->
      <!-- ============================================================== -->
      <section id="probar" class="w-full py-24 bg-surface relative overflow-hidden">

        {{-- Doodles de fondo: patrón SVG con 15 íconos financieros. --}}
        <div class="lp-doodles bg-primary" aria-hidden="true"></div>

        {{-- Glows drift laterales --}}
        <div
          class="lp-drift absolute -bottom-32 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-[100px] pointer-events-none">
        </div>
        <div
          class="lp-drift absolute -top-24 -right-32 w-80 h-80 bg-tertiary-container/15 rounded-full blur-[100px] pointer-events-none">
        </div>

        <div class="max-w-5xl mx-auto px-margin flex flex-col gap-10 relative z-10">

          {{-- Encabezado --}}
          <div class="lp-reveal flex flex-col items-center text-center gap-3 max-w-2xl mx-auto">
            <span
              class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-fixed/40 font-label-sm text-label-sm uppercase tracking-widest text-primary font-bold">
              <span class="material-symbols-outlined text-[14px]">touch_app</span>
              Demo
            </span>
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">Probar sin registrarte</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Un vistazo rápido a dos herramientas. Nada
              se guarda.</p>
          </div>

          {{-- Card con acentos flotantes --}}
          <div class="isolate">

            {{-- Acento flotante izquierdo · ahorro --}}
            <div
              class="lp-float absolute -left-12 top-1/3 w-14 h-14 rounded-2xl bg-primary flex items-center justify-center text-on-primary shadow-xl pointer-events-none hidden lg:flex z-20"
              aria-hidden="true">
              <span class="material-symbols-outlined text-[26px]" style="font-variation-settings:'FILL' 1;">savings</span>
            </div>

            {{-- CAMBIO 2: Acento flotante derecho · tendencia -> marca secundaria --}}
            <div
              class="lp-float-slow absolute -right-12 top-2/3 w-14 h-14 rounded-2xl bg-accent-secondary flex items-center justify-center text-on-accent-secondary shadow-xl pointer-events-none hidden lg:flex z-20"
              aria-hidden="true">
              <span class="material-symbols-outlined text-[26px]"
                style="font-variation-settings:'FILL' 1;">trending_up</span>
            </div>

            {{-- Acento flotante superior · calculadora --}}
            <div
              class="lp-float-late absolute -top-4 right-12 w-12 h-12 rounded-xl bg-secondary-container flex items-center justify-center text-on-secondary-container shadow-xl pointer-events-none hidden lg:flex z-20"
              aria-hidden="true">
              <span class="material-symbols-outlined text-[22px]">calculate</span>
            </div>

            {{-- Card --}}
            <div id="simulador"
              class="lp-reveal lp-reveal--zoom relative bg-surface-container-lowest rounded-3xl shadow-2xl p-5 sm:p-8 flex flex-col gap-6 border border-outline-variant/20">

              {{-- Tabs --}}
              <div role="tablist" aria-label="Elige una herramienta"
                class="inline-flex self-center p-1 rounded-xl bg-surface-container-low gap-1">
                <button type="button" role="tab" id="tab-regla" data-tab="regla" aria-selected="true"
                  aria-controls="panel-regla"
                  class="lp-tab inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-lg font-label-md text-label-md font-bold text-on-surface-variant aria-selected:bg-primary aria-selected:text-on-primary">
                  <span class="material-symbols-outlined text-[18px]">pie_chart</span>Mi 50/30/20
                </button>
                <button type="button" role="tab" id="tab-credito" data-tab="credito" aria-selected="false"
                  aria-controls="panel-credito"
                  class="lp-tab inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-lg font-label-md text-label-md font-bold text-on-surface-variant aria-selected:bg-primary aria-selected:text-on-primary">
                  <span class="material-symbols-outlined text-[18px]">calculate</span>Simular crédito
                </button>
              </div>

              <!-- Panel 1: 50/30/20 -->
              <div id="panel-regla" class="lp-panel grid grid-cols-1 md:grid-cols-2 gap-8" data-panel="regla"
                role="tabpanel" aria-labelledby="tab-regla">
                <div class="flex flex-col gap-4">
                  <label class="flex flex-col gap-1.5">
                    <span class="font-label-md text-label-md text-on-surface font-semibold">Tu ingreso mensual
                      (COP)</span>
                    <input id="reg-ingreso" type="text" inputmode="numeric" autocomplete="off" value="3.850.000"
                      class="lp-num w-full rounded-lg bg-surface-container-low px-4 py-3 font-numeric-md text-numeric-md text-on-surface border border-outline-variant/40 focus:border-primary focus:outline-none">
                  </label>
                  <label class="flex flex-col gap-1.5">
                    <span class="font-label-md text-label-md text-on-surface font-semibold">Lo que gastas en necesidades
                      <span class="text-secondary font-normal">(arriendo, mercado, servicios)</span></span>
                    <input id="reg-necesidades" type="text" inputmode="numeric" autocomplete="off" value="2.100.000"
                      class="lp-num w-full rounded-lg bg-surface-container-low px-4 py-3 font-numeric-md text-numeric-md text-on-surface border border-outline-variant/40 focus:border-primary focus:outline-none">
                  </label>
                  <label class="flex flex-col gap-1.5">
                    <span class="font-label-md text-label-md text-on-surface font-semibold">Lo que gastas en gustos
                      <span class="text-secondary font-normal">(salidas, ropa, suscripciones)</span></span>
                    <input id="reg-gustos" type="text" inputmode="numeric" autocomplete="off" value="1.150.000"
                      class="lp-num w-full rounded-lg bg-surface-container-low px-4 py-3 font-numeric-md text-numeric-md text-on-surface border border-outline-variant/40 focus:border-primary focus:outline-none">
                  </label>
                  <p class="font-body-sm text-body-sm text-secondary">Lo que sobra se cuenta como ahorro.</p>
                </div>

                <div class="bg-surface-container-low rounded-2xl p-5 flex flex-col justify-center gap-4">
                  <p id="reg-vacio" class="font-body-md text-body-md text-secondary text-center" hidden>Escribe tu
                    ingreso para ver el diagnóstico.</p>
                  <div id="reg-resultado" class="flex flex-col gap-4">
                    <div class="w-full h-4 rounded-full bg-surface-container-high overflow-hidden flex gap-0.5">
                      <div id="reg-bar-nec" class="lp-bar-live h-full bg-primary rounded-l-full"></div>
                      {{-- CAMBIO 3: bg-tertiary-container -> bg-want --}}
                      <div id="reg-bar-gus" class="lp-bar-live h-full bg-want"></div>
                      <div id="reg-bar-aho" class="lp-bar-live h-full bg-primary-fixed-dim rounded-r-full"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 font-label-sm text-[12px]">
                      <div class="flex flex-col gap-0.5"><span class="text-secondary flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-primary"></span>Necesidades</span><span id="reg-pct-nec"
                          class="lp-num font-numeric-md text-numeric-md font-bold text-on-surface">0%</span><span
                          id="reg-val-nec" class="lp-num text-secondary">$0</span></div>
                      {{-- CAMBIO 4: bg-tertiary-container -> bg-want --}}
                      <div class="flex flex-col gap-0.5"><span class="text-secondary flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-want"></span>Gustos</span><span id="reg-pct-gus"
                          class="lp-num font-numeric-md text-numeric-md font-bold text-on-surface">0%</span><span
                          id="reg-val-gus" class="lp-num text-secondary">$0</span></div>
                      <div class="flex flex-col gap-0.5"><span class="text-secondary flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-primary-fixed-dim"></span>Ahorro</span><span id="reg-pct-aho"
                          class="lp-num font-numeric-md text-numeric-md font-bold text-on-surface">0%</span><span
                          id="reg-val-aho" class="lp-num text-secondary">$0</span></div>
                    </div>
                    <p id="reg-mensaje" class="font-headline-sm text-headline-sm text-on-surface font-bold leading-snug">
                    </p>
                    <p id="reg-ideal" class="lp-num font-label-sm text-label-sm text-secondary"></p>
                  </div>
                </div>
              </div>

              <!-- Panel 2: crédito -->
              <div id="panel-credito" class="lp-panel grid grid-cols-1 md:grid-cols-2 gap-8" data-panel="credito"
                role="tabpanel" aria-labelledby="tab-credito" hidden>
                <div class="flex flex-col gap-4">
                  <label class="flex flex-col gap-1.5">
                    <span class="font-label-md text-label-md text-on-surface font-semibold">Monto del crédito
                      (COP)</span>
                    <input id="cr-monto" type="text" inputmode="numeric" autocomplete="off" value="10.000.000"
                      class="lp-num w-full rounded-lg bg-surface-container-low px-4 py-3 font-numeric-md text-numeric-md text-on-surface border border-outline-variant/40 focus:border-primary focus:outline-none">
                  </label>
                  <div class="grid grid-cols-2 gap-4">
                    <label class="flex flex-col gap-1.5">
                      <span class="font-label-md text-label-md text-on-surface font-semibold">Tasa mensual (%)</span>
                      <input id="cr-tasa" type="number" min="0" max="5" step="0.1" value="1.5"
                        class="lp-num w-full rounded-lg bg-surface-container-low px-4 py-3 font-numeric-md text-numeric-md text-on-surface border border-outline-variant/40 focus:border-primary focus:outline-none">
                    </label>
                    <label class="flex flex-col gap-1.5">
                      <span class="font-label-md text-label-md text-on-surface font-semibold">Plazo: <span
                          id="cr-plazo-out" class="text-primary font-bold">24 meses</span></span>
                      <input id="cr-plazo" type="range" min="6" max="84" step="1" value="24"
                        class="w-full accent-primary mt-3">
                    </label>
                  </div>
                  <label class="flex flex-col gap-1.5">
                    <span class="font-label-md text-label-md text-on-surface font-semibold">Tu ingreso mensual (para el
                      semáforo)</span>
                    <input id="cr-ingreso" type="text" inputmode="numeric" autocomplete="off" value="3.850.000"
                      class="lp-num w-full rounded-lg bg-surface-container-low px-4 py-3 font-numeric-md text-numeric-md text-on-surface border border-outline-variant/40 focus:border-primary focus:outline-none">
                  </label>
                </div>

                <div class="bg-surface-container-low rounded-2xl p-5 flex flex-col justify-center gap-4">
                  <p id="cr-vacio" class="font-body-md text-body-md text-secondary text-center" hidden>Escribe un monto
                    para calcular tu cuota.</p>
                  <div id="cr-resultado" class="flex flex-col gap-4">
                    <div class="flex flex-col">
                      <span class="font-label-sm text-label-sm text-secondary uppercase font-bold tracking-wider">Cuota
                        mensual</span>
                      <span id="cr-cuota" class="lp-num font-display-lg text-display-lg text-primary font-bold">$0</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 font-label-sm text-label-sm">
                      <div class="flex flex-col"><span class="text-secondary">Total a pagar</span><span id="cr-total"
                          class="lp-num font-numeric-md text-numeric-md font-bold text-on-surface">$0</span></div>
                      <div class="flex flex-col"><span class="text-secondary">Intereses</span><span id="cr-intereses"
                          class="lp-num font-numeric-md text-numeric-md font-bold text-on-surface">$0</span></div>
                    </div>

                    <div id="cr-semaforo" class="flex flex-col gap-3">
                      <div class="flex items-center justify-between">
                        <span class="font-label-sm text-label-sm text-secondary">La cuota es <strong id="cr-pct"
                            class="lp-num text-on-surface">0%</strong> de tu ingreso</span>
                        <span id="cr-chip-ok"
                          class="px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed font-label-sm text-label-sm font-bold">Saludable</span>
                        {{-- CAMBIO 5: chip moderado -> bg-want-soft text-want --}}
                        <span id="cr-chip-mid"
                          class="px-2.5 py-0.5 rounded-full bg-want-soft text-want font-label-sm text-label-sm font-bold"
                          hidden>Moderado</span>
                        <span id="cr-chip-bad"
                          class="px-2.5 py-0.5 rounded-full bg-error-container text-error font-label-sm text-label-sm font-bold"
                          hidden>Riesgo</span>
                      </div>
                      <div class="relative flex gap-0.5 h-2.5 mt-1">
                        <div id="cr-seg-ok" class="lp-seg is-active w-[40%] rounded-l-full bg-primary"></div>
                        {{-- CAMBIO 6: bg-tertiary-container -> bg-want --}}
                        <div id="cr-seg-mid" class="lp-seg w-[20%] bg-want"></div>
                        <div id="cr-seg-bad" class="lp-seg w-[40%] rounded-r-full bg-error"></div>
                        <div id="cr-marker" class="lp-marker bg-on-surface"></div>
                      </div>
                      <div class="flex justify-between font-label-sm text-[11px] text-secondary">
                        <span>0%</span><span>20%</span><span>30%</span><span>50%+</span>
                      </div>
                    </div>
                    <p id="cr-sin-ingreso" class="font-label-sm text-label-sm text-secondary" hidden>Agrega tu ingreso
                      para ver el semáforo de endeudamiento.</p>
                  </div>
                </div>
              </div>

              <p class="font-label-sm text-label-sm text-secondary text-center">
                Cálculo orientativo con sistema francés. Son simulaciones, no créditos reales.
                <span class="block mt-1 text-secondary/80">Con tu cuenta, cada simulación queda guardada para
                  comparar.</span>
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- CARACTERÍSTICAS -->
      <!-- ============================================================== -->
      <section id="caracteristicas" class="w-full py-16 bg-surface-container-low/60">
        <div class="max-w-7xl mx-auto px-margin flex flex-col gap-10">
          <div class="lp-reveal flex flex-col items-center text-center gap-3 max-w-2xl mx-auto">
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">Menos hojas de cálculo</h2>
            <p class="font-body-md text-body-md text-on-surface-variant">Cada herramienta resuelve un estrés concreto:
              olvidar un pago, no saber en qué se va la plata o endeudarte de más.</p>
          </div>

          <div class="lp-reveal lp-stagger grid grid-cols-1 md:grid-cols-3 gap-5">

            <div
              class="lp-tilt lp-lift flex flex-col bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-lg">
              <div class="w-12 h-12 rounded-xl bg-primary-fixed/50 flex items-center justify-center mb-4">
                <span class="lp-wiggle material-symbols-outlined text-[26px] text-primary">alarm_on</span>
              </div>
              <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold mb-2">Nunca más un pago
                vencido</h3>
              <p class="font-body-md text-body-md text-secondary leading-relaxed mb-4">Registra tus gastos fijos y te
                avisamos 3 días antes. Si se pasa la fecha, también.</p>
              <div class="mt-auto bg-surface-container-low p-3.5 rounded-xl flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5"><span
                    class="material-symbols-outlined text-primary text-[18px]">bolt</span><span
                    class="font-label-md text-label-md text-on-surface font-medium">Servicios públicos</span></div>
                <span class="px-2.5 py-0.5 rounded-full bg-error/15 text-error font-label-sm text-[11px] font-bold">Vence
                  en 3 días</span>
              </div>
            </div>

            <div
              class="lp-tilt lp-lift flex flex-col bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-lg">
              <div class="w-12 h-12 rounded-xl bg-secondary-container flex items-center justify-center mb-4">
                {{-- CAMBIO 7: text-tertiary -> text-want --}}
                <span class="material-symbols-outlined text-[26px] text-want">category</span>
              </div>
              <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold mb-2">Categorías a tu manera</h3>
              <p class="font-body-md text-body-md text-secondary leading-relaxed mb-4">Usa las del sistema o crea las
                tuyas con su ícono. Si borras una, tus gastos se mueven sin perderse.</p>
              <div class="mt-auto bg-surface-container-low p-3.5 rounded-xl flex items-center gap-2.5 flex-wrap">
                <span class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center"><span
                    class="material-symbols-outlined text-[20px]">home</span></span>
                <span class="w-9 h-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center"><span
                    class="material-symbols-outlined text-[20px]">shopping_cart</span></span>
                {{-- CAMBIO 8: bg-tertiary-fixed text-tertiary -> bg-want-soft text-want --}}
                <span class="w-9 h-9 rounded-lg bg-want-soft text-want flex items-center justify-center"><span
                    class="material-symbols-outlined text-[20px]">movie</span></span>
                {{-- CAMBIO 9: igual que el anterior --}}
                <span class="w-9 h-9 rounded-lg bg-want-soft text-want flex items-center justify-center"><span
                    class="material-symbols-outlined text-[20px]">restaurant</span></span>
                <span class="w-9 h-9 rounded-lg bg-primary-fixed text-primary flex items-center justify-center"><span
                    class="material-symbols-outlined text-[20px]">savings</span></span>
                <span class="w-9 h-9 rounded-lg bg-primary-fixed text-primary flex items-center justify-center"><span
                    class="material-symbols-outlined text-[20px]">flight</span></span>
              </div>
            </div>

            <div
              class="lp-tilt lp-lift flex flex-col bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-lg">
              <div class="w-12 h-12 rounded-xl bg-primary-fixed/50 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-[26px] text-primary">query_stats</span>
              </div>
              <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold mb-2">Tu informe, al día</h3>
              <p class="font-body-md text-body-md text-secondary leading-relaxed mb-4">Cada mes guardamos tu resumen.
                Compara cómo evolucionas sin rearmar la cuenta.</p>
              <div class="mt-auto bg-surface-container-low p-3.5 rounded-xl flex flex-col gap-2">
                <div class="flex justify-between font-label-sm text-label-sm font-semibold"><span>Distribución
                    ideal</span><span class="text-primary">100%</span></div>
                <div class="w-full h-2 rounded-full bg-surface-container-high flex overflow-hidden">
                  <div class="lp-fill lp-w-50 bg-primary h-full"></div>
                  {{-- CAMBIO 10: bg-tertiary-container -> bg-want --}}
                  <div class="lp-fill lp-w-30 bg-want h-full"></div>
                  <div class="lp-fill lp-w-20 bg-primary-fixed-dim h-full"></div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- METAS COMPARTIDAS -->
      <!-- ============================================================== -->
      <section id="metas-familiares" class="w-full py-24 bg-surface relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-margin">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">

            <div class="lg:col-span-6 flex flex-col gap-6 lp-reveal lp-reveal--left">
              <div class="lp-float-slow relative bg-surface-container-lowest rounded-2xl shadow-xl overflow-hidden">

                {{-- IMAGEN #2: pareja con alcancía --}}
                <div class="lp-foto-fade lp-ratio-3x2 bg-surface-container text-outline">
                  <span class="lp-foto__ph material-symbols-outlined" aria-hidden="true">image</span>
                  <img class="lp-img" src="{{ asset('img/meta-couple.jpg') }}"
                    alt="Pareja sonriendo junto a una alcancía, ahorrando para una meta compartida" width="1200"
                    height="800" decoding="async">
                </div>

                <div class="relative z-10 px-6 sm:px-8 pb-6 sm:pb-8 -mt-24">
                  <div class="flex items-center justify-between pb-4">
                    <div class="flex items-center gap-3">
                      <div
                        class="w-12 h-12 rounded-xl bg-primary-fixed flex items-center justify-center text-on-primary-fixed">
                        <span class="material-symbols-outlined text-[26px] text-primary">flight_takeoff</span>
                      </div>
                      <div>
                        <h4 class="font-headline-md text-headline-md text-on-surface font-bold">Vacaciones en pareja
                        </h4>
                        <p class="font-body-sm text-body-sm text-secondary">Meta compartida con 2 personas</p>
                      </div>
                    </div>
                    <span
                      class="px-3 py-1 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm font-bold">Activa</span>
                  </div>

                  <div class="bg-surface-container-low p-5 rounded-xl flex flex-col gap-3 my-2">
                    <div class="flex justify-between items-baseline gap-2 flex-wrap">
                      <span class="font-headline-sm text-headline-sm text-secondary">Total conjunto</span>
                      <span class="lp-num font-numeric-lg text-numeric-lg text-primary font-bold">
                        <span data-count="7200000" data-format="cop">$7.200.000</span>
                        <span class="font-body-sm text-body-sm text-secondary font-normal">/ $10.000.000</span>
                      </span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-surface-container-high overflow-hidden">
                      <div class="lp-fill lp-w-72 bg-primary h-full rounded-full"></div>
                    </div>
                    <div class="flex justify-between font-label-sm text-label-sm text-secondary pt-1">
                      <span>Faltan $2.800.000 COP</span>
                      <span class="font-bold text-on-surface">72% de la meta</span>
                    </div>
                  </div>

                  <div class="grid grid-cols-2 gap-4 mt-2">
                    <div class="bg-surface-container-low p-3.5 rounded-xl flex flex-col gap-1">
                      <div class="flex items-center justify-between">
                        <span class="font-headline-sm text-[13px] text-on-surface font-bold">Tu aporte</span>
                        <span class="material-symbols-outlined text-primary text-[16px]">visibility</span>
                      </div>
                      <p class="lp-num font-numeric-md text-numeric-md text-on-surface font-bold">$3.600.000</p>
                      <p class="font-label-sm text-[10px] text-secondary">Solo tú ves tu historial</p>
                    </div>
                    <div class="bg-surface-container-low p-3.5 rounded-xl flex flex-col gap-1">
                      <div class="flex items-center justify-between">
                        <span class="font-headline-sm text-[13px] text-on-surface font-bold">Aporte de tu pareja</span>
                        <span class="material-symbols-outlined text-primary text-[16px]">lock</span>
                      </div>
                      <p class="font-numeric-md text-numeric-md text-secondary font-bold tracking-widest">••••••</p>
                      <p class="font-label-sm text-[10px] text-secondary">Privado para ti</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="lg:col-span-6 flex flex-col gap-6 lp-reveal lp-reveal--right">
              <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold leading-tight">Ahorren juntos,
                sin perder la privacidad.</h2>
              <p class="font-body-lg text-body-lg text-secondary leading-relaxed">Para la cuota de una vivienda, un
                fondo de emergencias o las próximas vacaciones: ambos ven cuánto falta, nadie ve cuánto puso el
                otro.</p>

              <div class="lp-stagger lp-reveal flex flex-col gap-4 pt-2">
                <div class="flex items-start gap-3">
                  <div
                    class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">flag</span>
                  </div>
                  <div>
                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">Un solo avance para los
                      dos</h4>
                    <p class="font-body-md text-body-md text-secondary">El progreso se consolida solo, sin hojas de
                      cálculo desactualizadas.</p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <div
                    class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">lock</span>
                  </div>
                  <div>
                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">Aportes privados</h4>
                    <p class="font-body-md text-body-md text-secondary">Cada quien ve el total de la meta y su propio
                      historial. Tus gastos personales no se comparten.</p>
                  </div>
                </div>
                <div class="flex items-start gap-3">
                  <div
                    class="w-8 h-8 rounded-full bg-primary-fixed flex items-center justify-center text-primary shrink-0 mt-0.5">
                    <span class="material-symbols-outlined text-[18px]">forward_to_inbox</span>
                  </div>
                  <div>
                    <h4 class="font-headline-sm text-headline-sm text-on-surface font-bold">Invitas, no compartes
                      claves</h4>
                    <p class="font-body-md text-body-md text-secondary">Invitas a tu pareja o familiar y acepta desde
                      su propia cuenta. Máximo 2 personas por meta.</p>
                  </div>
                </div>
              </div>

              {{-- CAMBIO 11: botón "Crear cuenta en pareja" -> acento secundario --}}
              <a href="{{ route('registro', ['tipo' => 'familiar']) }}"
                class="self-start inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-accent-secondary text-on-accent-secondary font-headline-sm text-headline-sm shadow-md hover:shadow-lg transition-all group">
                <span>Crear cuenta en pareja</span>
                <span
                  class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-1">arrow_forward</span>
              </a>
            </div>

          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- CUENTAS -->
      <!-- ============================================================== -->
      <section id="cuentas" class="lp-plans relative w-full overflow-hidden py-24 bg-surface-container-low">

        <div class="lp-plans__bg" aria-hidden="true">
          <div class="lp-plans__dots"></div>
          <span class="lp-plans__ring lp-plans__ring--1"></span>
          <span class="lp-plans__ring lp-plans__ring--2"></span>
          <span class="lp-plans__ring lp-plans__ring--3"></span>
          <div class="lp-drift absolute -top-24 -left-24 w-96 h-96 bg-primary/10 rounded-full blur-[100px]"></div>
          <div
            class="lp-drift absolute -bottom-32 -right-24 w-96 h-96 bg-tertiary-container/15 rounded-full blur-[100px]">
          </div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-margin flex flex-col gap-14">

          <div class="lp-reveal flex flex-col items-center text-center gap-4 max-w-2xl mx-auto">
            <span
              class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-fixed/40 font-label-sm text-label-sm uppercase tracking-widest text-primary font-bold">
              <span class="material-symbols-outlined text-[14px]"
                style="font-variation-settings:'FILL' 1;">workspace_premium</span>
              Elige tu cuenta
            </span>
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">
              Dos cuentas, las dos
              <span
                class="text-primary underline decoration-primary-fixed decoration-wavy underline-offset-8">gratis</span>
            </h2>
            <p class="font-body-md text-body-md text-secondary">Misma seguridad en las dos. Puedes cambiar de tipo
              cuando quieras desde Ajustes.</p>
          </div>

          <div
            class="lp-reveal lp-stagger grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10 max-w-4xl mx-auto w-full items-stretch">

            <!-- Personal -->
            <article class="lp-plan lp-plan--basic lp-lift md:my-5">
              <div class="lp-plan__band">
                <span class="material-symbols-outlined text-[16px]">person</span>Para ti solo
              </div>
              <div class="flex flex-col flex-1 p-8 sm:p-9 gap-6">
                <div class="flex items-center gap-4">
                  <span class="lp-plan__icon"><span
                      class="material-symbols-outlined text-[26px]">account_circle</span></span>
                  <div>
                    <h3 class="font-headline-lg text-headline-lg text-on-surface font-bold">Cuenta personal</h3>
                    <p class="font-body-sm text-body-sm text-secondary">Tú, tu plata y tus metas.</p>
                  </div>
                </div>

                <p class="font-body-md text-body-md text-secondary">Si quieres poner orden a tu plata, tus gastos y
                  tus créditos sin depender de nadie.</p>

                <div class="flex items-baseline gap-2">
                  <span class="font-display-lg text-display-lg text-on-surface font-bold">Gratis</span>
                  <span class="font-label-md text-label-md text-secondary">sin tarjeta</span>
                </div>

                <div class="lp-plan__rule"></div>

                <ul class="flex flex-col gap-3.5 font-body-md text-body-md text-on-surface">
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Gastos diarios y categorías propias</li>
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Gastos fijos con avisos</li>
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Informe mensual guardado</li>
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Simulador con historial</li>
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Metas individuales</li>
                  <li class="lp-plan__off flex items-center gap-3 text-secondary"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">close</span></span><span class="opacity-70">Metas
                      compartidas en pareja</span></li>
                </ul>

                <div class="mt-auto pt-2">
                  <a class="lp-plan__btn lp-plan__btn--ghost font-headline-sm text-headline-sm"
                    href="{{ route('registro', ['tipo' => 'estandar']) }}">
                    Empezar solo
                  </a>
                </div>
              </div>
            </article>

            <!-- Pareja (destacada) -->
            <article class="lp-plan lp-plan--pro lp-lift bg-inverse-surface text-white">
              <svg class="lp-plans__icon lp-plans__duo lp-float-late text-primary" style="top:16%;right:5%;"
                viewBox="0 0 32 24" aria-hidden="true">
                <circle cx="22" cy="8" r="4" opacity=".55" />
                <path d="M14 22c0-4.4 3.6-8 8-8s8 3.6 8 8z" opacity=".55" />
                <circle cx="11" cy="9" r="4.5" />
                <path d="M2 23c0-5 4-9 9-9s9 4 9 9z" />
              </svg>
              <div class="flex flex-col flex-1 p-8 sm:p-9 gap-6">
                <div class="flex items-center gap-4">
                  <span class="lp-plan__icon"><span class="material-symbols-outlined text-[26px]">group</span></span>
                  <div>
                    <h3 class="font-headline-lg text-headline-lg text-white font-bold">Cuenta en pareja</h3>
                    <p class="lp-plan__muted font-body-sm text-body-sm">Ahorrar juntos, sin exponerse.</p>
                  </div>
                </div>

                <p class="lp-plan__muted font-body-md text-body-md">Si quieres ahorrar en equipo hacia una misma meta,
                  sin compartir cada gasto del día.</p>

                <div class="flex items-end justify-between gap-4">
                  <div class="flex items-baseline gap-2">
                    <span class="font-display-lg text-display-lg text-white font-bold">Gratis</span>
                    <span class="lp-plan__muted font-label-md text-label-md">sin tarjeta</span>
                  </div>
                  <span class="lp-plan__pair" title="Hasta 2 personas por meta" aria-label="Hasta 2 personas por meta">
                    <span class="material-symbols-outlined">person</span>
                    <span class="material-symbols-outlined">person</span>
                  </span>
                </div>

                <div class="lp-plan__rule"></div>

                <ul class="flex flex-col gap-3.5 font-body-md text-body-md text-white">
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Todo lo de la cuenta personal</li>
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Metas compartidas (máximo 2 personas)</li>
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Invitaciones desde la app</li>
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Cada quien ve el total, nunca el aporte del
                    otro</li>
                  <li class="flex items-center gap-3"><span class="lp-plan__check"><span
                        class="material-symbols-outlined">check</span></span>Progreso consolidado de la meta</li>
                </ul>

                <div class="mt-auto pt-2">
                  <a class="lp-shine lp-plan__btn lp-plan__btn--accent font-headline-sm text-headline-sm"
                    href="{{ route('registro', ['tipo' => 'familiar']) }}">
                    <span>Empezar en pareja</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                  </a>
                </div>
              </div>
            </article>

          </div>

          <div class="lp-reveal flex flex-wrap items-center justify-center gap-3">
            <span class="lp-plans__perk"><span class="material-symbols-outlined">swap_horiz</span>Cambia de tipo desde
              Ajustes</span>
            <span class="lp-plans__perk"><span class="material-symbols-outlined">credit_card_off</span>Sin tarjeta de
              crédito</span>
            <span class="lp-plans__perk"><span class="material-symbols-outlined">shield_lock</span>Misma seguridad en
              las dos</span>
          </div>

        </div>
      </section>

      <!-- ============================================================== -->
      <!-- ¿TE SUENA? -->
      <!-- ============================================================== -->
      <section class="w-full py-20 bg-surface">
        <div class="max-w-7xl mx-auto px-margin flex flex-col gap-12">
          <div class="lp-reveal flex flex-col items-center text-center gap-3 max-w-2xl mx-auto">
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">¿Te suena?</h2>
            <p class="font-body-md text-body-md text-secondary">Tres situaciones comunes. Si te identificas con
              alguna, esto es para ti.</p>
          </div>

          <div class="lp-reveal lp-stagger lp-situaciones grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="lp-situacion flex flex-col">
              <div class="lp-bubble bg-primary-fixed text-on-primary-fixed rounded-3xl p-7 shadow-sm">
                <span class="material-symbols-outlined text-[28px] opacity-70" aria-hidden="true">balance</span>
                <p class="font-headline-lg text-headline-lg font-bold leading-snug mt-2">¿Quién puso más este mes?</p>
              </div>
              <div class="flex items-start gap-4 mt-9 px-2">
                <div class="w-12 h-12 rounded-full bg-primary text-on-primary flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[24px]">diversity_3</span>
                </div>
                <div class="flex flex-col gap-1.5">
                  <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Vives en pareja</h3>
                  <p class="font-body-md text-body-md text-secondary leading-relaxed">Quieren ahorrar para algo grande
                    sin discutir por quién aporta más. Cada uno ve el avance, no el detalle del otro.</p>
                  <a href="#metas-familiares"
                    class="group inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                    <span>Ver metas compartidas</span>
                    <span
                      class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                  </a>
                </div>
              </div>
            </div>

            <div class="lp-situacion flex flex-col">
              {{-- CAMBIO 12: burbuja "independiente" -> bloque gustos semántico --}}
              <div class="lp-bubble bg-want-soft text-want rounded-3xl p-7 shadow-sm">
                <span class="material-symbols-outlined text-[28px] opacity-70"
                  aria-hidden="true">account_balance_wallet</span>
                <p class="font-headline-lg text-headline-lg font-bold leading-snug mt-2">¿Cuánto me queda de verdad?</p>
              </div>
              <div class="flex items-start gap-4 mt-9 px-2">
                {{-- CAMBIO 13: icono work -> bg-want text-on-want --}}
                <div class="w-12 h-12 rounded-full bg-want text-on-want flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[24px]">work</span>
                </div>
                <div class="flex flex-col gap-1.5">
                  <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Eres independiente</h3>
                  <p class="font-body-md text-body-md text-secondary leading-relaxed">Cada mes tus ingresos cambian.
                    Necesitas saber cuánto entra, cuánto sale y cuánto puedes gastar de verdad.</p>
                  <a href="#probar"
                    class="group inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                    <span>Probar el 50/30/20</span>
                    <span
                      class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                  </a>
                </div>
              </div>
            </div>

            <div class="lp-situacion flex flex-col">
              <div class="lp-bubble bg-secondary-container text-on-secondary-container rounded-3xl p-7 shadow-sm">
                <span class="material-symbols-outlined text-[28px] opacity-70" aria-hidden="true">visibility</span>
                <p class="font-headline-lg text-headline-lg font-bold leading-snug mt-2">¿En qué se me fue la plata?</p>
              </div>
              <div class="flex items-start gap-4 mt-9 px-2">
                <div
                  class="w-12 h-12 rounded-full bg-inverse-surface text-white flex items-center justify-center shrink-0">
                  <span class="material-symbols-outlined text-[24px]">person</span>
                </div>
                <div class="flex flex-col gap-1.5">
                  <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Quieres claridad</h3>
                  <p class="font-body-md text-body-md text-secondary leading-relaxed">No buscas un banco. Solo quieres
                    saber en qué se va tu plata, cuánto te queda y si te alcanza para lo que sigue.</p>
                  <a href="#caracteristicas"
                    class="group inline-flex items-center gap-1 font-label-md text-label-md text-primary font-bold">
                    <span>Ver cómo te ayuda</span>
                    <span
                      class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                  </a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- SEGURIDAD Y PRIVACIDAD -->
      <!-- ============================================================== -->
      <section id="seguridad" class="w-full py-20 bg-surface-container-low border-y border-outline-variant/30">
        <div class="max-w-7xl mx-auto px-margin flex flex-col gap-8">
          <div
            class="lp-reveal lp-reveal--zoom relative overflow-hidden rounded-3xl bg-inverse-surface text-inverse-on-surface shadow-xl">
            <div
              class="lp-drift absolute -top-24 -left-24 w-80 h-80 bg-primary/20 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
              class="lp-drift absolute -bottom-24 -right-24 w-80 h-80 bg-tertiary-container/20 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="lp-trust relative z-10">
              <div class="flex flex-col items-center lg:items-start text-center lg:text-left gap-5">
                <div
                  class="lp-ring w-16 h-16 rounded-2xl bg-primary text-on-primary flex items-center justify-center shadow-lg">
                  <span class="material-symbols-outlined text-[34px]">shield_lock</span>
                </div>
                <h2 class="font-headline-xl text-headline-xl text-white font-bold">Tus datos, bajo tu control</h2>
                <p class="font-body-lg text-body-lg text-white/80 leading-relaxed">Cuatro compromisos simples, sin letra
                  pequeña. Lo tuyo se queda contigo.</p>
                <a href="{{ route('politica') }}"
                  class="inline-flex items-center gap-1 font-label-md text-label-md text-white font-bold underline underline-offset-4">
                  Leer la política de privacidad
                  <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>
              </div>

              <div class="lp-stagger flex flex-col">
                <div class="lp-trust-row">
                  <div class="lp-trust-icon text-white">
                    <span class="material-symbols-outlined text-[24px]">key_off</span>
                  </div>
                  <div class="lp-trust-text">
                    <h4 class="font-headline-sm text-headline-sm text-white font-bold">No conectas tu banco</h4>
                    <p class="font-body-sm text-body-sm text-white/80">Cero claves, cero tokens, cero tarjetas. Tú
                      registras lo que quieras, cuando quieras.</p>
                  </div>
                  <div class="lp-trust-metric">
                    <span class="lp-num block font-numeric-lg text-numeric-lg text-white font-bold">0</span>
                    <span class="block font-label-sm text-[11px] uppercase tracking-wider text-white/80">claves
                      pedidas</span>
                  </div>
                </div>
                <div class="lp-trust-row">
                  <div class="lp-trust-icon text-white">
                    <span class="material-symbols-outlined text-[24px]">visibility_off</span>
                  </div>
                  <div class="lp-trust-text">
                    <h4 class="font-headline-sm text-headline-sm text-white font-bold">Tus números son privados</h4>
                    <p class="font-body-sm text-body-sm text-white/80">Tus gastos, informes y metas son solo tuyos.
                      Nadie más los ve.</p>
                  </div>
                  <div class="lp-trust-metric">
                    <span class="lp-num block font-numeric-lg text-numeric-lg text-white font-bold" data-count="100"
                      data-suffix="%">100%</span>
                    <span class="block font-label-sm text-[11px] uppercase tracking-wider text-white/80">tuyos</span>
                  </div>
                </div>
                <div class="lp-trust-row">
                  <div class="lp-trust-icon text-white">
                    <span class="material-symbols-outlined text-[24px]">block</span>
                  </div>
                  <div class="lp-trust-text">
                    <h4 class="font-headline-sm text-headline-sm text-white font-bold">No vendemos tus datos</h4>
                    <p class="font-body-sm text-body-sm text-white/80">No compartimos tu información con terceros ni
                      la usamos para publicidad.</p>
                  </div>
                  <div class="lp-trust-metric">
                    <span class="lp-num block font-numeric-lg text-numeric-lg text-white font-bold">0</span>
                    <span class="block font-label-sm text-[11px] uppercase tracking-wider text-white/80">terceros</span>
                  </div>
                </div>
                <div class="lp-trust-row">
                  <div class="lp-trust-icon text-white">
                    <span class="material-symbols-outlined text-[24px]">delete_forever</span>
                  </div>
                  <div class="lp-trust-text">
                    <h4 class="font-headline-sm text-headline-sm text-white font-bold">Borras todo cuando quieras</h4>
                    <p class="font-body-sm text-body-sm text-white/80">Elimina tu cuenta y tus datos desde Ajustes. Sin
                      llamar a nadie, sin pedir permiso.</p>
                  </div>
                  <div class="lp-trust-metric">
                    <span class="lp-num block font-numeric-lg text-numeric-lg text-white font-bold">Tú</span>
                    <span class="block font-label-sm text-[11px] uppercase tracking-wider text-white/80">decides</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- PREGUNTAS FRECUENTES -->
      <!-- ============================================================== -->
      <section id="preguntas" class="w-full py-20 bg-surface-container-low/60">
        <div class="max-w-3xl mx-auto px-margin flex flex-col gap-10">
          <div class="lp-reveal flex flex-col items-center text-center gap-3">
            <h2 class="font-headline-xl text-headline-xl text-on-surface font-bold">Preguntas frecuentes</h2>
          </div>

          <div class="lp-reveal lp-stagger flex flex-col gap-3">

            <details class="lp-faq bg-surface-container-lowest rounded-xl shadow-sm">
              <summary
                class="flex items-center justify-between gap-4 p-5 font-headline-sm text-headline-sm text-on-surface font-bold">
                <span>¿Necesito conectar mi banco?</span>
                <span class="lp-faq__icon material-symbols-outlined text-primary">add</span>
              </summary>
              <p class="lp-faq__body px-5 pb-5 font-body-md text-body-md text-secondary leading-relaxed">No. Tú
                registras tus gastos manualmente. Nunca pedimos claves, tokens ni datos de tarjetas.</p>
            </details>

            <details class="lp-faq bg-surface-container-lowest rounded-xl shadow-sm">
              <summary
                class="flex items-center justify-between gap-4 p-5 font-headline-sm text-headline-sm text-on-surface font-bold">
                <span>¿Qué es la regla 50/30/20?</span>
                <span class="lp-faq__icon material-symbols-outlined text-primary">add</span>
              </summary>
              <p class="lp-faq__body px-5 pb-5 font-body-md text-body-md text-secondary leading-relaxed">
                Es una forma de repartir tu ingreso mensual: 50&nbsp;% para necesidades (arriendo, mercado, servicios),
                30&nbsp;% para gustos (salidas, ropa, suscripciones) y 20&nbsp;% para ahorro. Cuentas Claras agrupa tus
                gastos en estos tres bloques y te muestra si estás por encima o por debajo en cada uno.
              </p>
            </details>

            <details class="lp-faq bg-surface-container-lowest rounded-xl shadow-sm">
              <summary
                class="flex items-center justify-between gap-4 p-5 font-headline-sm text-headline-sm text-on-surface font-bold">
                <span>¿Mi pareja puede ver mis gastos?</span>
                <span class="lp-faq__icon material-symbols-outlined text-primary">add</span>
              </summary>
              <p class="lp-faq__body px-5 pb-5 font-body-md text-body-md text-secondary leading-relaxed">No. En una
                meta compartida, cada quien ve el total conjunto y su propio historial de aportes. Tus gastos
                personales no se comparten.</p>
            </details>

            <details class="lp-faq bg-surface-container-lowest rounded-xl shadow-sm">
              <summary
                class="flex items-center justify-between gap-4 p-5 font-headline-sm text-headline-sm text-on-surface font-bold">
                <span>¿Funciona en el celular?</span>
                <span class="lp-faq__icon material-symbols-outlined text-primary">add</span>
              </summary>
              <p class="lp-faq__body px-5 pb-5 font-body-md text-body-md text-secondary leading-relaxed">Sí. Funciona
                en el navegador de tu celular, tablet o computador. No necesitas descargar nada.</p>
            </details>

            <details class="lp-faq bg-surface-container-lowest rounded-xl shadow-sm">
              <summary
                class="flex items-center justify-between gap-4 p-5 font-headline-sm text-headline-sm text-on-surface font-bold">
                <span>¿Puedo cambiar de cuenta personal a cuenta en pareja?</span>
                <span class="lp-faq__icon material-symbols-outlined text-primary">add</span>
              </summary>
              <p class="lp-faq__body px-5 pb-5 font-body-md text-body-md text-secondary leading-relaxed">Sí, desde
                Ajustes cuando quieras. De pareja a personal también, siempre que no tengas metas compartidas
                activas.</p>
            </details>

            <details class="lp-faq bg-surface-container-lowest rounded-xl shadow-sm">
              <summary
                class="flex items-center justify-between gap-4 p-5 font-headline-sm text-headline-sm text-on-surface font-bold">
                <span>¿El simulador solicita un crédito real?</span>
                <span class="lp-faq__icon material-symbols-outlined text-primary">add</span>
              </summary>
              <p class="lp-faq__body px-5 pb-5 font-body-md text-body-md text-secondary leading-relaxed">No. Solo
                calcula la cuota con sistema francés para que decidas con información. No reportamos nada a centrales
                de riesgo.</p>
            </details>

            <details class="lp-faq bg-surface-container-lowest rounded-xl shadow-sm">
              <summary
                class="flex items-center justify-between gap-4 p-5 font-headline-sm text-headline-sm text-on-surface font-bold">
                <span>¿Tiene costo?</span>
                <span class="lp-faq__icon material-symbols-outlined text-primary">add</span>
              </summary>
              <p class="lp-faq__body px-5 pb-5 font-body-md text-body-md text-secondary leading-relaxed">Las dos
                modalidades son gratuitas. Consulta los <a class="text-primary underline underline-offset-2"
                  href="{{ route('terminos') }}">términos y condiciones</a> para los detalles de uso.</p>
            </details>

          </div>
        </div>
      </section>

      <!-- ============================================================== -->
      <!-- CTA FINAL -->
      <!-- ============================================================== -->
      <section class="w-full pt-12 bg-surface pb-8">
        <div class="">
          <div
            class="lp-reveal lp-reveal--zoom relative bg-inverse-surface text-inverse-on-surface overflow-hidden p-10 sm:p-16 lg:py-20 lg:pl-40 flex flex-col items-center text-center lg:items-start lg:text-left shadow-xl">

            {{-- IMAGEN #3: pareja planeando, lado derecho con máscara --}}
            <div class="lp-cta-bg absolute -top-16 inset-y-0 right-0 w-full lg:w-[62%] pointer-events-none"
              aria-hidden="true">
              <img class="lp-img" src="{{ asset('img/cuenta-familiar.jpg') }}" alt="" width="1200" height="800"
                loading="lazy" decoding="async">
            </div>

            <div
              class="lp-drift absolute -top-24 -left-24 w-80 h-80 bg-primary/20 rounded-full blur-3xl pointer-events-none">
            </div>
            <div
              class="lp-drift absolute -bottom-24 -right-24 w-80 h-80 bg-tertiary-container/20 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="relative z-10 flex flex-col items-center lg:items-start gap-6 max-w-2xl lg:max-w-xl">
              <h2 class="font-display-lg text-display-lg font-bold text-white tracking-tight">
                Empieza hoy. Gratis.
              </h2>
              <p class="font-body-lg text-body-lg text-white/80 leading-relaxed">
                Crea tu cuenta en menos de 3 minutos. Sin tarjeta, sin integrar tu banco, sin compromisos.
              </p>
              <div class="flex flex-col sm:flex-row items-center gap-4 pt-4 w-full sm:w-auto">
                <a class="lp-shine w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-headline-sm text-headline-sm shadow-lg transition-transform active:scale-95"
                  href="{{ route('registro', ['tipo' => 'estandar']) }}">
                  <span>Crear mi cuenta</span>
                  <span class="material-symbols-outlined text-[20px]">bolt</span>
                </a>
                <a class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-4 rounded-xl text-white/80 hover:text-white font-label-md text-label-md"
                  href="{{ route('login') }}">
                  Ya tengo cuenta · Iniciar sesión
                </a>
              </div>
            </div>

          </div>
        </div>
      </section>

    </div>
  </main>
@endsection

@push('scripts')
  <script src="{{ asset('js/landing-page.js') }}" defer></script>

  @verbatim
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "SoftwareApplication",
    "name": "Cuentas Claras",
    "operatingSystem": "Web",
    "applicationCategory": "FinanceApplication",
    "description": "Plataforma web para la administración de finanzas personales, simulación de créditos y metas de ahorro compartidas.",
    "offers": {
      "@type": "Offer",
      "price": "0",
      "priceCurrency": "COP"
    }
  }
  </script>
  @endverbatim

  <script src="{{ asset('js/landing-header.js') }}" defer></script>
@endpush