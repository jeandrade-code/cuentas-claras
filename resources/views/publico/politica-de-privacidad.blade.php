{{--
==============================================================================
ARCHIVO : resources/views/publico/politica-de-privacidad.blade.php
QUÉ ES  : Política de Privacidad y Tratamiento de Datos Personales.
RUTA    : GET /politica-de-privacidad (politica)
CSS     : public/css/legal.css
DATOS   : config('legal.*'). Si config/legal.php no existe, usa fallbacks.
==============================================================================
--}}

@extends('layouts.publico')

@section('titulo', 'Política de privacidad · ' . (config('legal.app_name') ?: 'Cuentas Claras'))

<!-- Seo -->
@section('meta_description', 'Conoce cómo Cuentas Claras protege tus datos personales y financieros, cumpliendo con la Ley 1581 de 2012 en Colombia.')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/legal.css') }}">
@endpush

@section('content')
@php
  $app           = config('legal.app_name') ?: 'Cuentas Claras';
  $empresa       = config('legal.company_name') ?: 'Cuentas Claras';
  $nit           = config('legal.company_nit') ?: '—';
  $domicilio     = config('legal.company_domicile') ?: 'Colombia';
  $direccion     = config('legal.company_address') ?: '';
  $emailContacto = config('legal.contact_email') ?: 'contacto@cuentasclaras.app';
  $dpoEmail      = config('legal.dpo_email') ?: '';
  $version       = config('legal.policy_version') ?: '1.0.0';
  $fecha         = config('legal.policy_date') ?: now()->toDateString();
  $retActive     = config('legal.retention_days_active') ?: 365;
  $retClosed     = config('legal.retention_days_closed') ?: 1095;
  $retAudit      = config('legal.retention_days_audit_log') ?: 1825;
  $incidentes    = config('legal.incident_notification_hours') ?: 72;
  $minAge        = config('legal.min_age') ?: 18;
  $mostrarDpo    = !empty($dpoEmail) && $dpoEmail !== $emailContacto;
@endphp

