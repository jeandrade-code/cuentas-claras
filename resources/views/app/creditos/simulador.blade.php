{{-- 
  ==============================================================================
  ARCHIVO : resources/views/app/creditos/simulador.blade.php
  QUÉ ES  : Simulador de créditos.
  RUTA    : GET /creditos/simulador
  DATOS DEL CONTROLADOR:
    - $ingresoMensual
    - $simulaciones
  ============================================================================== 
--}}

@extends('layouts.app')

@section('titulo', 'Simulador de Créditos · Cuentas Claras')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/simulador-de-creditos.css') }}">
@endpush

@section('content')
  <main class="relative pt-16 w-full px-margin min-h-screen bg-surface">
    <div class="flex flex-col w-full pb-16">

      {{-- Encabezado --}}
      <div class="mb-6">
        <div class="flex flex-col gap-2">
          <span class="font-label-sm text-label-sm text-primary uppercase tracking-wide">
            Cuentas Claras
          </span>

          <h1 class="font-headline-xl text-headline-xl text-on-surface tracking-tight">
            Simulador de Créditos
          </h1>

          <p class="font-body-md text-body-md text-on-surface-variant">
            Calcula la cuota mensual, el total a pagar y los intereses de un crédito.
          </p>
        </div>
      </div>

      {{-- Mensaje de éxito --}}
      @if (session('exito'))
        <div class="mb-6 p-4 rounded-xl bg-primary/10 text-primary flex items-center gap-2">
          <span class="material-symbols-outlined text-[20px]">check_circle</span>
          <span class="font-body-md text-body-md">
            {{ session('exito') }}
          </span>
        </div>
      @endif

      {{-- Contenido principal --}}
      <div class="grid grid-cols-1 xl:grid-cols-12 gap-gutter items-start">

        {{-- ============================================================= --}}
        {{-- FORMULARIO --}}
        {{-- ============================================================= --}}
        <div class="xl:col-span-7" data-tour="sim-form">

          <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm">

            <div class="flex items-center gap-2 mb-6">
              <span class="w-1 h-10 bg-primary rounded-full"></span>

              <div>
                <h2 class="font-headline-md text-headline-md text-on-surface">
                  Datos del crédito
                </h2>

                <p class="font-body-sm text-body-sm text-on-surface-variant">
                  Ingresa las condiciones que deseas simular.
                </p>
              </div>
            </div>

            <form
              class="flex flex-col gap-5"
              id="loanForm"
              onsubmit="event.preventDefault(); window.recalculateLoan();"
            >

              {{-- Nombre --}}
              <div class="flex flex-col gap-1.5">
                <label
                  class="font-label-md text-label-md text-on-surface"
                  for="loanName"
                >
                  Nombre de la simulación
                </label>

                <input
                  class="w-full h-11 px-4 bg-surface-container-low text-on-surface font-body-md text-body-md rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/40"
                  id="loanName"
                  placeholder="Ej. Compra de vehículo"
                  type="text"
                  value=""

                >
              </div>

              {{-- Monto --}}
              <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between gap-3">
                  <label
                    class="font-label-md text-label-md text-on-surface"
                    for="amountInput"
                  >
                    Monto a financiar
                  </label>

                  <div class="flex items-center gap-1 bg-surface-container-low px-3 py-1.5 rounded-lg">
                    <span class="font-numeric-md text-numeric-md text-on-surface-variant">
                      $
                    </span>

                    <input
    class="w-36 text-right font-numeric-md text-numeric-md text-on-surface bg-transparent focus:outline-none"
    id="amountInput"
    max="150000000"
    min="1"
    step="1"
    type="text"
    inputmode="decimal"
    value=""
>

                    <span class="font-label-sm text-label-sm text-outline">
                      COP
                    </span>
                  </div>
                </div>

                <input
    id="amountSlider"
    max="150000000"
    min="50000"
    step="50000"
    type="range"
    value=""
>

                <div class="flex justify-between font-label-sm text-label-sm text-outline-variant">
    <span>$50.000</span>
    <span>$75.000.000</span>
    <span>$150.000.000</span>
