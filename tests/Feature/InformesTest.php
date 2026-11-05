<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\Informe;
use App\Models\User;
use App\Models\GastoFijo;
use App\Models\PagoGastoFijo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class InformesTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_no_puede_consultar_el_informe_de_otro(): void
    {
        $usuarioA = User::factory()->create(['ingreso_mensual' => 3000000]);
        $usuarioB = User::factory()->create(['ingreso_mensual' => 2500000]);

        $informe = Informe::create([
            'user_id' => $usuarioA->id,
            'anio' => 2026,
            'mes' => 9,
            'ingreso' => 3000000,
            'total_gastado' => 500000,
        ]);

        $this->actingAs($usuarioB)
            ->get(route('informes.ver', $informe->id))
            ->assertNotFound();
    }

    public function test_el_informe_guardado_no_cambia_al_editar_un_gasto(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Arriendo',
            'bloque' => 'necesidades',
        ]);

        $gasto = Gasto::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'monto' => 1200000,
            'fecha' => '2026-09-10',
        ]);

        $this->actingAs($usuario)
            ->post(route('informes.guardar'), [
                'mes' => '2026-09',
            ])
            ->assertRedirect()
            ->assertSessionHas('exito');

        $informe = Informe::where('user_id', $usuario->id)
            ->where('anio', 2026)
            ->where('mes', 9)
            ->firstOrFail();

        $this->assertSame(1200000, (int) $informe->total_gastado);

        $this->assertDatabaseHas('detalles_informe', [
            'informe_id' => $informe->id,
            'categoria_nombre' => 'Arriendo',
            'bloque' => 'necesidades',
            'monto_gastado' => 1200000,
        ]);

        // Simulamos que el usuario modifica el gasto después de guardar.
        $gasto->update(['monto' => 500000]);

        // El informe histórico debe conservar los valores originales.
        $informe->refresh();

        $this->assertSame(1200000, (int) $informe->total_gastado);

        $this->assertDatabaseHas('detalles_informe', [
            'informe_id' => $informe->id,
            'categoria_nombre' => 'Arriendo',
            'monto_gastado' => 1200000,
        ]);

        $this->assertDatabaseMissing('detalles_informe', [
            'informe_id' => $informe->id,
            'categoria_nombre' => 'Arriendo',
            'monto_gastado' => 500000,
        ]);

        // El usuario debe poder abrir su fotografía histórica.
        $this->get(route('informes.ver', $informe->id))
            ->assertOk()
            ->assertSee('Consulta de un informe guardado')
            ->assertSee('1.200.000');
    }

    public function test_el_informe_muestra_un_estado_vacio_si_no_hay_gastos(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertSee('Aún no tienes gastos en este mes');
    }

    public function test_el_informe_indica_cuando_falta_registrar_el_ingreso(): void
    {
        $usuario = User::factory()->sinIngreso()->create();

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertSee('Registra tu ingreso mensual');
    }

    
    public function test_el_informe_incluye_los_gastos_fijos_pagados_en_el_mes(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Arriendo',
            'bloque' => 'necesidades',
        ]);

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Arriendo',
            'monto' => 900000,
        ]);

        PagoGastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'gasto_fijo_id' => $gastoFijo->id,
            'periodo' => '2026-09',
            'estado' => 'pagado',
            'fecha_pago' => '2026-09-10',
            'fecha_vencimiento' => '2026-09-05',
            'monto' => 900000,
        ]);

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertSee('Arriendo')
            ->assertSee('900.000');

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_el_informe_no_incluye_los_gastos_fijos_pendientes(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Internet',
            'bloque' => 'necesidades',
        ]);

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'monto' => 120000,
        ]);

        PagoGastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'gasto_fijo_id' => $gastoFijo->id,
            'periodo' => '2026-09',
            'estado' => 'pendiente',
            'fecha_pago' => null,
            'monto' => 120000,
        ]);

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertDontSee('120.000');

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_el_informe_no_incluye_pagos_realizados_en_otro_mes(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Internet',
            'bloque' => 'necesidades',
        ]);

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'monto' => 120000,
        ]);

        PagoGastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'gasto_fijo_id' => $gastoFijo->id,
            'periodo' => '2026-10',
            'estado' => 'pagado',
            'fecha_pago' => '2026-10-05',
            'fecha_vencimiento' => '2026-10-05',
            'monto' => 120000,
        ]);

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertDontSee('120.000');

        $this->assertDatabaseCount('gastos', 0);
    }

    public function test_el_informe_no_incluye_pagos_fijos_de_otro_usuario(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $otroUsuario = User::factory()->create([
            'ingreso_mensual' => 2500000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Arriendo',
            'bloque' => 'necesidades',
        ]);

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $otroUsuario->id,
            'categoria_id' => $categoria->id,
            'monto' => 900000,
        ]);

        PagoGastoFijo::factory()->create([
            'user_id' => $otroUsuario->id,
            'gasto_fijo_id' => $gastoFijo->id,
            'periodo' => '2026-09',
            'estado' => 'pagado',
            'fecha_pago' => '2026-09-10',
            'fecha_vencimiento' => '2026-09-05',
            'monto' => 900000,
        ]);

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertDontSee('900.000');

        $this->assertDatabaseCount('gastos', 0);
    }
        public function test_los_gastos_hormiga_incluyen_el_limite_de_20000_y_muestran_su_descripcion(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Otros',
            'bloque' => 'deseos',
        ]);

        Gasto::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'monto' => 20000,
            'fecha' => '2026-09-10',
            'descripcion' => 'Compra de veinte mil',
        ]);

        Gasto::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'monto' => 20001,
            'fecha' => '2026-09-11',
            'descripcion' => 'Compra superior al limite',
        ]);

            $respuesta = $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertDontSee('Compra de veinte mil')
            ->assertDontSee('Compra superior al limite');

            $respuesta->assertViewHas('gastosHormiga', function ($datos) {
            return $datos['total'] === 0
            && $datos['cantidad'] === 0
            && $datos['promedio'] === 0
            && $datos['detalle']->isEmpty();
        });
    }

    public function test_los_gastos_hormiga_solo_corresponden_al_usuario_y_mes_seleccionados(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $otroUsuario = User::factory()->create([
            'ingreso_mensual' => 2500000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Otros',
            'bloque' => 'deseos',
        ]);

        // Cinco gastos del usuario en el mes seleccionado.
foreach (range(1, 5) as $dia) {
    Gasto::factory()->create([
        'user_id' => $usuario->id,
        'categoria_id' => $categoria->id,
        'monto' => 5000,
        'fecha' => '2026-09-' . str_pad($dia, 2, '0', STR_PAD_LEFT),
        'descripcion' => 'Compra del mes seleccionado',
    ]);
}

// Cinco gastos del mismo usuario, pero de otro mes.
foreach (range(1, 5) as $dia) {
    Gasto::factory()->create([
        'user_id' => $usuario->id,
        'categoria_id' => $categoria->id,
        'monto' => 7000,
        'fecha' => '2026-10-' . str_pad($dia, 2, '0', STR_PAD_LEFT),
        'descripcion' => 'Compra de otro mes',
    ]);
}

// Cinco gastos de otro usuario.
foreach (range(1, 5) as $dia) {
    Gasto::factory()->create([
        'user_id' => $otroUsuario->id,
        'categoria_id' => $categoria->id,
        'monto' => 9000,
        'fecha' => '2026-09-' . str_pad($dia, 2, '0', STR_PAD_LEFT),
        'descripcion' => 'Compra de otro usuario',
    ]);
}

        $respuesta = $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk()
            ->assertSee('Compra del mes seleccionado')
            ->assertDontSee('Compra de otro mes')
            ->assertDontSee('Compra de otro usuario');

        $respuesta->assertViewHas('gastosHormiga', function ($datos) {
    return $datos['total'] === 25000
        && $datos['cantidad'] === 5
        && $datos['promedio'] === 5000
        && $datos['detalle']->count() === 1;
});
    }

    public function test_los_pagos_de_gastos_fijos_no_se_cuentan_como_gastos_hormiga(): void
    {
        $usuario = User::factory()->create([
            'ingreso_mensual' => 3000000,
        ]);

        $categoria = Categoria::factory()->delSistema()->create([
            'nombre' => 'Internet',
            'bloque' => 'necesidades',
        ]);

        $gastoFijo = GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
            'nombre' => 'Internet',
            'monto' => 15000,
        ]);

        PagoGastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'gasto_fijo_id' => $gastoFijo->id,
            'periodo' => '2026-09',
            'estado' => 'pagado',
            'fecha_pago' => '2026-09-10',
            'fecha_vencimiento' => '2026-09-10',
            'monto' => 15000,
        ]);

        $respuesta = $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-09']))
            ->assertOk();

        $respuesta->assertViewHas('gastosHormiga', function ($datos) {
            return $datos['total'] === 0
                && $datos['cantidad'] === 0
                && $datos['promedio'] === 0
                && $datos['detalle']->isEmpty();
        });

        $this->assertDatabaseCount('gastos', 0);
    }

