<?php

namespace App\Services;

use App\Models\Meta;
use App\Models\User;
use App\Notifications\ConfirmarCambioCorreo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * QUÉ HACE : cambiar el tipo de cuenta, eliminar la cuenta y gestionar el cambio
 *            de correo con verificación. Contrato C7.
 * CONTRATO C7 : lo llama AjustesController (V4). Valeria solo arma la pantalla.
 * REGLAS :
 *   - estandar → familiar : siempre permitido.
 *   - familiar → estandar : bloqueado si el usuario tiene METAS COMPARTIDAS ACTIVAS.
 *   - Mismo tipo : no hace nada.
 *   - 'rol' NO es fillable: se asigna a mano.
 *   - eliminarCuenta: no se puede borrar al último administrador activo.
 *   - solicitarCambioCorreo: token hasheado, expira en 1 hora, correo al NUEVO.
 *   - confirmarCambioCorreo: valida hash, expiración y que el correo no esté ocupado.
 */
class ServicioCuentas
{
    public function __construct(private ServicioMetas $metas)
    {
    }

    // =========================================================================
    // CAMBIO DE TIPO (J5 / C7)
    // =========================================================================

    public function cambiarTipo(User $usuario, string $nuevoTipo): void
    {
        if (!in_array($nuevoTipo, ['estandar', 'familiar'], true)) {
            throw ValidationException::withMessages([
                'tipo_cuenta' => 'Selecciona un tipo de cuenta válido.',
            ]);
        }

        if ($usuario->rol === $nuevoTipo) {
            return;
        }

        if ($nuevoTipo === 'estandar' && $this->metas->tieneMetasCompartidasActivas($usuario)) {
            throw ValidationException::withMessages([
                'tipo_cuenta' => 'Debes finalizar o cancelar tus metas compartidas activas antes de cambiar a una cuenta estándar.',
            ]);
        }

        DB::transaction(function () use ($usuario, $nuevoTipo) {
            $usuario->rol = $nuevoTipo;
            $usuario->save();
        });
    }

    // =========================================================================
    // CAMBIO DE CORREO (V4)
    // =========================================================================

    /**
     * Genera un token, lo guarda hasheado con vencimiento a 1 hora y envía la
     * notificación al NUEVO correo. El correo actual sigue activo hasta que el
     * usuario confirme.
     */
    public function solicitarCambioCorreo(User $usuario, string $nuevoCorreo): void
    {
        $nuevoCorreo = strtolower(trim($nuevoCorreo));

        if ($nuevoCorreo === $usuario->email) {
            return;
        }

        if (User::where('email', $nuevoCorreo)->where('id', '!=', $usuario->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Este correo electrónico ya está registrado.',
            ]);
        }