</div>
              </div>

              {{-- Tasa --}}
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="flex flex-col gap-1.5">
                  <label
                    class="font-label-md text-label-md text-on-surface"
                    for="rateInput"
                  >
                    Tasa mensual
                  </label>

                  <div class="relative">
                    <input
                      class="w-full h-11 px-3 pr-8 bg-surface-container-low font-numeric-md text-numeric-md text-on-surface rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/40"
                      id="rateInput"
                      max="5.0"
                      min="0.1"
                      step="0.05"
                      type="number"
                      value=""
                    >

                    <span class="absolute right-3 top-1/2 -translate-y-1/2 font-label-md text-label-md text-outline">
                      %
                    </span>
                  </div>

                  <span class="font-body-sm text-body-sm text-on-surface-variant">
                    Tasa mensual vencida (M.V.)
                  </span>
                </div>

                <div class="flex flex-col gap-1.5">
                  <span class="font-label-md text-label-md text-on-surface">
                    Tasa efectiva anual
                  </span>

                  <div
                    class="h-11 px-3 flex items-center justify-between bg-surface-container-low rounded-lg"
                  >
                    <span
                      class="font-numeric-md text-numeric-md text-on-surface"
                      id="eaRateDisplay"
                    >
                      23.87%
                    </span>

                    <span class="font-label-sm text-label-sm text-outline">
                      E.A.
                    </span>
                  </div>

                  <span class="font-body-sm text-body-sm text-on-surface-variant">
                    Equivalente calculado automáticamente.
                  </span>
                </div>

              </div>

              {{-- Plazo --}}
<div class="flex flex-col gap-2">

  <div class="flex items-center justify-between gap-3">

    <label
      class="font-label-md text-label-md text-on-surface"
      for="termInput"
    >
      Plazo
    </label>

    <div class="flex items-center gap-1">

      <input
        id="termInput"
        type="number"
        min="2"
        max="60"
        step="1"
        value=""
        class="w-20 h-10 px-2 text-center font-numeric-md text-numeric-md text-primary bg-surface-container-low rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/40"
      >

      <span class="font-label-md text-label-md text-on-surface-variant">
        meses
      </span>

    </div>

  </div>

  <div
    class="grid grid-cols-5 gap-2"
    id="termButtonsContainer"
  >

    <button
      class="term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center"
      data-term="12"
      type="button"
    >
      12 m
    </button>

    <button
      class="term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center"
      data-term="24"
      type="button"
    >
      24 m
    </button>

    <button
      class="term-btn py-2.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm transition-all text-center"
      data-term="36"
      type="button"
    >
      36 m
    </button>

    <button
      class="term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center"
      data-term="48"
      type="button"
    >
      48 m
    </button>

    <button
      class="term-btn py-2.5 rounded-lg bg-surface-container font-label-md text-label-md text-on-surface hover:bg-surface-container-high transition-all text-center"
      data-term="60"
      type="button"
    >
      60 m
    </button>

  </div>

  <span class="font-body-sm text-body-sm text-on-surface-variant">
    Puedes ingresar cualquier plazo entre 12 y 60 meses.
  </span>

</div>

              {{-- Ingreso mensual --}}
              <div class="p-4 bg-surface-container-low rounded-xl">
                <div class="flex items-center justify-between gap-4">

                  <div>
                    <span class="font-label-md text-label-md text-on-surface block">
                      Ingreso mensual
                    </span>

                    <span class="font-body-sm text-body-sm text-on-surface-variant">
                      Tomado de tu perfil
                    </span>
                  </div>

                  <div class="text-right">
                    <span class="font-numeric-md text-numeric-md text-on-surface">
                      ${{ number_format($ingresoMensual ?? 0, 0, ',', '.') }}
                    </span>

                    <span class="font-label-sm text-label-sm text-outline block">
                      COP / mes
                    </span>
                  </div>

                </div>
              </div>

              {{-- Botones --}}
              <div class="flex flex-wrap items-center justify-between gap-3 pt-2">

                <button
                  class="flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md transition-colors"
                  id="resetBtn"
                  type="button"
                >
                  <span class="material-symbols-outlined text-[18px]">
                    restart_alt
                  </span>

                  Limpiar
                </button>

                <div class="flex flex-wrap gap-3">

                 
<button
    class="flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-secondary-container hover:bg-secondary-fixed text-on-secondary-container font-label-md text-label-md transition-colors shadow-sm"
    id="saveSimBtn"
    type="button"
>
    <span class="material-symbols-outlined text-[18px]">
        bookmark_add
    </span>

    Guardar
</button>

