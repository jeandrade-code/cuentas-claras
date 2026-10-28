<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePerfilRequest;
use App\Notifications\AlertaSeguridad;
use App\Services\ServicioCuentas;
use App\Services\ServicioMetas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
/**
 * 2026-10-22 (J9): notifica email.changed al correo ANTERIOR tras confirmar
 *                  el cambio de correo (para detectar ataques).
 */
class AjustesController extends Controller
{
    public function __construct(
        private ServicioCuentas $cuentas,
        private ServicioMetas $metas,
    ) {
    }

    // =========================================================================
    // PERFIL
    // =========================================================================

    public function perfil(Request $request): View
    {
        return view('app.ajustes.perfil', [
            'usuario' => $request->user(),
        ]);
    }

    public function actualizarPerfil(UpdatePerfilRequest $request)
    {
        $usuario = $request->user();
        $esAdmin = $usuario->rol === 'administrador';

        $datos = $request->validated();

        // ── FOTO PERFIL: extraer del array validado ─────────────────────────
        // Importante: hay que sacarlos de $datos porque UploadedFile no es
        // asignable en masa. Los manejamos aparte al final.
        $foto = $datos['foto_perfil'] ?? null;
        $eliminar = (bool) ($datos['eliminar_foto'] ?? false);
        unset($datos['foto_perfil'], $datos['eliminar_foto']);
        // ────────────────────────────────────────────────────────────────────

        // 1) ¿Cambia el correo? Delegar en ServicioCuentas (ya lo haces).
        $emailNuevo = strtolower(trim((string) ($datos['email'] ?? '')));
        $cambiaEmail = $emailNuevo !== '' && $emailNuevo !== $usuario->email;

        if ($cambiaEmail) {
            app(\App\Services\ServicioCuentas::class)
                ->solicitarCambioCorreo($usuario, $emailNuevo);
            unset($datos['email']); // no lo toques aquí; el servicio ya lo manejó
        }

        // 2) ¿Cambia la contraseña? Hashear (ya lo haces, lo dejo por claridad).
        if (!empty($datos['nueva_contrasena'])) {
            $usuario->password = \Hash::make($datos['nueva_contrasena']);
            unset($datos['nueva_contrasena']);
        }
        unset($datos['contrasena_actual'], $datos['nueva_contrasena_confirmation']);

        // 3) Asignar campos de texto (nombre, ingreso_mensual si aplica, etc.)
        //    Si usas fill()/update() con $datos ya limpios (sin foto ni password),
        //    está bien. Si prefieres uno a uno, hazlo también.
        if ($esAdmin) {
            unset($datos['ingreso_mensual']); // ya lo filtra el Request, refuerzo
        }
        $usuario->fill($datos);

        // 4) ── FOTO PERFIL: aplicar nueva o borrar la existente ────────────
        if ($foto) {
            // Borrar la anterior si existía
            if ($usuario->foto_perfil && Storage::disk('public')->exists($usuario->foto_perfil)) {
                Storage::disk('public')->delete($usuario->foto_perfil);
            }

            // Guardar la nueva en storage/app/public/avatares/
            // store() genera un nombre aleatorio (hash) y devuelve la ruta relativa.
            $usuario->foto_perfil = $foto->store('avatares', 'public');
        } elseif ($eliminar) {
            // El usuario marcó "Eliminar foto actual"
            if ($usuario->foto_perfil && Storage::disk('public')->exists($usuario->foto_perfil)) {
                Storage::disk('public')->delete($usuario->foto_perfil);
            }
            $usuario->foto_perfil = null;
        }
        // ────────────────────────────────────────────────────────────────────

        // 5) Guardar
        $usuario->save();

        // 6) Redirección con mensaje (ajusta al que ya uses)
        return redirect()
            ->route('ajustes.perfil')
            ->with('exito', $cambiaEmail
                ? 'Perfil actualizado. Revisa tu nuevo correo para confirmar el cambio.'
                : 'Tu perfil se actualizó correctamente.');
    }

