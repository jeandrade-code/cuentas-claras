/*
 * ==============================================================================
 * ARCHIVO : public/js/onboarding.js
 * QUÉ ES  : Motor de tour guiado (onboarding). Desktop + mobile.
 * HACE    : overlay con spotlight, tooltip flotante que se posiciona arriba o
 *           abajo del target según haya espacio, pasos, progreso, omitir,
 *           persistencia. NO bloquea el scroll del usuario.
 * USO     : CCOnboarding.start('dashboard') o <button data-tour-start="dashboard">
 * ==============================================================================
 */
(function () {
    'use strict';

    var STORAGE_PREFIX = 'cc-tour:';
    var MOBILE_QUERY = '(max-width: 767px), (max-height: 500px) and (max-width: 1024px)';
    var DEBUG = window.location.search.indexOf('debug-tour') !== -1;

    function log() {
        if (!DEBUG) return;
        var args = Array.prototype.slice.call(arguments);
        args.unshift('[tour]');
        console.log.apply(console, args);
    }

    /* ------------------------------------------------------------------ */
    /* Detección mobile                                                    */
    /* ------------------------------------------------------------------ */
    function isMobile() {
        try { return window.matchMedia(MOBILE_QUERY).matches; }
        catch (e) { return window.innerWidth <= 767; }
    }

    function isElementVisible(el) {
        if (!el) return false;
        // Solo verificamos que el elemento esté renderizado en el DOM, sin importar si está fuera de la pantalla.
        return el.offsetWidth > 0 || el.offsetHeight > 0;
    }

    /* ------------------------------------------------------------------ */
    /* Tours                                                               */
    /* ------------------------------------------------------------------ */
    var tours = {
        dashboard: {
            steps: [
                {
                    target: null,
                    title: '¡Bienvenido a tu panel!',
                    body: 'Te damos un recorrido rápido por las secciones que más vas a usar. Puedes saltártelo cuando quieras y volver a verlo desde Ajustes.',
                },
                {
                    target: '[data-tour="metricas"]',
                    title: 'Tus números del mes',
                    body: 'Aquí ves de un vistazo cuánto tienes disponible, cuánto has gastado y cuántos gastos fijos te faltan por pagar.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="distribucion"]',
                    title: 'Tu distribución 50/30/20',
                    body: 'Compara cómo repartes tu ingreso entre necesidades, gustos y ahorro, y descubre en qué bloque estás desviándote.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="proximos"]',
                    title: 'Próximos vencimientos',
                    body: 'Los gastos fijos que están por vencer. Te avisamos 3 días antes para que no se te pase ninguno.',
                    position: 'top',
                },
                {
                    target: '[data-tour="ultimos"]',
                    title: 'Últimos gastos',
                    body: 'Los movimientos que registraste recientemente. Ideal para revisar y corregir sin abrir el listado completo.',
                    position: 'top',
                },
                {
                    target: '[data-tour="notificaciones"]',
                    title: 'Tus notificaciones',
                    body: 'Aquí aparecen los avisos de vencimientos, invitaciones a metas y respuestas. La campana del topbar te muestra cuántas tienes pendientes.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="menu-lateral"]',
                    mobileTarget: '[data-tour="menu-movil"], [data-nav-toggle], [data-nav-open], .nav-toggle, #nav-toggle',
                    title: 'Menú de navegación',
                    body: 'Desde aquí accedes a gastos, categorías, gastos fijos, créditos, informes y metas. Siempre tienes el mapa completo a la mano.',
                    mobileBody: 'En móvil, toca el botón de menú (☰) arriba para abrir todas las secciones.',
                    position: 'right',
                },
            ],
        },

        'admin-panel': {
            steps: [
                {
                    target: null,
                    title: 'Bienvenido al panel de administración',
                    body: 'Te mostramos las secciones disponibles. Recuerda: desde aquí gestionas usuarios y categorías, nunca verás datos financieros individuales.',
                },
                {
                    target: '[data-tour="kpis"]',
                    title: 'Resumen del sistema',
                    body: 'Usuarios totales, activos, suspendidos y categorías del sistema. Solo conteos agregados, sin montos.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="distribucion-roles"]',
                    title: 'Distribución por rol',
                    body: 'Cuántos usuarios hay en cada rol: estándar, familiar y administrador. Todo con COUNT, sin exponer datos personales.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="ultimos-usuarios"]',
                    title: 'Últimos usuarios registrados',
                    body: 'Los 8 más recientes. Solo nombre, correo, rol y estado; ningún dato financiero.',
                    position: 'top',
                },
                {
                    target: '[data-tour="ultimas-categorias"]',
                    title: 'Categorías del sistema recientes',
                    body: 'Las últimas que creaste o modificaste. Puedes cambiar el ícono de cada una en la sección "Categorías".',
                    position: 'top',
                },
                {
                    target: '[data-tour="sidebar-admin"]',
                    mobileTarget: '[data-tour="menu-movil"], [data-nav-toggle], [data-nav-open], .nav-toggle, #nav-toggle',
                    title: 'Menú de administración',
                    body: 'Panel, usuarios, categorías, estadísticas y auditoría. Cada cambio queda registrado.',
                    mobileBody: 'En móvil, toca el botón de menú (☰) arriba para acceder a panel, usuarios, categorías, estadísticas y auditoría.',
                    position: 'right',
                },
            ],
        },
        gastos: {
            steps: [
                {
                    target: null,
                    title: 'Tus gastos y transacciones',
                    body: 'Aquí puedes registrar cada egreso, ver cuánto has gastado en el mes y revisar tu historial completo.',
                },
                {
                    target: '[data-tour="gastos-form"]',
                    title: 'Registrar un gasto',
                    body: 'Ingresa el monto, la categoría y la fecha. Si es un pago recurrente, cámbialo a "Gasto fijo".',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="gastos-resumen"]',
                    title: 'Resumen del mes',
                    body: 'Un vistazo rápido a cuánto dinero has gastado este mes y cuántos registros llevas.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="gastos-tabla"]',
                    title: 'Tu historial detallado',
                    body: 'Puedes editar o eliminar cualquier gasto desde aquí. Usa la paginación si tienes muchos registros.',
                    position: 'top',
                }
            ]
        },

        'gastos-fijos': {
            steps: [
                {
                    target: null,
                    title: 'Gastos fijos y vencimientos',
                    body: 'Aquí controlas tus pagos recurrentes (arriendo, servicios, etc.) para que nunca se te pase una fecha.',
                },
                {
                    target: '[data-tour="gf-resumen"]',
                    title: 'Estado de tus pagos',
                    body: 'Revisa cuántos gastos has pagado, cuáles están pendientes y si tienes alguno vencido este mes.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="gf-form"]',
                    title: 'Agregar un gasto fijo',
                    body: 'Ingresa el nombre, el monto, el día de vencimiento (del 1 al 31) y su categoría.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="gf-lista"]',
                    title: 'Tus obligaciones mensuales',
                    body: 'Desde aquí puedes marcar como pagado, editar o eliminar cada gasto. Te avisaremos 3 días antes de que venza.',
                    position: 'top',
                }
            ]
        },

        simulador: {
            steps: [
                {
                    target: null,
                    title: 'Simulador de Créditos',
                    body: 'Calcula las cuotas, intereses y el total a pagar antes de adquirir una deuda.',
                },
                {
                    target: '[data-tour="sim-form"]',
                    title: 'Condiciones del crédito',
                    body: 'Ajusta el monto, la tasa de interés mensual y el plazo. Puedes usar el deslizador o escribir las cifras exactas.',
                    position: 'right',
                },
                {
                    target: '[data-tour="sim-resultados"]',
                    title: 'Resultados y semáforo',
                    body: 'Aquí verás la cuota mensual. El semáforo compara la cuota con tu ingreso mensual para indicarte si es una deuda saludable (hasta 20%), moderada o de riesgo.',
                    position: 'left',
                },
                {
                    target: '[data-tour="sim-tabla"]',
                    title: 'Tabla de amortización',
                    body: 'Consulta cómo se distribuye cada pago entre intereses y capital a lo largo del tiempo.',
                    position: 'top',
                }
            ]
        },

        metas: {
            steps: [
                {
                    target: null,
                    title: 'Tus Metas de Ahorro',
                    body: 'Crea objetivos financieros y registra tus aportes para ver tu progreso.',
                },
                {
                    target: '[data-tour="metas-header"]',
                    title: 'Invitaciones (Cuenta Familiar)',
                    body: 'Si cambias a cuenta familiar, podrás compartir metas con otra persona y revisar tus invitaciones desde aquí.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="metas-form"]',
                    title: 'Crear una meta',
                    body: 'Define el nombre, el monto objetivo y la fecha límite. Si tienes cuenta familiar, puedes invitar a alguien por correo.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="metas-lista"]',
                    title: 'Mis metas',
                    body: 'Aquí ves el avance conjunto de tus metas. Si son compartidas, respetamos tu privacidad: solo verás el total, no los aportes individuales del otro.',
                    position: 'top',
                }
            ]
        },

        informes: {
            steps: [
                {
                    target: null,
                    title: 'Tu Informe 50/30/20',
                    body: 'Analiza cómo se distribuye tu dinero según la regla 50% necesidades, 30% gustos y 20% ahorro.',
                },
                {
                    target: '[data-tour="inf-selector"]',
                    title: 'Consultar un mes',
                    body: 'Selecciona el mes que quieres analizar. También puedes consultar informes de meses anteriores que hayas guardado.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="inf-resumen"]',
                    title: 'Resumen del mes',
                    body: 'Una vista rápida de tu ingreso mensual, total gastado y el saldo disponible.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="inf-distribucion"]',
                    title: 'Distribución real vs recomendada',
                    body: 'Compara en qué porcentaje estás gastando en cada bloque. Si una barra excede lo recomendado, te lo indicaremos en rojo.',
                    position: 'top',
                }
            ]
        },
        categorias: {
            steps: [
                {
                    target: null,
                    title: 'Tus Categorías',
                    body: 'Aquí organizas tus gastos. Puedes usar las categorías del sistema o crear las tuyas propias para tener un control más detallado.',
                },
                {
                    target: '[data-tour="cat-form"]',
                    title: 'Crear una categoría',
                    body: 'Dale un nombre y asígnala a uno de los tres bloques de la regla 50/30/20 (necesidades, discrecionales o ahorro).',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="cat-iconos"]',
                    title: 'Personaliza con íconos',
                    body: 'Elige un ícono representativo de un catálogo de 16 opciones para que identifiques tus gastos de un vistazo.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="cat-lista"]',
                    title: 'Tu listado de categorías',
                    body: 'Aquí ves las categorías del sistema (con un candado) y las tuyas. Si intentas borrar una con gastos, te pediremos moverlos a otra primero.',
                    position: 'top',
                }
            ]
        },
                'admin-usuarios': {
            steps: [
                {
                    target: null,
                    title: 'Gestión de Usuarios',
                    body: 'Desde aquí puedes administrar las cuentas de todos los usuarios registrados en la plataforma.',
                },
                {
                    target: '[data-tour="admin-usuarios-aviso"]',
                    title: 'Reglas de seguridad',
                    body: 'Recuerda: el sistema nunca se queda sin un administrador activo y no puedes eliminar tu propia cuenta desde este panel.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="admin-usuarios-filtros"]',
                    title: 'Buscar y filtrar',
                    body: 'Encuentra rápidamente a un usuario por su nombre, correo, rol o estado de cuenta.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="admin-usuarios-lista"]',
                    title: 'Listado de usuarios',
                    body: 'Aquí puedes cambiar el rol de un usuario, suspender su cuenta o eliminarla (para acciones destructivas, se te pedirá tu contraseña).',
                    position: 'top',
                }
            ]
        },

        'admin-categorias': {
            steps: [
                {
                    target: null,
                    title: 'Categorías del Sistema',
                    body: 'Administra el catálogo global de categorías que todos los usuarios ven al registrar sus gastos.',
                },
                {
                    target: '[data-tour="admin-cat-aviso"]',
                    title: '¿Cómo funcionan?',
                    body: 'Cada categoría pertenece a un bloque del 50/30/20. Además, ten en cuenta que las categorías en uso se desactivan, no se borran.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="admin-cat-resumen"]',
                    title: 'Resumen del catálogo',
                    body: 'Un vistazo rápido a cuántas categorías hay en total, cuántas están activas y cuántas inactivas.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="admin-cat-crear"]',
                    title: 'Crear nueva categoría',
                    body: 'Asigna un nombre, un bloque 50/30/20 y elige un ícono representativo para todos los usuarios.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="admin-cat-lista"]',
                    title: 'Listado por bloques',
                    body: 'Puedes editar, activar/desactivar o eliminar categorías (solo si no tienen gastos asociados).',
                    position: 'top',
                }
            ]
        },

        'admin-auditoria': {
            steps: [
                {
                    target: null,
                    title: 'Auditoría del Sistema',
                    body: 'Un registro histórico e inmutable de todas las acciones críticas realizadas por los administradores.',
                },
                {
                    target: '[data-tour="admin-aud-filtros"]',
                    title: 'Filtrar registros',
                    body: 'Busca acciones específicas por tipo (ej. eliminación de usuarios), por administrador o por rango de fechas.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="admin-aud-tabla"]',
                    title: 'Historial detallado',
                    body: 'Aquí ves quién hizo qué, cuándo, desde qué IP, y el estado de los datos antes y después del cambio. Por seguridad, estos registros no se pueden editar ni borrar.',
                    position: 'top',
                }
            ]
        },

        'admin-estadisticas': {
            steps: [
                {
                    target: null,
                    title: 'Estadísticas Generales',
                    body: 'Métricas globales de uso de la plataforma. Recuerda que por privacidad, solo verás conteos y nunca montos ni datos financieros.',
                },
                {
                    target: '[data-tour="admin-est-kpis"]',
                    title: 'Indicadores Principales (KPIs)',
                    body: 'Total de usuarios, cuentas activas, cuentas suspendidas y notificaciones enviadas.',
                    position: 'bottom',
                },
                {
                    target: '[data-tour="admin-est-roles"]',
                    title: 'Distribución por rol',
                    body: 'Observa cómo se divide la base de usuarios entre estándar, familiar y administrador.',
                    position: 'top',
                },
                {
                    target: '[data-tour="admin-est-notif"]',
                    title: 'Notificaciones por tipo',
                    body: 'Descubre cuántos correos de alertas de vencimiento, invitaciones a metas, etc., ha enviado el sistema.',
                    position: 'top',
                }
            ]
        },
    };

    /* ------------------------------------------------------------------ */
    /* Estado                                                              */
    /* ------------------------------------------------------------------ */
    var state = {
        active: null,
        $tip: null,
        $spot: null,
        $blocker: null,
        bound: false,
        currentPos: null,
        currentTarget: null,
        rendering: false,
        _tipAnimTimer: null,
    };

    /* ------------------------------------------------------------------ */
    /* Utilidades                                                          */
    /* ------------------------------------------------------------------ */
    function isDone(name) {
        try { return localStorage.getItem(STORAGE_PREFIX + name) === '1'; }
        catch (e) { return false; }
    }
    function markDone(name) {
        try { localStorage.setItem(STORAGE_PREFIX + name, '1'); } catch (e) { }
    }
    function reset(name) {
        try { localStorage.removeItem(STORAGE_PREFIX + name); } catch (e) { }
    }

    function el(tag, cls, html) {
        var n = document.createElement(tag);
        if (cls) n.className = cls;
        if (html != null) n.innerHTML = html;
        return n;
    }

    /* ------------------------------------------------------------------ */
    /* Build / destroy                                                     */
    /* ------------------------------------------------------------------ */
    function build() {
        destroy();
        state.$blocker = el('div', 'cc-tour__blocker');
        state.$spot = el('div', 'cc-tour__spot');
        state.$tip = el('div', 'cc-tour__tip');
        state.$tip.setAttribute('role', 'dialog');
        state.$tip.setAttribute('aria-modal', 'true');
        state.$tip.setAttribute('aria-live', 'polite');
        state.$tip.style.visibility = 'hidden';

        // appendChild a documentElement (html) en vez de body para evitar que
        // un transform residual en el body rompa position:fixed.
        document.documentElement.appendChild(state.$blocker);
        document.documentElement.appendChild(state.$spot);
        document.documentElement.appendChild(state.$tip);
    }

    function destroy() {
        clearTimeout(state._tipAnimTimer);
        [state.$tip, state.$spot, state.$blocker].forEach(function (n) {
            if (n && n.parentNode) n.parentNode.removeChild(n);
        });
        state.$tip = state.$spot = state.$blocker = null;
        state.currentPos = null;
        state.currentTarget = null;
        state.rendering = false;
    }

    /* ------------------------------------------------------------------ */
    /* Resolver target                                                     */
    /* ------------------------------------------------------------------ */
    function resolveTarget(step) {
        if (!step) return null;
        var mob = isMobile();
        var el;

        if (mob && step.mobileTarget) {
            el = document.querySelector(step.mobileTarget);
            if (el && !isElementVisible(el)) el = null;
        }

        if (!el && step.target) {
            el = document.querySelector(step.target);
            if (mob && el && !isElementVisible(el)) el = null;
        }
        return el;
    }

    /* ------------------------------------------------------------------ */
    /* Render                                                              */
    /* ------------------------------------------------------------------ */
    function render() {
        if (!state.active || state.rendering) return;
        state.rendering = true;

        var steps = state.active.steps;
        var i = state.active.index;
        var step = steps[i];
        if (!step) { state.rendering = false; return finish(); }

        var total = steps.length;
        var isFirst = i === 0;
        var isLast = i === total - 1;
        var pct = Math.round(((i + 1) / total) * 100);
        var mob = isMobile();

        log('render paso', i + 1, 'de', total, 'mob:', mob);

        state.currentPos = null;
        state.currentTarget = null;

        state.$tip.style.visibility = 'hidden';
        state.$tip.classList.remove('cc-tour__tip--entering');
        state.$tip.innerHTML = '';

        var bar = el('div', 'cc-tour__bar');
        bar.appendChild(el('div', 'cc-tour__bar-fill')).style.width = pct + '%';
        state.$tip.appendChild(bar);

        var head = el('div', 'cc-tour__head');
        head.appendChild(el('span', 'cc-tour__step', 'Paso ' + (i + 1) + ' de ' + total));
        var close = el('button', 'cc-tour__close',
            '<span class="material-symbols-outlined">close</span>');
        close.type = 'button';
        close.setAttribute('aria-label', 'Cerrar tour');
        close.addEventListener('click', skipAll);
        head.appendChild(close);
        state.$tip.appendChild(head);

        var bodyText = (mob && step.mobileBody) ? step.mobileBody : step.body;

        var body = el('div', 'cc-tour__body');
        body.appendChild(el('h3', 'cc-tour__title', step.title));
        body.appendChild(el('p', 'cc-tour__text', bodyText));
        state.$tip.appendChild(body);

        var foot = el('div', 'cc-tour__foot');
        var skip = el('button', 'cc-tour__skip', 'Omitir tour');
        skip.type = 'button';
        skip.addEventListener('click', skipAll);
        foot.appendChild(skip);

        var nav = el('div', 'cc-tour__nav');
        var prev = el('button', 'cc-tour__btn cc-tour__btn--ghost',
            '<span class="material-symbols-outlined">arrow_back</span>Anterior');
        prev.type = 'button';
        if (isFirst) prev.disabled = true;
        prev.addEventListener('click', prevStep);
        nav.appendChild(prev);

        var next = el('button', 'cc-tour__btn cc-tour__btn--primary',
            (isLast ? 'Finalizar' : 'Siguiente') +
            (isLast ? '' : '<span class="material-symbols-outlined">arrow_forward</span>'));
        next.type = 'button';
        next.addEventListener('click', isLast ? finish : nextStep);
        nav.appendChild(next);

        foot.appendChild(nav);
        state.$tip.appendChild(foot);

        state.$tip.style.visibility = '';

        var targetEl = resolveTarget(step);
        state.currentTarget = targetEl;

        if (!targetEl) {
            log('paso sin target → pantalla completa');
            placeFull();
            showTipWithAnimation();
            state.rendering = false;
            return;
        }

        log('target encontrado, scrolleando…');

        scrollAndWait(targetEl, mob, function () {
            if (!state.active || state.active.index !== i) {
                log('paso cambiado durante scroll, abortando posicionamiento');
                return;
            }

            var rect = targetEl.getBoundingClientRect();
            log('target rect FINAL:', rect.top, rect.left, rect.width, rect.height);

            placeSpot(targetEl);

            state.currentPos = choosePosition(targetEl, step.position || 'auto');
            applyTipPosition(targetEl, state.currentPos);
            log('pos =', state.currentPos, 'tooltip top =', state.$tip.style.top, 'left =', state.$tip.style.left, 'mob:', mob);

            showTipWithAnimation();
            state.rendering = false;
        });
    }

    /* ------------------------------------------------------------------ */
    /* Scroll + espera a que el RECT del target se estabilice.            */
    /* ------------------------------------------------------------------ */
    function scrollAndWait(targetEl, mob, callback) {
        var html = document.documentElement;

        var styleEl = document.getElementById('cc-tour-nosmooth');
        if (!styleEl) {
            styleEl = document.createElement('style');
            styleEl.id = 'cc-tour-nosmooth';
            styleEl.textContent = 'html, body { scroll-behavior: auto !important; }';
            document.head.appendChild(styleEl);
        }
        var prevSB = html.style.scrollBehavior;
        html.style.scrollBehavior = 'auto';

        // NUEVO: Calculamos la altura de la barra superior (topbar) para no tapar el target
        var topbar = document.querySelector('.topbar') || document.querySelector('header');
        var offset = topbar ? topbar.offsetHeight : 0;

        // NUEVO: Usamos window.scrollTo en lugar de scrollIntoView para un centrado exacto en móviles
        var rect = targetEl.getBoundingClientRect();
        var scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        // Centra el elemento, ajustando por la altura del topbar
        var targetTop = rect.top + scrollTop - (window.innerHeight / 2) + (rect.height / 2) - (offset / 2);
        targetTop = Math.max(0, targetTop); // Evita scroll negativo si está al principio

        window.scrollTo({
            top: targetTop,
            behavior: 'auto'
        });

        // Poll: esperar a que getBoundingClientRect() sea estable 5 frames.
        var lastTop = null, lastLeft = null, lastW = null, lastH = null;
        var stableFrames = 0;
        var attempts = 0;
        var MAX_ATTEMPTS = 90;

        function check() {
            attempts++;
            var r = targetEl.getBoundingClientRect();

            var stable = lastTop !== null
                && Math.abs(r.top - lastTop) < 0.5
                && Math.abs(r.left - lastLeft) < 0.5
                && Math.abs(r.width - lastW) < 0.5
                && Math.abs(r.height - lastH) < 0.5;

            if (stable) {
                stableFrames++;
                if (stableFrames >= 5 || attempts >= MAX_ATTEMPTS) {
                    if (styleEl && styleEl.parentNode) styleEl.parentNode.removeChild(styleEl);
                    html.style.scrollBehavior = prevSB;
                    requestAnimationFrame(callback);
                    return;
                }
            } else {
                stableFrames = 0;
                lastTop = r.top;
                lastLeft = r.left;
                lastW = r.width;
                lastH = r.height;
            }

            requestAnimationFrame(check);
        }

        requestAnimationFrame(check);
    }

    function showTipWithAnimation() {
        if (!state.$tip) return;
        void state.$tip.offsetWidth;
        state.$tip.classList.add('cc-tour__tip--entering');
        clearTimeout(state._tipAnimTimer);
        state._tipAnimTimer = setTimeout(function () {
            if (state.$tip) state.$tip.classList.remove('cc-tour__tip--entering');
        }, 350);
    }

    /* ------------------------------------------------------------------ */
    /* Posicionamiento                                                     */
    /* ------------------------------------------------------------------ */
    function placeSpot(target) {
        if (!state.$spot) return;
        var r = target.getBoundingClientRect();
        var pad = 8;
        var radius = Math.min(parseInt(getComputedStyle(target).borderRadius, 10) || 10, 20);
        state.$spot.classList.remove('cc-tour__spot--full');
        state.$spot.style.top = (r.top - pad) + 'px';
        state.$spot.style.left = (r.left - pad) + 'px';
        state.$spot.style.width = (r.width + pad * 2) + 'px';
        state.$spot.style.height = (r.height + pad * 2) + 'px';
        state.$spot.style.borderRadius = radius + 'px';
        state.$spot.style.background = '';
        state.$spot.style.boxShadow = '';
    }

    function choosePosition(target, prefer) {
        var r = target.getBoundingClientRect();
        var tip = state.$tip;
        var tw = tip.offsetWidth;
        var th = tip.offsetHeight;
        var vw = window.innerWidth;
        var vh = window.innerHeight;
        var gap = isMobile() ? 20 : 16;

        // NUEVO: Evitar que el tooltip se posicione arriba del topbar
        var topbar = document.querySelector('.topbar') || document.querySelector('header');
        var topbarHeight = topbar ? topbar.offsetHeight : 0;

        var candidates = {
            bottom: { top: r.bottom + gap, left: r.left + r.width / 2 - tw / 2 },
            top: { top: r.top - th - gap, left: r.left + r.width / 2 - tw / 2 },
            right: { top: r.top + r.height / 2 - th / 2, left: r.right + gap },
            left: { top: r.top + r.height / 2 - th / 2, left: r.left - tw - gap },
        };

        var order = prefer && candidates[prefer]
            ? [prefer, 'bottom', 'top', 'right', 'left']
            : ['bottom', 'top', 'right', 'left'];

        for (var k = 0; k < order.length; k++) {
            var p = candidates[order[k]];
            if (!p) continue;
            if (p.top >= topbarHeight + 8 && p.top + th <= vh - 8 && p.left >= 8 && p.left + tw <= vw - 8) {
                return order[k];
            }
        }
        return 'bottom';
    }

    function applyTipPosition(target, pos) {
        if (!state.$tip || !target || !pos) return;
        var r = target.getBoundingClientRect();
        var tip = state.$tip;
        var tw = tip.offsetWidth;
        var th = tip.offsetHeight;
        var vw = window.innerWidth;
        var vh = window.innerHeight;
        var gap = isMobile() ? 20 : 16;

        var topbar = document.querySelector('.topbar') || document.querySelector('header');
        var topbarHeight = topbar ? topbar.offsetHeight : 0;

        var p;
        if (pos === 'top') {
            p = { top: r.top - th - gap, left: r.left + r.width / 2 - tw / 2 };
        } else if (pos === 'right') {
            p = { top: r.top + r.height / 2 - th / 2, left: r.right + gap };
        } else if (pos === 'left') {
            p = { top: r.top + r.height / 2 - th / 2, left: r.left - tw - gap };
        } else {
            p = { top: r.bottom + gap, left: r.left + r.width / 2 - tw / 2 };
        }

        // NUEVO: El límite superior ahora es topbarHeight + 8, no solo 8
        var left = Math.max(8, Math.min(p.left, vw - tw - 8));
        var top = Math.max(topbarHeight + 8, Math.min(p.top, vh - th - 8));

        tip.style.top = top + 'px';
        tip.style.left = left + 'px';
    }

    function placeFull() {
        if (!state.$spot || !state.$tip) return;
        state.$spot.style.cssText = '';
        state.$spot.classList.add('cc-tour__spot--full');

        var tip = state.$tip;
        tip.style.top = '';
        tip.style.left = '';
        var left = (window.innerWidth - tip.offsetWidth) / 2;
        var top = (window.innerHeight - tip.offsetHeight) / 2;
        tip.style.left = Math.max(8, left) + 'px';
        tip.style.top = Math.max(8, top) + 'px';
    }

    /* ------------------------------------------------------------------ */
    /* Navegación                                                          */
    /* ------------------------------------------------------------------ */
    function start(name) {
        var t = tours[name];
        if (!t) return;
        state.active = { name: name, steps: t.steps, index: 0 };
        document.documentElement.classList.add('cc-tour-active');
        build();
        render();
        bindGlobal();
    }

    function goTo(i) {
        if (!state.active) return;
        if (i < 0 || i >= state.active.steps.length) return;
        state.active.index = i;
        state.rendering = false;
        render();
    }
    function nextStep() { goTo(state.active.index + 1); }
    function prevStep() { goTo(state.active.index - 1); }

    function finish() {
        var tourName = state.active ? state.active.name : null;
        if (state.active) markDone(state.active.name);
        state.active = null;
        document.documentElement.classList.remove('cc-tour-active');
        destroy();
        unbindGlobal();

        if (tourName) {
            document.dispatchEvent(new CustomEvent('cc-tour:finished', {
                detail: { name: tourName }
            }));
        }
    }
    function skipAll() { finish(); }

    /* ------------------------------------------------------------------ */
    /* Recolocar al hacer scroll / resize — SIN bloquear el scroll        */
    /* ------------------------------------------------------------------ */
    var raf = null;
    function onScrollOrResize() {
        if (!state.active) return;
        if (state.rendering) return;
        if (raf) cancelAnimationFrame(raf);
        raf = requestAnimationFrame(function () {
            var step = state.active.steps[state.active.index];
            if (!step) return;
            var targetEl = resolveTarget(step);
            if (targetEl) {
                placeSpot(targetEl);
                if (state.currentPos) {
                    applyTipPosition(targetEl, state.currentPos);
                }
            } else {
                placeFull();
            }
        });
    }

    function bindGlobal() {
        if (state.bound) return;
        window.addEventListener('scroll', onScrollOrResize, { passive: true });
        window.addEventListener('resize', onScrollOrResize);
        window.addEventListener('orientationchange', onScrollOrResize);
        document.addEventListener('keydown', onKey);
        state.bound = true;
    }
    function unbindGlobal() {
        if (!state.bound) return;
        window.removeEventListener('scroll', onScrollOrResize);
        window.removeEventListener('resize', onScrollOrResize);
        window.removeEventListener('orientationchange', onScrollOrResize);
        document.removeEventListener('keydown', onKey);
        state.bound = false;
    }
    function onKey(e) {
        if (!state.active) return;
        if (e.key === 'Escape') skipAll();
        if (e.key === 'ArrowRight') nextStep();
        if (e.key === 'ArrowLeft') prevStep();
    }

    /* ------------------------------------------------------------------ */
    /* Auto-start / API                                                    */
    /* ------------------------------------------------------------------ */
    function autoStart(name) {
        if (!tours[name]) return;
        if (isDone(name)) return;
        setTimeout(function () { start(name); }, 600);
    }

    window.CCOnboarding = {
        start: start,
        autoStart: autoStart,
        isDone: isDone,
        reset: reset,
        restart: function (name) { reset(name); start(name); },
        tours: tours,
        isMobile: isMobile,
        
        //Función para borrar el estado de TODOS los tours
        resetAll: function () {
            try {
                var keys = Object.keys(localStorage);
                keys.forEach(function (key) {
                    if (key.indexOf(STORAGE_PREFIX) === 0) {
                        localStorage.removeItem(key);
                    }
                });
                return true;
            } catch (e) {
                return false;
            }
        }
    };

    function bindTriggers() {
        document.querySelectorAll('[data-tour-start]').forEach(function (btn) {
            if (btn.dataset.tourBound === '1') return;
            btn.dataset.tourBound = '1';
            btn.addEventListener('click', function () {
                var name = btn.getAttribute('data-tour-start');
                if (name) start(name);
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindTriggers);
    } else {
        bindTriggers();
    }
})();