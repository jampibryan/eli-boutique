<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class MobileAuthService
{
    public function login(array $input): array
    {
        $user = User::where('email', $input['email'])->first();
        if (!$user || !Hash::check($input['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => 'Correo o contraseña incorrectos.']);
        }
        if ($user->hasEnabledTwoFactorAuthentication()) {
            if (empty($input['two_factor_code']) || !app(TwoFactorAuthenticationProvider::class)
                ->verify(decrypt($user->two_factor_secret), $input['two_factor_code'])) {
                throw ValidationException::withMessages(['two_factor_code' => 'Ingresa el código de tu autenticador.']);
            }
        }
        $access = app(MobileAccess::class);
        abort_if($access->modules($user) === [], 403, 'Tu cuenta no tiene permisos de consulta.');
        return DB::transaction(function () use ($user, $input, $access) {
            // Una sesión por instalación; nunca revocar tokens de Streamlit u otros dispositivos.
            $name = 'mobile:'.hash('sha256', $input['device_id']);
            $user->tokens()->where('name', $name)->delete();
            $expires = now()->addDays(30);
            $token = $user->createToken($name, ['read', 'mobile:read'], $expires);
            return ['token' => $token->plainTextToken, 'expires_at' => $expires->toIso8601String(),
                'user' => $access->profile($user)];
        });
    }
}