<main class="min-h-screen pt-28 pb-16 px-margin bg-surface">
  <article class="card max-w-3xl mx-auto p-space-xl">

    <header class="legal-header">
      <p class="legal-meta legal-meta--version">
        Versión {{ $version }} · Vigente desde {{ $fecha }}
      </p>
      <h1 class="font-headline-xl text-headline-xl text-on-surface mt-space-sm">
        Política de Privacidad y Tratamiento de Datos Personales
      </h1>
      <p class="legal-meta">
        {{ $app }} · Responsable: {{ $empresa }}
      </p>
    </header>

    <aside class="legal-aviso" role="note">
      <p>
        Esta política describe cómo <strong>{{ $empresa }}</strong> recolecta, usa,
        almacena y protege los datos personales de los usuarios de
        <strong>{{ $app }}</strong>, conforme a la Ley 1581 de 2012 y sus
        decretos reglamentarios. Al registrarte aceptas esta política y los
        <a href="{{ route('terminos') }}">Términos y condiciones</a>.
        Si no estás de acuerdo, no debes crear una cuenta.
      </p>
    </aside>

    <nav class="legal-toc" aria-label="Contenido de la política">
      <p class="legal-toc__titulo">Contenido</p>
      <ol class="legal-toc__lista">
        <li><a href="#responsable">1. Responsable del tratamiento</a></li>
        <li><a href="#datos">2. Datos personales que se tratan</a></li>
        <li><a href="#finalidades">3. Finalidades del tratamiento</a></li>
        <li><a href="#base-legal">4. Base legal y autorización</a></li>
        <li><a href="#seguridad">5. Cómo se protege la información</a></li>
        <li><a href="#cookies">6. Cookies y tecnologías similares</a></li>
        <li><a href="#comparticion">7. Con quién se comparte la información</a></li>
        <li><a href="#transferencias">8. Transferencias internacionales</a></li>
        <li><a href="#derechos">9. Derechos del titular</a></li>
        <li><a href="#ejercer">10. Cómo ejercer los derechos</a></li>
        <li><a href="#menores">11. Menores de edad</a></li>
        <li><a href="#eliminacion">12. Eliminación de la cuenta y de los datos</a></li>
        <li><a href="#conservacion">13. Conservación de los datos</a></li>
        <li><a href="#incidentes">14. Notificación de incidentes de seguridad</a></li>
        <li><a href="#cambios">15. Cambios a esta política</a></li>
        <li><a href="#contacto">16. Contacto</a></li>
      </ol>
    </nav>

    <div class="legal-cuerpo">

      <section id="responsable" class="legal-seccion">
        <h2 class="legal-titulo">1. Responsable del tratamiento</h2>
        <p>
          El responsable del tratamiento de los datos personales es
          <strong>{{ $empresa }}</strong>, identificada con NIT
          <strong>{{ $nit }}</strong>, con domicilio principal en
          <strong>{{ $domicilio }}</strong>.
        </p>
        @if ($direccion)
          <p>Dirección física: <strong>{{ $direccion }}</strong>.</p>
        @endif
        <p>
          Canal oficial para consultas, solicitudes o reclamaciones
          relacionadas con datos personales:
          <strong>{{ $emailContacto }}</strong>.
        </p>
        @if ($mostrarDpo)
          <p>
            Para asuntos que requieran la atención del Delegado de Protección
            de Datos: <strong>{{ $dpoEmail }}</strong>.
          </p>
        @endif
      </section>

      <section id="datos" class="legal-seccion">
        <h2 class="legal-titulo">2. Datos personales que se tratan</h2>
        <p>
          Tratamos únicamente los datos que el usuario ingresa voluntariamente
          y los que el sistema necesita para prestar el servicio:
        </p>
        <ul class="legal-lista">
          <li><strong>Datos de identificación:</strong> nombre completo y número de cédula, solicitados al registrarse.</li>
          <li><strong>Datos de contacto:</strong> correo electrónico principal y, cuando el usuario solicita cambiar su correo, el correo pendiente de verificación.</li>
          <li><strong>Credenciales de acceso:</strong> la contraseña se almacena exclusivamente como hash criptográfico (bcrypt con costo 12). El sistema no conserva ni puede recuperar la contraseña en texto plano.</li>
          <li><strong>Información financiera ingresada por el usuario:</strong> ingreso mensual declarado, gastos, gastos fijos, pagos registrados, simulaciones de crédito, informes y metas de ahorro, individuales o compartidas.</li>
          <li><strong>Registros de operación:</strong> fecha y hora de creación de los registros, fecha de aceptación de esta política, tokens temporales de verificación o restablecimiento de contraseña (guardados como hash SHA-256), dirección IP y agente de usuario del último inicio de sesión.</li>
          <li><strong>Registros de auditoría:</strong> cuando el usuario tiene rol administrador, cada acción administrativa que modifique el estado del sistema queda registrada (quién, qué acción, valores antes y después, IP, agente). Estos registros son de solo lectura y no se pueden editar.</li>
        </ul>
        <p>
          <strong>No</strong> solicitamos ni almacenamos números de tarjetas de
          crédito, cuentas bancarias, claves de acceso bancario, tokens de
          entidades financieras ni información de terceros que no sea
          estrictamente necesaria para el funcionamiento del sistema.
        </p>
      </section>

      <section id="finalidades" class="legal-seccion">
        <h2 class="legal-titulo">3. Finalidades del tratamiento</h2>
        <p>Los datos se utilizan exclusivamente para:</p>
        <ul class="legal-lista">
          <li>Crear, autenticar y administrar la cuenta del usuario.</li>
          <li>Registrar y organizar ingresos, gastos, categorías y gastos fijos.</li>
          <li>Calcular el informe mensual conforme a la regla 50/30/20.</li>
          <li>Simular créditos con base en la información que el usuario ingresa.</li>
          <li>Gestionar metas de ahorro individuales o compartidas y calcular su avance conjunto.</li>
          <li>Generar recordatorios de vencimientos de gastos fijos y notificaciones relacionadas con la cuenta.</li>
          <li>Enviar correos transaccionales de verificación de cambio de correo, restablecimiento de contraseña y alertas de seguridad (cambios de contraseña, cambios de correo, inicios de sesión desde nuevas ubicaciones, intentos de inicio de sesión bloqueados).</li>
          <li>Atender consultas, solicitudes o reclamaciones del titular.</li>
          <li>Cumplir las obligaciones legales aplicables y garantizar la seguridad del servicio.</li>
        </ul>
        <p>
          Los datos <strong>no</strong> se utilizan para perfilamiento
          comercial, publicidad, venta a terceros ni para finalidades distintas
          a las aquí informadas.
        </p>
      </section>

      <section id="base-legal" class="legal-seccion">
        <h2 class="legal-titulo">4. Base legal y autorización</h2>
        <p>
          El tratamiento se fundamenta en la autorización previa, expresa e
          informada del titular (Ley 1581 de 2012, art. 9). Al registrarse, el
          usuario marca una casilla de aceptación y el sistema registra la
          fecha y hora de esa autorización. Sin esa aceptación, la plataforma
          no permite crear la cuenta.
        </p>
        <p>
          El usuario puede revocar la autorización o solicitar la supresión de
          sus datos en cualquier momento, conforme a los derechos reconocidos
          por la ley y al procedimiento descrito en la sección 10.
        </p>
      </section>

      <section id="seguridad" class="legal-seccion">
        <h2 class="legal-titulo">5. Cómo se protege la información</h2>
        <p>
          Adoptamos medidas técnicas, humanas y administrativas razonables, entre
          ellas:
        </p>
        <ul class="legal-lista">
          <li><strong>Contraseñas:</strong> se almacenan con <code>bcrypt</code> (costo 12), un algoritmo de hash con sal, diseñado para dificultar su reversión. Ni el equipo de desarrollo ni el administrador pueden leer la contraseña original.</li>
          <li><strong>Tokens de verificación:</strong> los enlaces de restablecimiento de contraseña y de verificación de cambio de correo usan tokens de un solo uso, guardados en la base como hash SHA-256, con vencimiento de 30 minutos (restablecer contraseña) y 1 hora (cambio de correo).</li>
          <li><strong>Aislamiento por usuario:</strong> cada consulta filtra los registros por el usuario autenticado. Un usuario no puede ver ni modificar los datos de otro, aunque escriba manualmente la URL.</li>
          <li><strong>Roles:</strong> existen tres roles (estándar, familiar y administrador) con permisos diferenciados. El administrador <strong>no puede consultar montos, gastos ni datos financieros individuales</strong>; solo accede a conteos agregados y a la gestión de usuarios, roles y categorías globales.</li>
          <li><strong>Sesión:</strong> expira automáticamente después de 15 minutos de inactividad. Al restablecer contraseña o confirmar un cambio de correo, la sesión se cierra y debe iniciarse de nuevo.</li>
          <li><strong>Cambio de correo:</strong> requiere la contraseña actual y una verificación posterior en el nuevo correo, antes de que el cambio sea efectivo. Se envía un aviso al correo anterior.</li>
          <li><strong>Límite de intentos:</strong> el inicio de sesión tiene un límite de intentos fallidos; cuando se alcanza, el acceso se bloquea temporalmente y se envía una alerta al correo del titular.</li>
          <li><strong>Autenticación de dos factores (2FA):</strong> opcional para todos los usuarios. Cuando está activa, además de la contraseña se solicita un código temporal generado por una aplicación autenticadora (TOTP).</li>
          <li><strong>Re-autenticación para acciones destructivas:</strong> las acciones administrativas que eliminan usuarios o desactivan categorías del sistema exigen la contraseña actual del administrador.</li>
          <li><strong>Auditoría:</strong> cada acción del administrador que modifica el estado del sistema queda registrada con fecha, IP y valores anteriores y posteriores. Los registros son inmutables y se conservan {{ $retAudit }} días.</li>
          <li><strong>Cabeceras de seguridad:</strong> el servicio envía cabeceras <code>Content-Security-Policy</code>, <code>X-Frame-Options</code>, <code>X-Content-Type-Options</code>, <code>Referrer-Policy</code> y, sobre HTTPS, <code>Strict-Transport-Security</code>.</li>
        </ul>
        <p>
          Ningún sistema es absolutamente invulnerable. No garantizamos
          seguridad total frente a accesos no autorizados, pero nos
          comprometemos a actuar de manera diligente y a notificar los
          incidentes relevantes conforme a la sección 14.
        </p>
      </section>

      <section id="cookies" class="legal-seccion">
        <h2 class="legal-titulo">6. Cookies y tecnologías similares</h2>
        <p>
          El servicio utiliza cookies estrictamente necesarias para el
          funcionamiento:
        </p>
        <ul class="legal-lista">
          <li><strong>Cookie de sesión:</strong> identifica al usuario autenticado. Configurada como <code>HttpOnly</code>, <code>Secure</code> (sobre HTTPS) y <code>SameSite=Lax</code>.</li>
          <li><strong>Cookie CSRF:</strong> protege los formularios contra falsificación de peticiones en sitios cruzados.</li>
          <li><strong>Preferencia de tema (localStorage):</strong> guarda la elección de modo claro u oscuro. No identifica al usuario y no se envía al servidor.</li>
        </ul>
        <p>
          No utilizamos cookies de publicidad, de perfilamiento comercial ni de
          seguimiento entre sitios. No compartimos identificadores con redes
          publicitarias.
        </p>
      </section>

      <section id="comparticion" class="legal-seccion">
        <h2 class="legal-titulo">7. Con quién se comparte la información</h2>
        <p>
          <strong>No vendemos ni cedemos</strong> datos personales a terceros con
          fines comerciales. La información puede ser conocida por:
        </p>
        <ul class="legal-lista">
          <li><strong>El propio usuario</strong>, a través de su cuenta.</li>
          <li><strong>El otro participante de una meta compartida</strong>, exclusivamente respecto de los datos de la meta (nombre, monto objetivo, avance conjunto y nombres de los participantes). <strong>Los aportes individuales no se muestran al otro</strong>; solo se calcula y muestra el total conjunto.</li>
          <li><strong>El administrador de la plataforma</strong>, que puede ver datos de identificación (nombre, correo, rol, estado de la cuenta) para gestionar usuarios y roles, pero <strong>no puede consultar información financiera individual</strong>.</li>
          <li><strong>Proveedores tecnológicos</strong> estrictamente necesarios para operar el servicio: hospedaje, envío de correos transaccionales y monitoreo de errores. Estos proveedores actúan como encargados del tratamiento bajo contrato y solo pueden usar los datos para prestar el servicio contratado.</li>
          <li><strong>Autoridades públicas</strong>, cuando exista una obligación legal de hacerlo.</li>
        </ul>
      </section>

      <section id="transferencias" class="legal-seccion">
        <h2 class="legal-titulo">8. Transferencias internacionales</h2>
        <p>
          Algunos proveedores tecnológicos (por ejemplo, servicios de correo
          transaccional o de hospedaje en la nube) pueden procesar los datos
          fuera de Colombia. En esos casos, exigimos contractualmente que el
          proveedor aplique niveles de protección equivalentes a los de la
          Ley 1581 de 2012 y sus decretos reglamentarios. Cuando la ley lo
          requiera, solicitaremos autorización expresa para la transferencia.
        </p>
      </section>

      <section id="derechos" class="legal-seccion">
        <h2 class="legal-titulo">9. Derechos del titular</h2>
        <p>
          Conforme a la Ley 1581 de 2012 y sus decretos reglamentarios, el
          titular de los datos tiene derecho a:
        </p>
        <ul class="legal-lista">
          <li>Conocer los datos personales que están siendo tratados.</li>
          <li>Actualizar o rectificar la información cuando sea incompleta, inexacta o desactualizada.</li>
          <li>Solicitar prueba de la autorización otorgada, cuando corresponda.</li>
          <li>Consultar el uso que se ha dado a sus datos.</li>
          <li>Presentar consultas o reclamaciones sobre el tratamiento.</li>
          <li>Revocar la autorización y solicitar la supresión de sus datos, cuando proceda legalmente.</li>
          <li>Acceder de forma gratuita a sus datos personales que hayan sido objeto de tratamiento.</li>
          <li>Presentar quejas ante la Superintendencia de Industria y Comercio, una vez agotado el trámite de consulta o reclamación ante el responsable.</li>
        </ul>
      </section>

      <section id="ejercer" class="legal-seccion">
        <h2 class="legal-titulo">10. Cómo ejercer los derechos</h2>
        <p>
          Para ejercer cualquiera de estos derechos, el titular debe enviar una
          solicitud a <strong>{{ $emailContacto }}</strong>, indicando:
        </p>
        <ul class="legal-lista">
          <li>Nombre completo.</li>
          <li>Medio de contacto para la respuesta.</li>
          <li>Derecho que desea ejercer.</li>
          <li>Descripción clara de la solicitud.</li>
          <li>Documento que acredite su identidad o representación, cuando aplique.</li>
        </ul>
        <p>
          Las solicitudes se atienden conforme a los plazos de la ley colombiana:
          <strong>15 días hábiles para consultas</strong> y
          <strong>15 días hábiles para reclamaciones</strong>, prorrogables
          por 8 días hábiles cuando sea necesario, informando al titular los
          motivos.
        </p>
      </section>

      <section id="menores" class="legal-seccion">
        <h2 class="legal-titulo">11. Menores de edad</h2>
        <p>
          El servicio está dirigido a personas mayores de
          <strong>{{ $minAge }} años</strong>. No recolectamos conscientemente
          datos de menores de edad. Si detectamos una cuenta creada por un menor
          sin la autorización de su representante legal, procederemos a su
          eliminación. Los representantes legales pueden contactarnos a
          <strong>{{ $emailContacto }}</strong> para solicitar la revisión o
          eliminación de la cuenta.
        </p>
      </section>

      <section id="eliminacion" class="legal-seccion">
        <h2 class="legal-titulo">12. Eliminación de la cuenta y de los datos</h2>
        <p>
          El usuario puede eliminar su cuenta desde
          <strong>Ajustes &gt; Eliminar cuenta</strong>. La eliminación:
        </p>
        <ul class="legal-lista">
          <li>Cierra la sesión y borra la cuenta de forma definitiva.</li>
          <li>Borra en cascada los datos asociados: gastos, gastos fijos, pagos, simulaciones de crédito, informes, aportes y notificaciones.</li>
          <li>Respecto de las <strong>metas compartidas activas</strong>, la meta se cancela y se notifica al otro participante. Si el usuario que se va era el creador, la meta sobrevive y se transfiere al otro participante para que conserve su historial de aportes.</li>
          <li>No permite eliminar la cuenta del último administrador activo del sistema, por razones de continuidad operativa.</li>
        </ul>
        <p>
          Algunos registros pueden conservarse por los periodos indicados en la
          sección 13 cuando exista una obligación legal o un interés legítimo
          del responsable.
        </p>
      </section>

      <section id="conservacion" class="legal-seccion">
        <h2 class="legal-titulo">13. Conservación de los datos</h2>
        <p>
          Los datos se conservan según los siguientes criterios:
        </p>
        <ul class="legal-lista">
          <li><strong>Mientras la cuenta está activa:</strong> los datos financieros y de identificación se conservan para prestar el servicio.</li>
          <li><strong>Cuenta inactiva:</strong> hasta {{ $retActive }} días después del último inicio de sesión, tras lo cual la cuenta puede suspenderse por inactividad.</li>
          <li><strong>Cuenta eliminada por el usuario:</strong> los datos financieros se borran de inmediato. Algunos metadatos mínimos (fecha de eliminación, motivo) pueden conservarse hasta {{ $retClosed }} días por razones de seguridad y prevención de fraude.</li>
          <li><strong>Registros de auditoría:</strong> hasta {{ $retAudit }} días después de la acción administrativa.</li>
          <li><strong>Registros de acceso (IP, agente):</strong> solo se conserva la última IP y agente del login exitoso, para detectar accesos desde ubicaciones nuevas.</li>
        </ul>
        <p>
          Cuando ya no exista una finalidad legítima ni obligación legal, los
          datos se eliminan de forma segura.
        </p>
      </section>

      <section id="incidentes" class="legal-seccion">
        <h2 class="legal-titulo">14. Notificación de incidentes de seguridad</h2>
        <p>
          Si detectamos un incidente de seguridad que afecte datos personales
          (acceso no autorizado, pérdida, alteración o divulgación), actuaremos
          de inmediato para contenerlo y evaluar su alcance. Cuando el incidente
          represente un riesgo significativo para los derechos del titular, se
          lo notificaremos por correo electrónico dentro de las
          <strong>{{ $incidentes }} horas</strong> siguientes a su detección, y
          reportaremos el hecho a la Superintendencia de Industria y Comercio
          cuando la ley lo exija.
        </p>
      </section>

      <section id="cambios" class="legal-seccion">
        <h2 class="legal-titulo">15. Cambios a esta política</h2>
        <p>
          Esta política puede actualizarse cuando cambien el servicio, las
          prácticas de tratamiento o los requisitos legales. La versión
          vigente estará siempre disponible en esta página con su número de
          versión y su fecha de vigencia. Cuando los cambios sean
          sustanciales, se notificará a los usuarios por correo electrónico con
          antelación razonable y, si la ley lo exige, se solicitará una nueva
          aceptación.
        </p>
      </section>

      <section id="contacto" class="legal-seccion">
        <h2 class="legal-titulo">16. Contacto</h2>
        <p>
          Para cualquier consulta, solicitud o reclamación relacionada con esta
          política o con el tratamiento de tus datos personales:
        </p>
        <ul class="legal-lista">
          <li><strong>Correo:</strong> {{ $emailContacto }}</li>
          @if ($mostrarDpo)
            <li><strong>Delegado de Protección de Datos:</strong> {{ $dpoEmail }}</li>
          @endif
          <li><strong>Responsable:</strong> {{ $empresa }}</li>
          <li><strong>Domicilio:</strong> {{ $domicilio }}</li>
        </ul>
      </section>

    </div>

    <footer class="legal-footer">
      <p class="legal-footer__version">
        {{ $app }} · Política de Privacidad v{{ $version }} · Vigente desde {{ $fecha }}
      </p>
      <p class="legal-footer__empresa">
        {{ $empresa }} · NIT {{ $nit }} · {{ $domicilio }}
      </p>
    </footer>

  </article>
</main>
@endsection