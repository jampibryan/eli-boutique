<?php

namespace App\Services\Ai;

class SqlReadonlyValidator
{
    // Palabras reservadas estrictamente prohibidas
    private const FORBIDDEN_KEYWORDS = [
        'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE',
        'EXEC', 'EXECUTE', 'GRANT', 'REVOKE', 'DENY', 'CREATE',
        'MERGE', 'SHUTDOWN', 'BACKUP', 'RESTORE', 'XP_', 'SP_',
        'DBCC', 'KILL', 'INTO'
    ];

    // Tablas y vistas sensibles estrictamente prohibidas (Prevención de fuga de credenciales)
    private const FORBIDDEN_TABLES = [
        'users', 'sessions', 'password_resets', 'password_reset_tokens',
        'personal_access_tokens', 'failed_jobs', 'chat_histories',
        'audit_logs', 'INFORMATION_SCHEMA', 'sys'
    ];

    /**
     * Valida y sanitiza una consulta SQL para garantizar solo lectura.
     */
    public function validateAndSanitize(string $rawSql, int $maxRows = 50): array
    {
        $rawSql = trim($rawSql);

        if (empty($rawSql)) {
            return [
                'isValid' => false,
                'errorMessage' => 'La consulta SQL está vacía.',
                'sanitizedSql' => null,
            ];
        }

        // Limpieza de bloques markdown tipo ```sql ... ```
        $cleanSql = preg_replace('/^```(?:sql)?\s*/i', '', $rawSql);
        $cleanSql = preg_replace('/\s*```$/', '', $cleanSql);
        $cleanSql = trim($cleanSql);

        // 1. Debe iniciar obligatoriamente con SELECT o WITH (CTE)
        if (!preg_match('/^\s*(SELECT|WITH)\b/i', $cleanSql)) {
            return [
                'isValid' => false,
                'errorMessage' => 'Por seguridad, únicamente se permiten consultas de solo lectura que inicien con SELECT o WITH.',
                'sanitizedSql' => null,
            ];
        }

        // 2. Bloqueo de sentencias DML/DDL destructivas o comandos de sistema
        $forbiddenPattern = '/\b(' . implode('|', self::FORBIDDEN_KEYWORDS) . ')\b/i';
        if (preg_match($forbiddenPattern, $cleanSql, $matches)) {
            return [
                'isValid' => false,
                'errorMessage' => "Consulta bloqueada por seguridad. Se detectó la instrucción no permitida: '" . strtoupper($matches[1]) . "'.",
                'sanitizedSql' => null,
            ];
        }

        // 3. Bloqueo de tablas con credenciales, sesiones o metadatos de sistema
        $forbiddenTablesPattern = '/\b(' . implode('|', self::FORBIDDEN_TABLES) . ')\b/i';
        if (preg_match($forbiddenTablesPattern, $cleanSql, $matches)) {
            return [
                'isValid' => false,
                'errorMessage' => "Consulta bloqueada por seguridad. No está permitido consultar entidades sensibles del sistema ('" . $matches[1] . "').",
                'sanitizedSql' => null,
            ];
        }

        // 4. Bloqueo de inyección por encadenamiento de comandos con punto y coma (;)
        if (preg_match('/;\s*\S+/', $cleanSql)) {
            return [
                'isValid' => false,
                'errorMessage' => 'No se permite la ejecución de múltiples instrucciones SQL encadenadas por punto y coma (;).',
                'sanitizedSql' => null,
            ];
        }

        // Limpieza de punto y coma al final
        $cleanSql = rtrim(rtrim($cleanSql), ';');

        // 5. Inyección segura de límite TOP / LIMIT para proteger rendimiento y memoria
        $driver = config('database.default', 'sqlsrv');

        if ($driver === 'sqlsrv') {
            if (!preg_match('/\b(TOP\s+\d+|OFFSET\s+\d+\s+ROWS)\b/i', $cleanSql) && !preg_match('/^\s*WITH\b/i', $cleanSql)) {
                if (preg_match('/^\s*SELECT\s+DISTINCT\b/i', $cleanSql)) {
                    $cleanSql = preg_replace('/^\s*SELECT\s+DISTINCT\b/i', "SELECT DISTINCT TOP {$maxRows}", $cleanSql, 1);
                } elseif (preg_match('/^\s*SELECT\b/i', $cleanSql)) {
                    $cleanSql = preg_replace('/^\s*SELECT\b/i', "SELECT TOP {$maxRows}", $cleanSql, 1);
                }
            }
        } else {
            // MySQL fallback
            if (!preg_match('/\bLIMIT\s+\d+/i', $cleanSql)) {
                $cleanSql .= " LIMIT {$maxRows}";
            }
        }

        return [
            'isValid' => true,
            'errorMessage' => null,
            'sanitizedSql' => $cleanSql,
        ];
    }
}
