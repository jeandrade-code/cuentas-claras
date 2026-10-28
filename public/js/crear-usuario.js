/*
 * ==============================================================================
 * ARCHIVO : public/js/crear-usuario.js
 
 * QUÉ ES  : Micro-interacciones del formulario de registro.
 * HACE    :
 *   - Alternar entre tarjeta "Personal" y "Familiar" (agrega/remove is-selected).
 *   - Actualizar el input hidden `tipo_cuenta` al cambiar de tarjeta.
 *   - Alternar visibilidad de los campos de contraseña.
 *   - Medidor de fuerza de contraseña (3 barras + check de requisitos).
 * NO HACE : validación real (eso lo hace el servidor en StoreRegistroRequest).
 * ACTUALIZADO 2026-10-22:
 *   - Soporte para la nueva clase .is-selected en las tarjetas.
 *   - Se mantienen las clases viejas (tarjeta-tinte--estandar/familiar) por
 *     compatibilidad con estilos previos.
 * ==============================================================================
 */
(function () {
  'use strict';

  /* ------------------------------------------------------------------ */
  /* Selector de tipo de cuenta (Personal / Familiar)                    */
  /* ------------------------------------------------------------------ */
  var cardStandard = document.getElementById('card-standard');
  var cardFamily   = document.getElementById('card-family');
  var radioStandard = document.getElementById('radio-standard');
  var radioFamily   = document.getElementById('radio-family');
  var hiddenTipo    = document.getElementById('tipo_cuenta');

  function marcarSeleccion(tipo) {
    // tipo: 'standard' | 'family'
    if (cardStandard) {
      cardStandard.classList.toggle('is-selected', tipo === 'standard');
    }
    if (cardFamily) {
      cardFamily.classList.toggle('is-selected', tipo === 'family');
    }

    // Radio visual: el círculo interior se muestra/oculta
    if (radioStandard && radioStandard.firstElementChild) {
      radioStandard.firstElementChild.style.opacity =
        tipo === 'standard' ? '1' : '0';
    }
    if (radioFamily && radioFamily.firstElementChild) {
      radioFamily.firstElementChild.style.opacity =
        tipo === 'family' ? '1' : '0';
    }

    // Actualizar el input hidden que viaja al servidor
    if (hiddenTipo) {
      hiddenTipo.value = tipo === 'family' ? 'familiar' : 'estandar';
    }
  }

  // Exponer globalmente para el onclick del Blade
  window.selectAccountType = function (tipo) {
    marcarSeleccion(tipo);
  };

  // Estado inicial: leer lo que el servidor puso en el hidden (old o request)
  if (hiddenTipo) {
    var inicial = hiddenTipo.value === 'familiar' ? 'family' : 'standard';
    marcarSeleccion(inicial);
  }

  // Accesibilidad: Enter/Space en las tarjetas
  [cardStandard, cardFamily].forEach(function (card) {
    if (!card) return;
    card.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        card.click();
      }
    });
  });

  /* ------------------------------------------------------------------ */
  /* Mostrar/ocultar contraseña                                          */
  /* ------------------------------------------------------------------ */
  window.togglePasswordVisibility = function (inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon  = document.getElementById(iconId);
    if (!input) return;
    var mostrar = input.type === 'password';
    input.type = mostrar ? 'text' : 'password';
    if (icon) {
      icon.textContent = mostrar ? 'visibility_off' : 'visibility';
    }
  };

  /* ------------------------------------------------------------------ */
  /* Medidor de fuerza de contraseña                                     */
  /* ------------------------------------------------------------------ */
  function setCheck(el, ok) {
    if (!el) return;
    var icon = el.querySelector('.material-symbols-outlined');
    if (!icon) return;
    icon.textContent = ok ? 'check_circle' : 'radio_button_unchecked';
    el.classList.toggle('text-primary', ok);
    el.classList.toggle('text-secondary', !ok);
  }

  function setBar(el, colorClass, active) {
    if (!el) return;
    // Limpiar clases de color
    el.classList.remove('bg-error', 'bg-tertiary-container', 'bg-primary',
                         'bg-surface-container-highest');
    el.classList.add(active ? colorClass : 'bg-surface-container-highest');
  }

  window.evaluatePasswordStrength = function (valor) {
    valor = String(valor || '');

    var tieneLen = valor.length >= 8;
    var tieneNum = /\d/.test(valor);
    var tieneSym = /[A-Z!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(valor);

    setCheck(document.getElementById('req-len'), tieneLen);
    setCheck(document.getElementById('req-num'), tieneNum);
    setCheck(document.getElementById('req-sym'), tieneSym);

    var score = [tieneLen, tieneNum, tieneSym].filter(Boolean).length;
    var label = document.getElementById('strength-label');
    var bar1  = document.getElementById('bar-1');
    var bar2  = document.getElementById('bar-2');
    var bar3  = document.getElementById('bar-3');

    // Sin contraseña escrita: estado mínimo
    if (valor.length === 0) {
      if (label) { label.textContent = 'Mínima'; label.className = 'font-label-sm text-label-sm text-secondary font-bold'; }
      setBar(bar1, 'bg-error', false);
      setBar(bar2, 'bg-error', false);
      setBar(bar3, 'bg-error', false);
      return;
    }

    if (score <= 1) {
      if (label) { label.textContent = 'Débil'; label.className = 'font-label-sm text-label-sm text-error font-bold'; }
      setBar(bar1, 'bg-error', true);
      setBar(bar2, 'bg-error', false);
      setBar(bar3, 'bg-error', false);
    } else if (score === 2) {
      if (label) { label.textContent = 'Media'; label.className = 'font-label-sm text-label-sm text-tertiary font-bold'; }
      setBar(bar1, 'bg-tertiary-container', true);
      setBar(bar2, 'bg-tertiary-container', true);
      setBar(bar3, 'bg-tertiary-container', false);
    } else {
      if (label) { label.textContent = 'Fuerte'; label.className = 'font-label-sm text-label-sm text-primary font-bold'; }
      setBar(bar1, 'bg-primary', true);
      setBar(bar2, 'bg-primary', true);
      setBar(bar3, 'bg-primary', true);
    }
  };
})();