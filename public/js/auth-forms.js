/*
 * ==============================================================================
 * ARCHIVO : public/js/auth-forms.js
 * QUÉ ES  : Funciones compartidas por login, recuperar y restablecer contraseña.
 * HACE    : mostrar/ocultar contraseña, medidor de fuerza (opcional).
 * ACTUALIZADO 2026-10-22: centraliza la lógica que antes solo existía en crear-usuario.js.
 * ==============================================================================
 */
(function () {
  'use strict';

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
  /* Medidor de fuerza (solo se activa si existen los elementos)         */
  /* ------------------------------------------------------------------ */
  function setBar(el, colorClass, active) {
    if (!el) return;
    el.classList.remove('bg-error', 'bg-tertiary-container', 'bg-primary',
                         'bg-surface-container-highest');
    el.classList.add(active ? colorClass : 'bg-surface-container-highest');
  }

  window.evaluatePasswordStrength = function (valor) {
    var label = document.getElementById('strength-label');
    if (!label) return; // no hay medidor en esta página

    valor = String(valor || '');
    var tieneLen = valor.length >= 8;
    var tieneNum = /\d/.test(valor);
    var tieneSym = /[A-Z!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(valor);

    var reqLen = document.getElementById('req-len');
    var reqNum = document.getElementById('req-num');
    var reqSym = document.getElementById('req-sym');

    function setCheck(el, ok) {
      if (!el) return;
      var icon = el.querySelector('.material-symbols-outlined');
      if (icon) icon.textContent = ok ? 'check_circle' : 'radio_button_unchecked';
      el.classList.toggle('text-primary', ok);
      el.classList.toggle('text-secondary', !ok);
    }
    setCheck(reqLen, tieneLen);
    setCheck(reqNum, tieneNum);
    setCheck(reqSym, tieneSym);

    var bar1 = document.getElementById('bar-1');
    var bar2 = document.getElementById('bar-2');
    var bar3 = document.getElementById('bar-3');
    var score = [tieneLen, tieneNum, tieneSym].filter(Boolean).length;

    if (valor.length === 0) {
      label.textContent = 'Mínima';
      label.className = 'font-label-sm text-label-sm text-secondary font-bold';
      setBar(bar1, 'bg-error', false);
      setBar(bar2, 'bg-error', false);
      setBar(bar3, 'bg-error', false);
    } else if (score <= 1) {
      label.textContent = 'Débil';
      label.className = 'font-label-sm text-label-sm text-error font-bold';
      setBar(bar1, 'bg-error', true);
      setBar(bar2, 'bg-error', false);
      setBar(bar3, 'bg-error', false);
    } else if (score === 2) {
      label.textContent = 'Media';
      label.className = 'font-label-sm text-label-sm text-tertiary font-bold';
      setBar(bar1, 'bg-tertiary-container', true);
      setBar(bar2, 'bg-tertiary-container', true);
      setBar(bar3, 'bg-tertiary-container', false);
    } else {
      label.textContent = 'Fuerte';
      label.className = 'font-label-sm text-label-sm text-primary font-bold';
      setBar(bar1, 'bg-primary', true);
      setBar(bar2, 'bg-primary', true);
      setBar(bar3, 'bg-primary', true);
    }
  };
})();