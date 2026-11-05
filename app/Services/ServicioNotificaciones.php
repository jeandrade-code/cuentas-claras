<?php

namespace App\Services;

use App\Models\GastoFijo;
use App\Models\Notificacion;
use App\Models\PagoGastoFijo;
use App\Models\User;

/**
 * Qué hace: concentra la creación, el conteo, la cancelación y el marcado
 *           como leídas de las notificaciones (contrato C4).
 * Quién lo usa: GastoFijoController (N3), NotificacionController (N4),
 *           el comando diario (J6), el View Composer de la campana y,
 *           cuando se refactorice, ServicioMetas::notificar().
 * Dueño: Juan (J6).
 * Ejemplo: $servicio->crear($usuario, ServicioNotificaciones::TIPO_POR_VENCER,
 *          'Tu gasto fijo "Internet" vence el 30/10/2026.',
 *          ['pago_gasto_fijo_id' => $pago->id, 'clave_unica' => "por_vencer:{$pago->id}"]);
 */
class ServicioNotificaciones
{
    public const TIPO_POR_VENCER = 'por_vencer';
    public const TIPO_VENCIDO = 'vencido';
    public const TIPO_INVITACION_META = 'invitacion_meta';
    public const TIPO_INVITACION_META_REQUIERE_FAMILIAR = 'invitacion_meta_requiere_familiar';
    public const TIPO_RESPUESTA_META = 'respuesta_meta';
    public const TIPO_META_FINALIZADA = 'meta_finalizada';
    public const TIPO_META_ABANDONADA = 'meta_abandonada';
    public const TIPO_INFORME_DISPONIBLE = 'informe_disponible';

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_LEIDA = 'leida';
    public const ESTADO_CANCELADA = 'cancelada';

    /**
     * Crea una notificación. Si recibe clave_unica y ya existe una con
     * la misma clave para el usuario:
     *  - si estaba cancelada, se reactiva con el mensaje nuevo;
     *  - si estaba pendiente o leída, se deja como estaba.
     * Esto garantiza idempotencia del comando diario.
     */
    public function crear(
        User $usuario,
        string $tipo,
        string $mensaje,
        array $opciones = []
    ): Notificacion {
        $datos = [
            'user_id' => $usuario->id,
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'estado' => self::ESTADO_PENDIENTE,
            'pago_gasto_fijo_id' => $opciones['pago_gasto_fijo_id'] ?? null,
            'meta_id' => $opciones['meta_id'] ?? null,
            'clave_unica' => $opciones['clave_unica'] ?? null,
        ];

        if ($datos['clave_unica'] === null) {
            return Notificacion::create($datos);
        }

        $existente = Notificacion::query()
            ->where('user_id', $usuario->id)
            ->where('clave_unica', $datos['clave_unica'])
            ->first();

        if ($existente === null) {
            return Notificacion::create($datos);
        }

        if ($existente->estado === self::ESTADO_CANCELADA) {
            $existente->update([
                'estado' => self::ESTADO_PENDIENTE,
                'mensaje' => $datos['mensaje'],
            ]);
        }

        return $existente;
    }

    /**
     * Cuenta las notificaciones pendientes del usuario (para la campana).
     */
    public function contarPendientes(User $usuario): int
    {
        return Notificacion::delUsuario((int) $usuario->id)
            ->where('estado', self::ESTADO_PENDIENTE)
            ->count();
    }

    /**
     * Cancela las notificaciones pendientes ligadas a un pago concreto.
     * Se usa al marcar un gasto fijo como pagado.
     */
    public function cancelarPorPago(User $usuario, PagoGastoFijo $pago): int
    {
        return Notificacion::delUsuario((int) $usuario->id)
            ->where('pago_gasto_fijo_id', $pago->id)
            ->where('estado', self::ESTADO_PENDIENTE)
            ->update(['estado' => self::ESTADO_CANCELADA]);
    }

    /**
     * Cancela las notificaciones pendientes ligadas a cualquier pago
     * del gasto fijo. Se usa al editar o eliminar el gasto fijo.
     */
    public function cancelarPorGastoFijo(User $usuario, GastoFijo $gastoFijo): int
    {
        $idsPagos = $gastoFijo->pagos()->pluck('id');

        if ($idsPagos->isEmpty()) {
            return 0;
        }

        return Notificacion::delUsuario((int) $usuario->id)
            ->whereIn('pago_gasto_fijo_id', $idsPagos)
            ->where('estado', self::ESTADO_PENDIENTE)
            ->update(['estado' => self::ESTADO_CANCELADA]);
    }

    /**
     * Marca una notificación como leída. El controlador (N4) ya autoriza
     * que la notificación sea del usuario antes de llamar aquí.
     */
    public function marcarLeida(Notificacion $notificacion): void
    {
        if ($notificacion->estado !== self::ESTADO_PENDIENTE) {
            return;
        }

        $notificacion->update(['estado' => self::ESTADO_LEIDA]);
    }

    /**
     * Marca todas las pendientes del usuario como leídas.
     * Devuelve cuántas se actualizaron.
     */
    public function marcarTodasLeidas(User $usuario): int
    {
        return Notificacion::delUsuario((int) $usuario->id)
            ->where('estado', self::ESTADO_PENDIENTE)
            ->update(['estado' => self::ESTADO_LEIDA]);
    }
}