<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles; // Agrega esta línea


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles; // Y agrega este trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function primaryRoleName(): string
    {
        return $this->getRoleNames()->first() ?? 'usuario';
    }

    public function roleLabel(): string
    {
        return match ($this->primaryRoleName()) {
            'administrador' => 'Administrador',
            'gerente' => 'Gerente',
            'vendedor' => 'Vendedor',
            default => Str::headline($this->primaryRoleName()),
        };
    }

    public function initials(): string
    {
        $segments = collect(preg_split('/\s+/', trim($this->name)) ?: [])
            ->filter()
            ->take(2);

        return $segments
            ->map(fn ($segment) => Str::upper(Str::substr($segment, 0, 1)))
            ->implode('');
    }

    public function firstName(): string
    {
        return Str::of(trim($this->name))
            ->before(' ')
            ->value();
    }

    public function dashboardWelcomeMessage(): string
    {
        return match ($this->primaryRoleName()) {
            'administrador' => 'Tienes visibilidad completa del sistema para supervisar configuracion, usuarios y operaciones del dia.',
            'gerente' => 'Revisa el rendimiento del dia, el estado de caja y los indicadores clave antes de tomar decisiones.',
            'vendedor' => 'Consulta tus ventas, el estado de caja y los productos clave para empezar la atencion con claridad.',
            default => 'Consulta tu panel y continua con las tareas mas importantes de la jornada.',
        };
    }

    public function adminlte_desc(): string
    {
        return $this->roleLabel();
    }

    public function adminlte_image(): string
    {
        return $this->profile_photo_url;
    }
}
