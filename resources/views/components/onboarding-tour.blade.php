{{--
==============================================================================
ARCHIVO : resources/views/components/onboarding-tour.blade.php
USO     : <x-onboarding-tour name="dashboard" /> al final del contenido.
HACE    : carga el CSS + JS y arranca el tour si no se ha visto antes.
==============================================================================
--}}
@props(['name' => 'dashboard', 'autoStart' => true])

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/onboarding.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('js/onboarding.js') }}" defer></script>
  @if ($autoStart)
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        if (window.CCOnboarding) {
          window.CCOnboarding.autoStart(@json($name));
        }
      });
    </script>
  @endif
@endpush