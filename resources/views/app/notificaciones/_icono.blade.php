{{--
  Icono de una notificación (Material Symbols).
  Uso: @include('app.notificaciones._icono', ['tipo' => $item->tipo])
--}}
<div class="notificacion-icono" aria-hidden="true">
  <span class="material-symbols-outlined">
    {{ match ($tipo) {
        'por_vencer' => 'schedule',
        'vencido' => 'warning',
        'invitacion_meta', 'invitacion_meta_requiere_familiar' => 'mail',
        'respuesta_meta' => 'forum',
        'meta_finalizada' => 'celebration',
        'meta_abandonada' => 'push_pin',
        'informe_disponible' => 'description',
        default => 'notifications',
    } }}
  </span>
</div>