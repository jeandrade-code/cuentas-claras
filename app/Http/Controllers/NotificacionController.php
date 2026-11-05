<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Services\ServicioNotificaciones;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    /**
     * Muestra las notificaciones del usuario autenticado.
     */
    public function index(): View
    {
        $usuarioId = (int) auth()->id();

        $pendientes = Notificacion::delUsuario($usuarioId)
            ->where('estado', ServicioNotificaciones::ESTADO_PENDIENTE)
            ->latest()
            ->paginate(10, ['*'], 'pendientes');

        $anteriores = Notificacion::delUsuario($usuarioId)
            ->whereIn('estado', [
                ServicioNotificaciones::ESTADO_LEIDA,
                ServicioNotificaciones::ESTADO_CANCELADA,
            ])
            ->latest()
            ->paginate(10, ['*'], 'anteriores');

        return view('app.notificaciones.index', compact(
            'pendientes',
            'anteriores'
        ));
    }

    /**
     * Marca una notificación propia como leída y abre su destino.
     */
    public function leer(
        Request $request,
        ServicioNotificaciones $servicio
    ): RedirectResponse {
        $datos = $request->validate([
            'notificacion_id' => ['required', 'integer', 'min:1'],
        ]);

        $notificacionId = (int) $datos['notificacion_id'];

        $notificacion = Notificacion::delUsuario((int) auth()->id())
            ->whereKey($notificacionId)
            ->first();

        if ($notificacion === null) {
            $existe = Notificacion::query()
                ->whereKey($notificacionId)
                ->exists();

            abort_if($existe, 403);
            abort(404);
        }

        $this->authorize('update', $notificacion);

        $servicio->marcarLeida($notificacion);

        if (in_array($notificacion->tipo, [
            ServicioNotificaciones::TIPO_INVITACION_META,
            ServicioNotificaciones::TIPO_INVITACION_META_REQUIERE_FAMILIAR,
        ], true)) {
            return redirect()->route('metas.invitaciones');
        }

        if ($notificacion->tipo === ServicioNotificaciones::TIPO_INFORME_DISPONIBLE) {
    if (preg_match(
        '/^informe_disponible:(\d{4}-\d{2})$/',
        $notificacion->clave_unica ?? '',
        $coincidencias
    )) {
        return redirect()->route('informes.actual', [
            'mes' => $coincidencias[1],
        ]);
    }
}

        if (
            $notificacion->meta_id !== null
            && in_array($notificacion->tipo, [
                ServicioNotificaciones::TIPO_RESPUESTA_META,
                ServicioNotificaciones::TIPO_META_FINALIZADA,
                ServicioNotificaciones::TIPO_META_ABANDONADA,
            ], true)
        ) {
            return redirect()->route('metas.show', [
                'meta' => $notificacion->meta_id,
            ]);
        }

        if (in_array($notificacion->tipo, [
            ServicioNotificaciones::TIPO_POR_VENCER,
            ServicioNotificaciones::TIPO_VENCIDO,
        ], true)) {
            return redirect()->route('gastos-fijos.index');
        }

        return redirect()->route('notificaciones.index');
    }

    /**
     * Marca todas las notificaciones pendientes del usuario como leídas.
     */
    public function leerTodas(
        ServicioNotificaciones $servicio
    ): RedirectResponse {
        $this->authorize('viewAny', Notificacion::class);

        $cantidad = $servicio->marcarTodasLeidas(auth()->user());

        return redirect()
            ->route('notificaciones.index')
            ->with(
                'exito',
                $cantidad > 0
                    ? 'Todas las notificaciones se marcaron como leídas.'
                    : 'No tienes notificaciones pendientes.'
            );
    }
}
