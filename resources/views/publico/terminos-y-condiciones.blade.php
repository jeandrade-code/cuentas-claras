{{--
==============================================================================
ARCHIVO : resources/views/publico/terminos-y-condiciones.blade.php
QUÉ ES  : Términos y Condiciones de Uso.
RUTA    : GET /terminos (terminos)
CSS     : public/css/legal.css
DATOS   : config('legal.*'). Si config/legal.php no existe, usa fallbacks.
==============================================================================
--}}

@extends('layouts.publico')

@section('titulo', 'Términos y condiciones · ' . (config('legal.app_name') ?: 'Cuentas Claras'))

<!-- Seo -->
@section('meta_description', 'Lee los términos y condiciones de uso de Cuentas Claras. Una herramienta de organización financiera, no una entidad bancaria.')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/legal.css') }}">
@endpush

@section('content')
@php
  $app           = config('legal.app_name') ?: 'Cuentas Claras';
  $empresa       = config('legal.company_name') ?: 'Cuentas Claras';
  $nit           = config('legal.company_nit') ?: '—';
  $domicilio     = config('legal.company_domicile') ?: 'Colombia';
  $emailContacto = config('legal.contact_email') ?: 'contacto@cuentasclaras.app';
  $version       = config('legal.terms_version') ?: '1.0.0';
  $fecha         = config('legal.terms_date') ?: now()->toDateString();
  $minAge        = config('legal.min_age') ?: 18;
@endphp