        if (User::where('pending_email', $nuevoCorreo)->where('id', '!=', $usuario->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Otra cuenta está pendiente de confirmar este correo. Intenta con otro.',
            ]);
        }

        $tokenPlano = Str::random(64);
        $tokenHash = hash('sha256', $tokenPlano);

        DB::transaction(function () use ($usuario, $nuevoCorreo, $tokenHash) {
            $usuario->pending_email = $nuevoCorreo;
            $usuario->email_token = $tokenHash;
            $usuario->pending_email_expires_at = now()->addHour();
            $usuario->save();
        });

        $usuario->refresh();

        Notification::route('mail', $nuevoCorreo)
            ->notify(new ConfirmarCambioCorreo(
                token: $tokenPlano,
                usuario: $usuario,
                expiraEn: '1 hora',
            ));
    }

    /**
     * Reenvía la confirmación del cambio pendiente (por si el correo se perdió).
     * Regenera token y vencimiento, NO cambia el pending_email.
     */
    public function reenviarConfirmacionCorreo(User $usuario): void
    {
        if (!$usuario->pending_email) {
            throw ValidationException::withMessages([
                'email' => 'No hay un cambio de correo pendiente.',
            ]);
        }

        $tokenPlano = Str::random(64);
        $tokenHash = hash('sha256', $tokenPlano);

        DB::transaction(function () use ($usuario, $tokenHash) {
            $usuario->email_token = $tokenHash;
            $usuario->pending_email_expires_at = now()->addHour();
            $usuario->save();
        });

        $usuario->refresh();

        Notification::route('mail', $usuario->pending_email)
            ->notify(new ConfirmarCambioCorreo(
                token: $tokenPlano,
                usuario: $usuario,
                expiraEn: '1 hora',
            ));
    }

    /**
     * Confirma el cambio de correo. Devuelve el usuario actualizado.
     * Lanza ValidationException si el token no existe, expiró o el correo ya
     * fue confirmado por otra cuenta.
     */
    public function confirmarCambioCorreo(User $usuario, string $token): User
    {
        $tokenHash = hash('sha256', $token);

        if (
            !$usuario->email_token
            || !$usuario->pending_email
            || !hash_equals($usuario->email_token, $tokenHash)
        ) {
            throw ValidationException::withMessages([
                'token' => 'El enlace de confirmación no es válido o ya expiró.',
            ]);
        }

        if (
            !$usuario->pending_email_expires_at
            || $usuario->pending_email_expires_at->isPast()
        ) {
            throw ValidationException::withMessages([
                'token' => 'El enlace de confirmación expiró. Solicita uno nuevo.',
            ]);
        }

        if (
            User::where('email', $usuario->pending_email)
                ->where('id', '!=', $usuario->id)
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'token' => 'Ese correo ya fue confirmado por otra cuenta.',
            ]);
        }

        DB::transaction(function () use ($usuario) {
            $usuario->email = $usuario->pending_email;
            $usuario->pending_email = null;
            $usuario->email_token = null;
            $usuario->pending_email_expires_at = null;
            $usuario->save();
        });

        return $usuario->refresh();
    }

    // =========================================================================
    // ELIMINAR CUENTA (J8a)
    // =========================================================================

    public function eliminarCuenta(User $usuario): void
    {
        $this->verificarUltimoAdministrador($usuario);

        DB::transaction(function () use ($usuario) {
            $this->cancelarMetasCompartidasActivas($usuario);
            // Borrar la foto de perfil del storage antes de eliminar la cuenta.
            if ($usuario->foto_perfil && \Storage::disk('public')->exists($usuario->foto_perfil)) {
                \Storage::disk('public')->delete($usuario->foto_perfil);
            }
            $usuario->delete();
        });
    }

    // =========================================================================
    // CAMBIAR ROL / SUSPENDER / REACTIVAR (J7)
    // =========================================================================

    public function cambiarRol(User $usuario, string $nuevoRol): void
    {
        if (!in_array($nuevoRol, ['estandar', 'familiar', 'administrador'], true)) {
            throw ValidationException::withMessages([
                'rol' => 'Selecciona un rol válido.',
            ]);
        }

        if ($usuario->rol === $nuevoRol) {
            return;
        }

        if ($usuario->rol === 'administrador' && $nuevoRol !== 'administrador') {
            $this->verificarUltimoAdministrador($usuario);
        }

        if (
            $usuario->rol === 'familiar' && $nuevoRol === 'estandar'
            && $this->metas->tieneMetasCompartidasActivas($usuario)
        ) {
            throw ValidationException::withMessages([
                'rol' => 'Debes finalizar o cancelar las metas compartidas activas antes de bajar a estándar.',
            ]);
        }

        DB::transaction(function () use ($usuario, $nuevoRol) {
            $usuario->rol = $nuevoRol;
            $usuario->save();
        });
    }

    public function suspender(User $usuario): void
    {
        if (!$usuario->activo) {
            return;
        }

        $this->verificarUltimoAdministrador($usuario);

        DB::transaction(function () use ($usuario) {
            $usuario->activo = false;
            $usuario->save();
        });
    }

    public function reactivar(User $usuario): void
    {
        if ($usuario->activo) {
            return;
        }

        DB::transaction(function () use ($usuario) {
            $usuario->activo = true;
            $usuario->save();
        });
    }

    private function verificarUltimoAdministrador(User $usuario): void
    {
        if ($usuario->rol !== 'administrador' || !$usuario->activo) {
            return;
        }

        $otrosAdministradoresActivos = User::query()
            ->where('rol', 'administrador')
            ->where('activo', true)
            ->where('id', '!=', $usuario->id)
            ->exists();

        if (!$otrosAdministradoresActivos) {
            throw ValidationException::withMessages([
                'cuenta' => 'No puedes eliminar tu cuenta porque eres el único administrador activo del sistema.',
            ]);
        }
    }

    private function cancelarMetasCompartidasActivas(User $usuario): void
    {
        $notificaciones = app(ServicioNotificaciones::class);

        $metas = Meta::query()
            ->where('compartida', true)
            ->where('estado', 'activa')
            ->where(function ($consulta) use ($usuario) {
                $consulta
                    ->where('creador_id', $usuario->id)
                    ->orWhereHas('participaciones', function ($sub) use ($usuario) {
                        $sub->where('user_id', $usuario->id)
                            ->where('estado_invitacion', 'aceptada');
                    });
            })
            ->with(['participaciones'])
            ->get();

        foreach ($metas as $meta) {
            $otrosParticipantes = $meta->participaciones
                ->filter(
                    fn($p) =>
                        (int) $p->user_id !== (int) $usuario->id
                        && $p->estado_invitacion === 'aceptada'
                );

            if ((int) $meta->creador_id === (int) $usuario->id) {
                $nuevoCreador = $otrosParticipantes->first();

                if ($nuevoCreador === null) {
                    continue;
                }

                $meta->creador_id = $nuevoCreador->user_id;
            }

            $meta->estado = 'cancelada';
            $meta->save();

            foreach ($otrosParticipantes as $participacion) {
                $otro = User::find($participacion->user_id);

                if ($otro === null) {
                    continue;
                }

                $notificaciones->crear(
                    $otro,
                    ServicioNotificaciones::TIPO_META_ABANDONADA,
                    sprintf(
                        'La meta compartida "%s" se canceló porque el otro participante eliminó su cuenta.',
                        $meta->nombre
                    ),
                    [
                        'meta_id' => $meta->id,
                        'clave_unica' => 'meta_abandonada:' . $meta->id . ':' . $otro->id,
                    ]
                );
            }
        }
    }
}