    public function reenviarConfirmacionCorreo(Request $request): RedirectResponse
    {
        try {
            $this->cuentas->reenviarConfirmacionCorreo($request->user());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('ajustes.perfil')
            ->with('exito', 'Te enviamos un nuevo enlace de confirmación.');
    }

    // =========================================================================
    // CONFIRMAR CORREO
    // =========================================================================

    public function confirmarCorreo(Request $request, string $token): RedirectResponse
    {
        // Captura ANTES del servicio: después, $usuario->email es el nuevo.
        $correoViejo = $request->user()->email;
        $nombreUsuario = $request->user()->nombre;

        try {
            $this->cuentas->confirmarCambioCorreo($request->user(), $token);
        } catch (ValidationException $e) {
            return redirect()
                ->route('ajustes.perfil')
                ->withErrors($e->errors());
        }

        // Notifica al correo ANTERIOR (por si el cambio fue un ataque).
        try {
            Notification::route('mail', $correoViejo)
                ->notify(new AlertaSeguridad('email.changed', [
                    'nombre' => $nombreUsuario,
                    'correo_nuevo' => $request->user()->email,
                    'fecha' => now()->format('Y-m-d H:i'),
                ]));
        } catch (\Throwable $e) {
            Log::warning('No se pudo enviar alerta email.changed', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
            ]);
        }

        $request->user()->setRememberToken(null);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('exito', 'Tu correo se actualizó. Inicia sesión con el nuevo.');
    }

    // =========================================================================
    // TIPO DE CUENTA
    // =========================================================================

    public function tipoCuenta(Request $request): View
    {
        abort_unless(
            in_array($request->user()->rol, ['estandar', 'familiar'], true),
            403
        );

        $usuario = $request->user();

        return view('app.ajustes.tipo-cuenta', [
            'usuario' => $usuario,
            'tieneMetasCompartidasActivas' =>
                $this->metas->tieneMetasCompartidasActivas($usuario),
        ]);
    }

    public function cambiarTipo(Request $request): RedirectResponse
    {
        abort_unless(
            in_array($request->user()->rol, ['estandar', 'familiar'], true),
            403
        );

        $datos = $request->validate([
            'tipo_cuenta' => ['required', 'in:estandar,familiar'],
        ], [
            'tipo_cuenta.required' => 'Selecciona un tipo de cuenta.',
            'tipo_cuenta.in' => 'Selecciona un tipo de cuenta válido.',
        ]);

        try {
            $this->cuentas->cambiarTipo($request->user(), $datos['tipo_cuenta']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('ajustes.tipo-cuenta')
            ->with('exito', 'Tipo de cuenta actualizado.');
    }

    // =========================================================================
    // ELIMINAR CUENTA
    // =========================================================================

    public function eliminarCuenta(Request $request): View
    {
        abort_unless(
            in_array($request->user()->rol, ['estandar', 'familiar'], true),
            403
        );

        return view('app.ajustes.eliminar-cuenta', [
            'usuario' => $request->user(),
        ]);
    }

    public function destruirCuenta(Request $request): RedirectResponse
    {
        abort_unless(
            in_array($request->user()->rol, ['estandar', 'familiar'], true),
            403
        );

        $request->validate([
            'contrasena_actual' => ['required', 'current_password'],
            'confirmar_eliminacion' => ['required', 'accepted'],
        ], [
            'contrasena_actual.required' => 'Ingresa tu contraseña actual.',
            'contrasena_actual.current_password' => 'La contraseña actual no es correcta.',
            'confirmar_eliminacion.required' => 'Debes confirmar que deseas eliminar tu cuenta.',
            'confirmar_eliminacion.accepted' => 'Debes confirmar que deseas eliminar tu cuenta.',
        ]);

        $usuario = $request->user();

        try {
            $this->cuentas->eliminarCuenta($usuario);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $usuario->setRememberToken(null);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('inicio')
            ->with('exito', 'Tu cuenta se eliminó correctamente.');
    }
}