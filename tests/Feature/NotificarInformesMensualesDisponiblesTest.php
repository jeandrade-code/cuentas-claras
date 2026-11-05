<?php

namespace Tests\Feature;

use App\Models\Notificacion;
use App\Models\User;
use App\Services\ServicioNotificaciones;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class NotificarInformesMensualesDisponiblesTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_comando_notifica_a_usuarios_activos_estandar_y_familiares(): void
    {
        $estandar = User::factory()->create([
            'rol' => 'estandar',
            'activo' => true,
        ]);

        $familiar = User::factory()->familiar()->create([
            'activo' => true,
        ]);

        User::factory()->administrador()->create();

        User::factory()->inactivo()->create();

        Artisan::call('informes:notificar-disponibles', [
            '--fecha' => '2026-11-01',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $estandar->id,
            'tipo' => ServicioNotificaciones::TIPO_INFORME_DISPONIBLE,
            'clave_unica' => 'informe_disponible:2026-10',
        ]);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $familiar->id,
            'tipo' => ServicioNotificaciones::TIPO_INFORME_DISPONIBLE,
            'clave_unica' => 'informe_disponible:2026-10',
        ]);

        $this->assertDatabaseCount('notificaciones', 2);
    }

    public function test_el_comando_no_duplica_notificaciones_al_ejecutarse_dos_veces(): void
    {
        $usuario = User::factory()->create();

        Artisan::call('informes:notificar-disponibles', [
            '--fecha' => '2026-11-01',
        ]);

        Artisan::call('informes:notificar-disponibles', [
            '--fecha' => '2026-11-01',
        ]);

        $this->assertDatabaseCount('notificaciones', 1);

        $this->assertDatabaseHas('notificaciones', [
            'user_id' => $usuario->id,
            'clave_unica' => 'informe_disponible:2026-10',
        ]);
    }
}
