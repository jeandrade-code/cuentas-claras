<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;


/*
 * ==============================================================================
 * routes/web.php  —  ÍNDICE DE RUTAS (solo Juan lo modifica)
 * Cada módulo vive en routes/modulos/<modulo>.php y lo escribe su dueño.
 * Los NOMBRES de ruta salen de docs/backend/00-rutas.php y NO se cambian.
 *
 * J3 HECHO (2026-10-06): login real. Todo el bloque privado exige 'auth' (sin sesión -> /iniciar-sesion).
 * J4 HECHO (2026-10-06): 'cuenta.activa' expulsa cuentas suspendidas; 'rol:...' limita cada grupo:
 *     B  todos los roles con sesión ......... ajustes
 *     C  estandar y familiar ................ panel, gastos, categorías, gastos fijos, notificaciones,
 *                                             créditos, informes y METAS (individuales para estandar,
 *                                             compartidas para familiar)
 *     E  solo administrador (prefijo /admin)  usuarios, categorías del sistema, estadísticas
 * J5 ACTUALIZADO (2026-10-08): metas pasaron al Grupo C. El estándar puede tener metas individuales
 *     y recibir invitaciones (con aviso de cambiar a familiar para aceptar). El familiar sigue
 *     pudiendo crear metas compartidas.
 *
 * 2026-10-17: se agrega 'sesion.inactividad' al bloque privado. Expira la sesión
 * tras 15 minutos sin actividad y muestra un mensaje en el login. Requiere
 * SESSION_LIFETIME en .env mayor que 15 (por ejemplo, 480).
 *
 * 2026-10-17: Route::fallback() al final del bloque privado para que la vista
 * errors/404 se renderice con sesión activa (y @auth funcione).
 * ==============================================================================
 */

// Ruta dinámica para robots.txt
Route::get('/robots.txt', function () {
    // route('sitemap') generará automáticamente la URL absoluta correcta
    // basándose en tu archivo .env (APP_URL).
    $sitemapUrl = route('sitemap');
    
    $content = "User-agent: *\n";
    $content .= "Disallow: /panel\n";
    $content .= "Disallow: /gastos\n";
    $content .= "Disallow: /gastos-fijos\n";
    $content .= "Disallow: /creditos\n";
    $content .= "Disallow: /metas\n";
    $content .= "Disallow: /informes\n";
    $content .= "Disallow: /ajustes\n";
    $content .= "Disallow: /admin/\n";
    $content .= "Disallow: /notificaciones\n";
    $content .= "Disallow: /categorias\n";
    $content .= "Disallow: /iniciar-sesion\n";
    $content .= "Disallow: /crear-usuario\n";
    $content .= "Disallow: /recuperar-contrasena\n";
    $content .= "Disallow: /restablecer-contrasena/\n\n";
    
    // Aquí inyectamos la URL absoluta generada dinámicamente
    $content .= "Sitemap: " . $sitemapUrl;

    return response($content, 200)
        ->header('Content-Type', 'text/plain');
})->name('robots');

// GRUPO A - PÚBLICAS
require __DIR__.'/modulos/publico.php';   // Nikol (N5)
require __DIR__.'/modulos/auth.php';      // Juan  (J3)

// ==========================================
// RUTA DEL SITEMAP (PÚBLICA)
// ==========================================
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');


// GRUPOS B a E - PRIVADAS
Route::middleware(['sesion.inactividad', 'auth', 'cuenta.activa'])->group(function () {

    // GRUPO B - todos los roles con sesión
    require __DIR__.'/modulos/ajustes.php';          // Valeria (V4)

    // GRUPO C - estándar y familiar
    Route::middleware('rol:estandar,familiar')->group(function () {
        require __DIR__.'/modulos/panel.php';            // Juan    (J8)
        require __DIR__.'/modulos/gastos.php';           // Nikol   (N1, N2)
        require __DIR__.'/modulos/gastos-fijos.php';     // Nikol   (N3)
        require __DIR__.'/modulos/notificaciones.php';   // Nikol   (N4)
        require __DIR__.'/modulos/creditos.php';         // Valeria (V1, V2)
        require __DIR__.'/modulos/informes.php';         // Valeria (V3)
        require __DIR__.'/modulos/metas.php';            // Juan    (J5) - individuales para todos
    });

    // GRUPO E - solo administrador
    Route::middleware('rol:administrador')->prefix('admin')->name('admin.')->group(function () {
        require __DIR__.'/modulos/admin.php';            // Juan (J7)
    });

    /*
    * Fallback: captura cualquier URL que no coincida con una ruta.
    * Al ser una ruta, corre el middleware 'web' (sesión incluida), así la vista
    * errors.404 puede usar @auth y mostrar los botones del rol correcto.
    */
    Route::fallback(function () {
        return response()->view('errors.404', [], 404);
    });
});