<main class="min-h-screen pt-28 pb-16 px-margin bg-surface">
  <article class="card max-w-3xl mx-auto p-space-xl">

    <header class="legal-header">
      <p class="legal-meta legal-meta--version">
        Versión {{ $version }} · Vigente desde {{ $fecha }}
      </p>
      <h1 class="font-headline-xl text-headline-xl text-on-surface mt-space-sm">
        Términos y Condiciones de Uso
      </h1>
      <p class="legal-meta">
        {{ $app }} · Titular: {{ $empresa }}
      </p>
    </header>

    <aside class="legal-aviso" role="note">
      <p>
        Al crear una cuenta y usar <strong>{{ $app }}</strong> aceptas estos
        Términos y Condiciones y la
        <a href="{{ route('politica') }}">Política de Privacidad</a>.
        {{ $app }} es una herramienta de organización financiera personal y
        familiar; <strong>no es una entidad financiera</strong>, no presta
        servicios bancarios y no está vigilada por la Superintendencia
        Financiera de Colombia.
      </p>
    </aside>

    <nav class="legal-toc" aria-label="Contenido de los términos">
      <p class="legal-toc__titulo">Contenido</p>
      <ol class="legal-toc__lista">
        <li><a href="#aceptacion">1. Aceptación de los términos</a></li>
        <li><a href="#servicio">2. Descripción del servicio</a></li>
        <li><a href="#registro">3. Registro y seguridad de la cuenta</a></li>
        <li><a href="#uso">4. Uso permitido</a></li>
        <li><a href="#alcance">5. Alcance informativo y responsabilidad financiera</a></li>
        <li><a href="#privacidad">6. Información personal y privacidad</a></li>
        <li><a href="#disponibilidad">7. Disponibilidad y mantenimiento</a></li>
        <li><a href="#propiedad">8. Propiedad intelectual</a></li>
        <li><a href="#responsabilidad">9. Limitaciones de responsabilidad</a></li>
        <li><a href="#cierre">10. Cierre o suspensión de cuentas</a></li>
        <li><a href="#menores">11. Menores de edad</a></li>
        <li><a href="#modificaciones">12. Modificaciones a los términos</a></li>
        <li><a href="#divisibilidad">13. Divisibilidad e integridad</a></li>
        <li><a href="#fuerza-mayor">14. Fuerza mayor</a></li>
        <li><a href="#legislacion">15. Legislación aplicable y jurisdicción</a></li>
        <li><a href="#contacto">16. Contacto</a></li>
      </ol>
    </nav>

    <div class="legal-cuerpo">

      <section id="aceptacion" class="legal-seccion">
        <h2 class="legal-titulo">1. Aceptación de los términos</h2>
        <p>
          Estos términos regulan el acceso y uso de <strong>{{ $app }}</strong>,
          una herramienta de organización financiera personal y familiar operada
          por <strong>{{ $empresa }}</strong>. Al marcar la casilla de aceptación
          durante el registro y al utilizar la plataforma, aceptas cumplir estas
          condiciones, la <a href="{{ route('politica') }}">Política de
          Privacidad</a> y la normativa aplicable.
        </p>
        <p>
          El sistema registra la fecha y hora de aceptación. Si no estás de
          acuerdo con alguna de las condiciones, debes abstenerte de crear
          una cuenta y de utilizar las funciones que requieran una sesión
          iniciada.
        </p>
      </section>

      <section id="servicio" class="legal-seccion">
        <h2 class="legal-titulo">2. Descripción del servicio</h2>
        <p>
          {{ $app }} permite registrar y organizar información financiera
          personal y familiar. Concretamente, el servicio incluye:
        </p>
        <ul class="legal-lista">
          <li>Registro de gastos diarios y administración de categorías propias y del sistema.</li>
          <li>Registro de gastos fijos mensuales con vencimiento y recordatorios automáticos tres días antes de la fecha límite.</li>
          <li>Informe mensual de gastos conforme a la regla 50/30/20 (necesidades, gustos y ahorro).</li>
          <li>Simulador de créditos por sistema francés, con semáforo de capacidad de endeudamiento (por debajo del 20 % del ingreso: saludable; entre 20 % y 30 %: moderado; por encima del 30 %: riesgo).</li>
          <li>Metas de ahorro individuales o compartidas (máximo dos participantes), con privacidad de aportes: cada participante ve el total conjunto y su propio historial, no el aporte del otro.</li>
          <li>Cambio del tipo de cuenta (estándar ↔ familiar) desde Ajustes, siempre que se cumplan las condiciones vigentes.</li>
          <li>Cambio de correo electrónico con verificación mediante enlace de un solo uso al nuevo correo y aviso al correo anterior.</li>
          <li>Autenticación de dos factores (2FA) opcional con aplicación autenticadora TOTP.</li>
          <li>Modo claro y oscuro, y tinte visual por tipo de cuenta.</li>
        </ul>
        <p>
          El servicio <strong>no</strong> solicita, almacena ni procesa claves
          bancarias, números de tarjetas, tokens de entidades financieras ni
          credenciales de terceros. Las funciones descritas pueden cambiar,
          mejorar, limitarse o dejar de estar disponibles cuando {{ $empresa }}
          lo decida, informándolo por los medios habituales de contacto y
          respetando los derechos adquiridos por los usuarios.
        </p>
      </section>

      <section id="registro" class="legal-seccion">
        <h2 class="legal-titulo">3. Registro y seguridad de la cuenta</h2>
        <p>
          Para usar {{ $app }} debes crear una cuenta con información veraz y
          mantenerla actualizada. Eres responsable de proteger tus credenciales
          de acceso y de informar de inmediato cualquier uso no autorizado a
          <strong>{{ $emailContacto }}</strong>.
        </p>
        <ul class="legal-lista">
          <li>Las contraseñas se almacenan exclusivamente como hash criptográfico; el sistema no puede recuperarlas en texto plano.</li>
          <li>La sesión expira automáticamente después de 15 minutos de inactividad.</li>
          <li>El enlace de restablecimiento de contraseña es de un solo uso y vence a los 30 minutos.</li>
          <li>El cambio de correo requiere la contraseña actual y la confirmación mediante enlace de un solo uso enviado al nuevo correo, con vencimiento de 1 hora.</li>
          <li>El cambio de tipo de cuenta y la eliminación de la cuenta solo están disponibles para los roles estándar y familiar; el rol administrador no tiene acceso a esas funciones.</li>
          <li>Algunas acciones administrativas requieren re-autenticación con la contraseña actual del administrador.</li>
          <li>El inicio de sesión desde una ubicación IP nueva y los intentos fallidos bloqueados generan una notificación por correo al titular.</li>
          <li>La autenticación de dos factores (2FA), cuando está activa, requiere un código TOTP adicional después de la contraseña.</li>
        </ul>
      </section>

      <section id="uso" class="legal-seccion">
        <h2 class="legal-titulo">4. Uso permitido</h2>
        <p>Al utilizar {{ $app }} te comprometes a:</p>
        <ul class="legal-lista">
          <li>Utilizar la plataforma de forma lícita y de buena fe.</li>
          <li>Ingresar únicamente información que estés autorizado a proporcionar.</li>
          <li>No intentar acceder sin autorización a cuentas, datos o sistemas de otras personas.</li>
          <li>No interferir con el funcionamiento, la disponibilidad o la seguridad del servicio.</li>
          <li>No utilizar la plataforma para actividades fraudulentas, ilícitas o contrarias a estos términos.</li>
          <li>No copiar, alterar ni explotar indebidamente los componentes del servicio, salvo cuando la ley lo permita.</li>
          <li>No automatizar accesos ni realizar extracciones masivas de datos sin autorización expresa.</li>
        </ul>
      </section>

      <section id="alcance" class="legal-seccion">
        <h2 class="legal-titulo">5. Alcance informativo y responsabilidad financiera</h2>
        <p>
          {{ $app }} es una herramienta de <strong>organización y seguimiento
          financiero</strong>. Salvo que se indique expresamente lo contrario,
          la información y los cálculos que ofrece no constituyen asesoría
          financiera, tributaria, contable ni jurídica personalizada.
        </p>
        <p>
          El simulador de créditos utiliza el sistema francés y ofrece
          resultados orientativos. <strong>No otorga créditos reales, no
          consulta centrales de riesgo y no reporta información a terceros</strong>.
          El semáforo de capacidad de endeudamiento es informativo y no
          reemplaza la evaluación de una entidad financiera.
        </p>
        <p>
          Las decisiones financieras que tomes son de tu exclusiva
          responsabilidad. Debes revisar los datos que ingresas y comprobar
          los resultados antes de utilizarlos para tomar decisiones.
        </p>
      </section>

      <section id="privacidad" class="legal-seccion">
        <h2 class="legal-titulo">6. Información personal y privacidad</h2>
        <p>
          El tratamiento de los datos personales se rige por la
          <a href="{{ route('politica') }}">Política de Privacidad</a>, que
          describe las finalidades del tratamiento, los derechos del titular y
          los canales para ejercerlos conforme a la Ley 1581 de 2012 y sus
          decretos reglamentarios.
        </p>
        <p>
          El registro exige la aceptación expresa de estos términos y de la
          Política de Privacidad. El sistema registra la fecha y hora de la
          aceptación y no permite crear la cuenta sin ese consentimiento.
        </p>
      </section>

      <section id="disponibilidad" class="legal-seccion">
        <h2 class="legal-titulo">7. Disponibilidad y mantenimiento</h2>
        <p>
          {{ $empresa }} procurará mantener la plataforma disponible y en
          funcionamiento, pero no garantiza que el acceso sea ininterrumpido
          ni que esté libre de errores. Podrán realizarse tareas de
          mantenimiento, actualizaciones o cambios necesarios para el
          funcionamiento del servicio. Cuando sea posible, los mantenimientos
          programados se anunciarán con antelación.
        </p>
      </section>

      <section id="propiedad" class="legal-seccion">
        <h2 class="legal-titulo">8. Propiedad intelectual</h2>
        <p>
          Los elementos de la plataforma, incluidos su diseño, marca, textos y
          componentes propios, están sujetos a los derechos de propiedad
          intelectual de {{ $empresa }} o de sus licenciantes. No se concede
          autorización para utilizarlos fuera de lo permitido por la ley o por
          el titular de los derechos.
        </p>
        <p>
          El usuario conserva la titularidad de la información que ingresa. Al
          usar el servicio, otorga a {{ $empresa }} una licencia limitada,
          no exclusiva y revocable para almacenar, procesar y mostrar esa
          información con la única finalidad de prestar el servicio.
        </p>
      </section>

      <section id="responsabilidad" class="legal-seccion">
        <h2 class="legal-titulo">9. Limitaciones de responsabilidad</h2>
        <p>
          En la medida permitida por la legislación aplicable, cada usuario es
          responsable de la información que introduce y de las decisiones que
          toma a partir de ella. {{ $empresa }} no será responsable por daños
          indirectos, incidentales o consecuentes derivados del uso o de la
          imposibilidad de uso del servicio, salvo en los casos de dolo o culpa
          grave.
        </p>
        <p>
          Nada en estos términos excluye ni limita derechos o responsabilidades
          que legalmente no puedan excluirse o limitarse, en particular los
          derechos del consumidor y los derechos del titular de datos
          personales.
        </p>
      </section>

      <section id="cierre" class="legal-seccion">
        <h2 class="legal-titulo">10. Cierre o suspensión de cuentas</h2>
        <p>
          {{ $empresa }} podrá suspender o limitar el acceso cuando existan
          motivos legítimos, como riesgos de seguridad, uso contrario a estos
          términos o requerimientos de autoridad competente, respetando la
          legislación aplicable y el derecho de defensa del usuario.
        </p>
        <p>
          El usuario puede eliminar su cuenta desde
          <strong>Ajustes &gt; Eliminar cuenta</strong>. La eliminación borra en
          cascada los datos asociados, cancela las metas compartidas activas
          (con notificación al otro participante) y transfiere la titularidad de
          la meta al otro participante cuando el usuario que se va era el
          creador. El sistema no permite eliminar la cuenta del último
          administrador activo. Los detalles completos están en la
          <a href="{{ route('politica') }}">Política de Privacidad</a>.
        </p>
      </section>

      <section id="menores" class="legal-seccion">
        <h2 class="legal-titulo">11. Menores de edad</h2>
        <p>
          El servicio está dirigido a personas mayores de
          <strong>{{ $minAge }} años</strong>. Al registrarte declaras cumplir
          con este requisito. Si {{ $empresa }} detecta una cuenta creada por
          un menor sin la autorización de su representante legal, procederá a
          su eliminación.
        </p>
      </section>

      <section id="modificaciones" class="legal-seccion">
        <h2 class="legal-titulo">12. Modificaciones a los términos</h2>
        <p>
          {{ $empresa }} puede modificar estos términos cuando cambien el
          servicio, las prácticas comerciales o los requisitos legales. La
          versión vigente estará siempre disponible en esta página con su
          número de versión y su fecha de vigencia.
        </p>
        <p>
          Cuando los cambios sean sustanciales, se notificará a los usuarios
          por correo electrónico con antelación razonable y, si la ley lo
          exige, se solicitará una nueva aceptación. El uso continuado del
          servicio después de la entrada en vigencia de los cambios implica su
          aceptación.
        </p>
      </section>

      <section id="divisibilidad" class="legal-seccion">
        <h2 class="legal-titulo">13. Divisibilidad e integridad</h2>
        <p>
          Si alguna disposición de estos términos fuera declarada nula, ilegal
          o inaplicable por autoridad competente, las demás disposiciones
          seguirán vigentes. La disposición afectada se interpretará en la
          medida necesaria para preservar la intención original de las partes.
        </p>
        <p>
          Estos términos, junto con la
          <a href="{{ route('politica') }}">Política de Privacidad</a>,
          constituyen el acuerdo completo entre el usuario y {{ $empresa }}
          respecto del uso del servicio, y sustituyen cualquier acuerdo
          anterior sobre la misma materia.
        </p>
      </section>

      <section id="fuerza-mayor" class="legal-seccion">
        <h2 class="legal-titulo">14. Fuerza mayor</h2>
        <p>
          {{ $empresa }} no será responsable por el incumplimiento o la demora
          en la prestación del servicio cuando ello se deba a causas de fuerza
          mayor o caso fortuito, incluyendo, sin limitación, desastres
          naturales, actos de autoridad, fallas generalizadas de
          telecomunicaciones o de energía, ciberataques de gran escala,
          pandemias o conflictos armados. En tales casos, {{ $empresa }}
          notificará a los usuarios y adoptará medidas razonables para
          restablecer el servicio.
        </p>
      </section>

      <section id="legislacion" class="legal-seccion">
        <h2 class="legal-titulo">15. Legislación aplicable y jurisdicción</h2>
        <p>
          Estos términos se rigen por las leyes de la República de Colombia,
          sin perjuicio de las normas imperativas que resulten aplicables,
          incluyendo las de protección al consumidor y las de protección de
          datos personales (Ley 1581 de 2012 y sus decretos reglamentarios).
        </p>
        <p>
          Cualquier controversia que no pueda resolverse amigablemente será
          sometida a los jueces competentes de la República de Colombia, en
          los términos que establezca la ley procesal aplicable. Lo anterior no
          limita el derecho del consumidor a acudir a la jurisdicción de su
          domicilio cuando la ley lo permita.
        </p>
      </section>

      <section id="contacto" class="legal-seccion">
        <h2 class="legal-titulo">16. Contacto</h2>
        <p>
          Para consultas, solicitudes o reclamaciones relacionadas con estos
          términos:
        </p>
        <ul class="legal-lista">
          <li><strong>Correo:</strong> {{ $emailContacto }}</li>
          <li><strong>Titular:</strong> {{ $empresa }}</li>
          <li><strong>NIT:</strong> {{ $nit }}</li>
          <li><strong>Domicilio:</strong> {{ $domicilio }}</li>
        </ul>
      </section>

    </div>

    <footer class="legal-footer">
      <p class="legal-footer__version">
        {{ $app }} · Términos y Condiciones v{{ $version }} · Vigente desde {{ $fecha }}
      </p>
      <p class="legal-footer__empresa">
        {{ $empresa }} · NIT {{ $nit }} · {{ $domicilio }}
      </p>
    </footer>

  </article>
</main>
@endsection