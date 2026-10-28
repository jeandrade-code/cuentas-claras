<?php

namespace Tests\Feature\Admin;

use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\Gasto;
use App\Models\GastoFijo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriasSistemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_ve_el_listado(): void
    {
        $admin = User::factory()->administrador()->create();
        $this->actingAs($admin)->get(route('admin.categorias'))->assertOk();
    }

    public function test_no_admin_recibe_403(): void
    {
        $user = User::factory()->create(['rol' => 'estandar']);
        $this->actingAs($user)->get(route('admin.categorias'))->assertForbidden();
    }

    public function test_admin_crea_una_categoria_del_sistema(): void
    {
        $admin = User::factory()->administrador()->create();

        $this->actingAs($admin)->post(route('admin.categorias.store'), [
            'nombre' => 'Nueva del sistema',
            'bloque' => 'necesidades',
            'peso_recomendado' => 50,
        ])->assertRedirect();

        $this->assertDatabaseHas('categorias', [
            'nombre' => 'Nueva del sistema',
            'user_id' => null,
            'activa' => true,
        ]);
    }

    public function test_admin_no_puede_duplicar_nombre(): void
    {
        $admin = User::factory()->administrador()->create();
        Categoria::factory()->delSistema()->create(['nombre' => 'Mercado']);

        $this->actingAs($admin)
            ->post(route('admin.categorias.store'), [
                'nombre' => 'Mercado',
                'bloque' => 'necesidades',
            ])
            ->assertSessionHasErrors('nombre');
    }

    public function test_admin_puede_eliminar_categoria_del_sistema_sin_uso(): void
    {
        $admin = User::factory()->administrador()->create();
        $categoria = Categoria::factory()->delSistema()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.categorias.destroy', $categoria), [
                'password' => 'password', // password por defecto de la factory
            ]);

        $response->assertRedirect(route('admin.categorias'));
        $response->assertSessionHas('exito', 'Categoría eliminada');
        $this->assertDatabaseMissing('categorias', ['id' => $categoria->id]);
    }

    public function test_no_se_puede_eliminar_categoria_con_gastos(): void
    {
        $admin = User::factory()->administrador()->create();
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->delSistema()->create();

        // Usar user_id directo: no depender del nombre de la relación.
        Gasto::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.categorias.destroy', $categoria), [
                'password' => 'password',
            ]);

        $response->assertSessionHasErrors('categoria');
        $this->assertDatabaseHas('categorias', ['id' => $categoria->id]);
    }

    public function test_no_se_puede_eliminar_categoria_con_gastos_fijos(): void
    {
        $admin = User::factory()->administrador()->create();
        $usuario = User::factory()->create();
        $categoria = Categoria::factory()->delSistema()->create();

        GastoFijo::factory()->create([
            'user_id' => $usuario->id,
            'categoria_id' => $categoria->id,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.categorias.destroy', $categoria), [
                'password' => 'password',
            ]);

        $response->assertSessionHasErrors('categoria');
        $this->assertDatabaseHas('categorias', ['id' => $categoria->id]);
    }

    public function test_eliminar_categoria_requiere_password_correcta(): void
    {
        $admin = User::factory()->administrador()->create();
        $categoria = Categoria::factory()->delSistema()->create();

        // Sin password: el Request lanza error sobre el campo 'password'.
        $this->actingAs($admin)
            ->delete(route('admin.categorias.destroy', $categoria))
            ->assertSessionHasErrors('password');

        // Password incorrecta.
        $this->actingAs($admin)
            ->delete(route('admin.categorias.destroy', $categoria), [
                'password' => 'incorrecta',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseHas('categorias', ['id' => $categoria->id]);
    }

    public function test_eliminar_categoria_crea_registro_de_auditoria(): void
    {
        $admin = User::factory()->administrador()->create();
        $categoria = Categoria::factory()->delSistema()->create(['nombre' => 'Categoría Test']);

        $this->actingAs($admin)
            ->delete(route('admin.categorias.destroy', $categoria), [
                'password' => 'password',
            ]);

        $this->assertDatabaseHas('auditorias', [
            'accion' => 'categoria.eliminar',
            'modelo' => 'Categoria',
            'modelo_id' => $categoria->id,
            'user_id' => $admin->id,
        ]);
    }

    public function test_no_admin_no_puede_eliminar_categorias(): void
    {
        $usuario = User::factory()->create(['rol' => 'estandar']);
        $categoria = Categoria::factory()->delSistema()->create();

        $this->actingAs($usuario)
            ->delete(route('admin.categorias.destroy', $categoria), [
                'current_password' => 'password',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('categorias', ['id' => $categoria->id]);
    }

    public function test_desactivar_cambia_el_estado(): void
    {
        $admin = User::factory()->administrador()->create(['password' => bcrypt('secreto123')]);
        $categoria = Categoria::factory()->delSistema()->create(['activa' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.categorias.desactivar', $categoria), [
                'password' => 'secreto123',
            ]);

        $this->assertFalse($categoria->fresh()->activa);
    }

    public function test_desactivar_sin_password_falla(): void
    {
        $admin = User::factory()->administrador()->create();
        $categoria = Categoria::factory()->delSistema()->create(['activa' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.categorias.desactivar', $categoria))
            ->assertSessionHasErrors('password');

        $this->assertTrue($categoria->fresh()->activa);
    }

    public function test_no_se_puede_desactivar_una_categoria_personal(): void
    {
        $admin = User::factory()->administrador()->create();
        $user = User::factory()->create();
        $categoria = Categoria::factory()->create(['user_id' => $user->id]);

        $this->actingAs($admin)
            ->patch(route('admin.categorias.desactivar', $categoria))
            ->assertForbidden();
    }
}