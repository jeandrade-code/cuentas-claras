<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\RestablecerContrasena;
use Illuminate\Notifications\Notifiable;

/**
 * Usuario de Cuentas Claras. REEMPLAZA al User.php que trae Laravel.
 * Roles: estandar | familiar | administrador
 *
 * Los campos pending_email, email_token, pending_email_expires_at,
 * last_login_ip y last_login_at NO están en $fillable a propósito: se
 * asignan a mano (con forceFill) para que nadie pueda mass-assign un token,
 * un correo pendiente o la IP de último login desde un formulario.
 *
 * 2026-10-22: se agregan last_login_ip, last_login_at, esIpNueva(),
 * registrarLogin() y rutaPanel(). Ver J9.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre',
        'cedula',
        'email',
        'password',
        'ingreso_mensual',
        'consentimiento_datos_at',
        'foto_perfil',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_token',
        'pending_email',
        'pending_email_expires_at',
        'last_login_ip',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'ingreso_mensual' => 'integer',
            'consentimiento_datos_at' => 'datetime',
            'pending_email_expires_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new RestablecerContrasena($token));
    }

    /**
     * Ruta del panel según el rol. Fix del 404 en /panel para admin.
     * Uso: auth()->user()->rutaPanel() desde el header, redirecciones, etc.
     */
    public function rutaPanel(): string
    {
        return $this->rol === 'administrador'
            ? route('admin.panel')
            : route('panel');
    }

    /**
     * ¿La IP es distinta a la del último login exitoso?
     * Devuelve false si nunca ha iniciado sesión (para no notificar en el
     * primer login tras el registro).
     */
    public function esIpNueva(?string $ip): bool
    {
        return $this->last_login_at !== null
            && $this->last_login_ip !== $ip;
    }

    /**
     * Registra la IP y fecha del login exitoso.
     * Usa forceFill porque estos campos no son fillable a propósito.
     */
    public function registrarLogin(?string $ip, ?string $userAgent = null): void
    {
        $this->forceFill([
            'last_login_ip' => $ip,
            'last_login_at' => now(),
        ])->save();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (!$this->foto_perfil)
            return null;
        if (!\Storage::disk('public')->exists($this->foto_perfil))
            return null;

        // URL relativa: '/storage/avatares/xxx.jpg'
        // El navegador la resuelve contra el origen actual (localhost, 127.0.0.1,
        // dominio real en producción...). CSP 'self' siempre la permite.
        return '/storage/' . ltrim($this->foto_perfil, '/');
    }

    public function gastos(): HasMany
    {
        return $this->hasMany(Gasto::class);
    }
    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
    }
    public function gastosFijos(): HasMany
    {
        return $this->hasMany(GastoFijo::class);
    }
    public function simulaciones(): HasMany
    {
        return $this->hasMany(SimulacionCredito::class);
    }
    public function informes(): HasMany
    {
        return $this->hasMany(Informe::class);
    }
    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }
}