public function test_el_indicador_de_gastos_hormiga_no_altera_los_totales_del_informe(): void
{
    $usuario = User::factory()->create([
        'ingreso_mensual' => 3000000,
    ]);

    $categoria = Categoria::factory()->delSistema()->create([
        'nombre' => 'Otros',
        'bloque' => 'deseos',
    ]);

    Gasto::factory()->create([
        'user_id' => $usuario->id,
        'categoria_id' => $categoria->id,
        'monto' => 10000,
        'fecha' => '2026-09-10',
        'descripcion' => 'Compra pequeña',
    ]);

    Gasto::factory()->create([
        'user_id' => $usuario->id,
        'categoria_id' => $categoria->id,
        'monto' => 50000,
        'fecha' => '2026-09-12',
        'descripcion' => 'Compra grande',
    ]);

    $respuesta = $this->actingAs($usuario)
        ->get(route('informes.actual', ['mes' => '2026-09']))
        ->assertOk();

    $respuesta->assertViewHas('resumen', function ($resumen) {
        return (int) $resumen['total_gastado'] === 60000;
    });

    $respuesta->assertViewHas('gastosHormiga', function ($datos) {
        return $datos['total'] === 0
        && $datos['cantidad'] === 0
        && $datos['promedio'] === 0
        && $datos['detalle']->isEmpty();
    });
}
public function test_el_mes_actual_no_muestra_el_informe(): void
{
    Carbon::setTestNow('2026-11-05 08:00:00');

    try {
        $usuario = User::factory()->create();

        $respuesta = $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-11']))
            ->assertOk()
            ->assertSee('Informe aún no disponible')
            ->assertViewHas('informeDisponible', false);

        $respuesta->assertViewHas('informe', []);
    } finally {
        Carbon::setTestNow();
    }
}

public function test_un_mes_futuro_no_muestra_el_informe(): void
{
    Carbon::setTestNow('2026-11-05 08:00:00');

    try {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-12']))
            ->assertOk()
            ->assertSee('Informe aún no disponible')
            ->assertViewHas('informeDisponible', false);
    } finally {
        Carbon::setTestNow();
    }
}

public function test_no_se_puede_guardar_el_informe_del_mes_actual(): void
{
    Carbon::setTestNow('2026-11-05 08:00:00');

    try {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->post(route('informes.guardar'), [
                'mes' => '2026-11',
            ])
            ->assertRedirect(route('informes.actual', ['mes' => '2026-11']))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('informes', [
            'user_id' => $usuario->id,
            'anio' => 2026,
            'mes' => 11,
        ]);
    } finally {
        Carbon::setTestNow();
    }
}

public function test_un_mes_anterior_sigue_disponible(): void
{
    Carbon::setTestNow('2026-11-05 08:00:00');

    try {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->get(route('informes.actual', ['mes' => '2026-10']))
            ->assertOk()
            ->assertDontSee('Informe aún no disponible')
            ->assertViewHas('informeDisponible', true);
    } finally {
        Carbon::setTestNow();
    }
}

}
