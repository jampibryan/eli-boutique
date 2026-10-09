<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class EliSqlExecutor
{
    protected SqlReadonlyValidator $validator;

    public function __construct(SqlReadonlyValidator $validator)
    {
        $this->validator = $validator;
    }

    protected function safeLog(string $message, string $level = 'info'): void
    {
        try {
            Log::$level($message);
        } catch (Throwable $e) {
            // Silencioso
        }
    }

    /**
     * Ejecuta una consulta analítica de solo lectura con timeout y límites de seguridad.
     */
    public function executeReadonlyQuery(string $rawSql, int $maxRows = 50): array
    {
        $startTime = microtime(true);

        // 1. Validación de seguridad
        $validation = $this->validator->validateAndSanitize($rawSql, $maxRows);
        if (!$validation['isValid'] || empty($validation['sanitizedSql'])) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            return [
                'success' => false,
                'errorMessage' => $validation['errorMessage'] ?? 'Consulta no válida.',
                'sanitizedQuery' => null,
                'rowCount' => 0,
                'dataJson' => '[]',
                'executionTimeMs' => $durationMs,
            ];
        }

        $queryToRun = $validation['sanitizedSql'];

        try {
            // 2. Ejecución con DB Facade
            $results = DB::select($queryToRun);

            // Conversión de objetos stdClass a arrays asociativos con fechas formateadas
            $rows = [];
            foreach ($results as $item) {
                $row = (array) $item;
                foreach ($row as $k => $v) {
                    if ($v instanceof \DateTimeInterface) {
                        $row[$k] = $v->format('Y-m-d H:i:s');
                    }
                }
                $rows[] = $row;
                if (count($rows) >= $maxRows) {
                    break;
                }
            }

            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            $this->safeLog("EliSqlExecutor ejecutó query con éxito en {$durationMs}ms. Filas retornadas: " . count($rows));

            return [
                'success' => true,
                'errorMessage' => null,
                'sanitizedQuery' => $queryToRun,
                'rowCount' => count($rows),
                'dataJson' => json_encode($rows, JSON_UNESCAPED_UNICODE),
                'executionTimeMs' => $durationMs,
            ];
        } catch (Throwable $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $this->safeLog("Error SQL al ejecutar consulta: {$queryToRun}. Mensaje: " . $e->getMessage(), 'warning');

            return [
                'success' => false,
                'errorMessage' => 'Error de base de datos: ' . $e->getMessage(),
                'sanitizedQuery' => $queryToRun,
                'rowCount' => 0,
                'dataJson' => '[]',
                'executionTimeMs' => $durationMs,
            ];
        }
    }
}