<a
    class="flex items-center gap-1.5 px-4 py-2.5 rounded-lg border border-outline-variant hover:bg-surface-container-high text-on-surface font-label-md text-label-md transition-colors"
    href="{{ route('creditos.historial') }}"
>
    <span class="material-symbols-outlined text-[18px]">
        history
    </span>

    Historial
</a>

<button
    class="flex items-center gap-1.5 px-5 py-2.5 rounded-lg bg-primary hover:bg-primary-container text-on-primary font-label-md text-label-md shadow-md transition-all active:scale-95"
    type="submit"
>
    <span class="material-symbols-outlined text-[18px]">
        calculate
    </span>

    Recalcular
</button>



                </div>
              </div>

            </form>
          </div>
        </div>

        {{-- ============================================================= --}}
        {{-- RESULTADOS --}}
        {{-- ============================================================= --}}
        <div class="xl:col-span-5" data-tour="sim-resultados">

          <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-sm">

            <div class="flex items-center justify-between mb-5">
              <h2 class="font-headline-md text-headline-md text-on-surface">
                Resultado
              </h2>

              <span class="px-2.5 py-1 rounded-full bg-primary/10 text-primary font-label-sm text-label-sm">
                Simulación
              </span>
            </div>

            {{-- Cuota --}}
            <div class="p-5 bg-surface-container-low rounded-xl mb-4">
              <span class="font-label-md text-label-md text-on-surface-variant">
                Cuota mensual
              </span>

              <div class="flex items-baseline gap-2 mt-1">
                <span
                  class="font-display-lg text-display-lg text-primary tracking-tight"
                  id="installmentDisplay"
                >
                  $569.757
                </span>

                <span class="font-headline-sm text-headline-sm text-outline font-normal">
                  COP
                </span>
              </div>
            </div>

            {{-- Total e intereses --}}
            <div class="grid grid-cols-2 gap-3 mb-5">

              <div class="p-4 bg-surface-container rounded-lg">
                <span class="font-label-sm text-label-sm text-on-surface-variant block">
                  Total a pagar
                </span>

                <span
                  class="font-numeric-md text-numeric-md text-on-surface"
                  id="totalPaymentDisplay"
                >
                  $20.511.269
                </span>
              </div>

              <div class="p-4 bg-surface-container rounded-lg">
                <span class="font-label-sm text-label-sm text-on-surface-variant block">
                  Intereses
                </span>

                <span
                  class="font-numeric-md text-numeric-md text-tertiary"
                  id="totalInterestDisplay"
                >
                  $5.511.269
                </span>
              </div>

            </div>

            {{-- Distribución capital/intereses --}}
            <div class="flex flex-col gap-2 mb-5">

              <div class="flex justify-between font-label-sm text-label-sm text-on-surface-variant">
                <span>Capital</span>
                <span>Intereses</span>
              </div>

              <div class="w-full h-3 bg-surface-container-highest rounded-full overflow-hidden flex">
                <div
                  class="h-full bg-primary transition-all duration-500"
                  id="capitalBarSegment"
                  style="width: 73%;"
                ></div>

                <div
                class="h-full transition-all duration-500"
                id="interestBarSegment"
                style="width: 27%;"
                ></div>
              </div>

            </div>

            {{-- Capacidad de endeudamiento --}}
            <div class="p-4 bg-surface rounded-xl">

              <div class="flex items-center justify-between mb-3">

                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary text-[20px]">
                    speed
                  </span>

                  <h3 class="font-headline-sm text-headline-sm text-on-surface">
                    Capacidad de endeudamiento
                  </h3>
                </div>

                <span
                  class="px-2.5 py-0.5 rounded-full bg-primary/15 text-primary font-label-sm text-label-sm flex items-center gap-1.5"
                  id="semaphoreBadge"
                >
                  <span class="w-2 h-2 rounded-full bg-primary"></span>
                  Rango saludable
                </span>

              </div>

              <div class="flex flex-col gap-2">

                <div class="flex justify-between items-center">
                  <span class="font-body-sm text-body-sm text-on-surface-variant">
                    Compromiso del ingreso
                  </span>

                  <span
                    class="font-numeric-md text-numeric-md text-primary font-bold"
                    id="debtRatioDisplay"
                  >
                    14.8%
                  </span>
                </div>

                <div class="relative w-full h-3 bg-surface-container-highest rounded-full overflow-hidden">
                  <div
                    class="h-full bg-primary rounded-full transition-all duration-500"
                    id="debtRatioBar"
                    style="width: 14.8%;"
                  ></div>
                </div>

                <div class="flex justify-between font-label-sm text-label-sm text-outline">
                  <span>0%</span>
                  <span>30% recomendado</span>
                  <span>40%+</span>
                </div>

              </div>

              <p
                class="font-body-sm text-body-sm text-on-surface-variant leading-normal bg-surface-container-lowest p-3 rounded-lg mt-3"
                id="debtEvaluationText"
              >
                <strong class="text-on-surface">Diagnóstico:</strong>
                Esta cuota representa un porcentaje de tus ingresos mensuales.
              </p>

            </div>

          </div>
        </div>
      </div>

      {{-- ============================================================= --}}
      {{-- TABLA DE AMORTIZACIÓN --}}
      {{-- ============================================================= --}}
      <div class="mt-8 bg-surface-container-lowest rounded-xl p-space-lg shadow-sm" data-tour="sim-tabla">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4">

          <div>
            <h2 class="font-headline-md text-headline-md text-on-surface">
              Tabla de amortización
            </h2>

            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
              Consulta cómo se distribuye cada cuota entre intereses y capital.
            </p>
          </div>

          <button
            type="button"
            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container text-on-surface-variant hover:text-on-surface font-label-md text-label-md transition-colors"
            id="toggleTableBtn"
          >
            <span
              class="material-symbols-outlined text-[18px]"
              id="toggleIcon"
            >
              expand_less
            </span>

            <span id="toggleText">
              Ocultar Detalle
            </span>
          </button>

        </div>

        <div
          class="overflow-x-auto transition-all duration-300"
          id="tableContainer"
        >
          <table class="w-full text-left font-body-sm text-body-sm">

            <thead>
              <tr class="bg-surface-container-low text-on-surface-variant font-label-md text-label-md">
                <th class="py-3 px-4 rounded-l-lg">Periodo</th>
                <th class="py-3 px-4">Saldo inicial</th>
                <th class="py-3 px-4">Cuota</th>
                <th class="py-3 px-4">Intereses</th>
                <th class="py-3 px-4">Abono a capital</th>
                <th class="py-3 px-4 rounded-r-lg text-right">Saldo final</th>
              </tr>
            </thead>

            <tbody
              class="text-on-surface"
              id="amortizationBody"
            >
              {{-- El JavaScript genera las filas --}}
            </tbody>

          </table>
        </div>

        <div class="flex items-center justify-between pt-4 text-on-surface-variant font-body-sm text-body-sm">
          <span>
            Mostrando los primeros 6 periodos.
          </span>

          <span class="font-label-sm text-label-sm text-outline">
            Valores expresados en COP.
          </span>
        </div>

      </div>

      {{-- Elementos que necesita el JavaScript para cálculos --}}
      <div class="hidden">
        <span id="endYearDisplay"></span>
        <span id="costMultiplierDisplay"></span>
      </div>

      {{-- Formulario oculto para guardar la simulación --}}
      <form
        id="saveSimForm"
        class="hidden"
        method="POST"
        action="{{ route('creditos.guardar') }}"
      >
        @csrf

        <input type="hidden" name="nombre">
        <input type="hidden" name="monto">
        <input type="hidden" name="tasa">
        <input type="hidden" name="plazo">
      </form>

      {{-- Toast de respaldo para el JavaScript --}}
      <div
        class="fixed bottom-6 right-6 bg-inverse-surface text-inverse-on-surface px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 transition-opacity duration-300 opacity-0 pointer-events-none z-50"
        id="saveToast"
      >
        <span class="material-symbols-outlined text-primary-fixed text-[22px]">
          check_circle
        </span>

        <span
          class="font-body-md text-body-md"
          id="toastMessage"
        >
          Simulación guardada correctamente.
        </span>
      </div>

    </div>
  </main>
  {{-- ONBOARDING TOUR --}}
  <x-onboarding-tour name="simulador" :auto-start="true" />
@endsection

@push('scripts')
  <script>
    window.CC = {
      ingreso: {{ (int) ($ingresoMensual ?? 0) }}
    };
  </script>

  <script src="{{ asset('js/simulador-de-creditos.js') }}"></script>
@endpush
