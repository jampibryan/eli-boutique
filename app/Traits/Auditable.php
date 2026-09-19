<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAuditEvent($model, 'CREATE');
        });

        static::updated(function ($model) {
            self::logAuditEvent($model, 'UPDATE');
        });

        static::deleted(function ($model) {
            self::logAuditEvent($model, 'DELETE');
        });
    }

    protected static function logAuditEvent($model, string $action)
    {
        try {
            $changes = [];
            if ($action === 'UPDATE') {
                $changes = $model->getChanges();
                // Omitir timestamps automáticos en los cambios para evitar ruido
                unset($changes['updated_at']);
                if (empty($changes)) {
                    return;
                }
            } elseif ($action === 'CREATE') {
                $changes = $model->getAttributes();
                unset($changes['password'], $changes['remember_token']);
            } elseif ($action === 'DELETE') {
                $changes = ['id' => $model->getKey()];
            }

            AuditLog::create([
                'user_id'     => Auth::id(),
                'model_type'  => get_class($model),
                'model_id'    => (string) $model->getKey(),
                'action'      => $action,
                'ip_address'  => Request::ip(),
                'user_agent'  => Request::userAgent(),
                'payload'     => $changes,
            ]);
        } catch (\Throwable $e) {
            // Prevenir romper la transacción principal si la auditoría encuentra algún problema no crítico
            logger()->error('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}
