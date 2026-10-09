<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;

class EliAgentOrchestrator
{
    private const MAX_AGENT_STEPS = 4;

    protected DeepSeekService $deepSeek;
    protected EliSqlExecutor $sqlExecutor;

    public function __construct(DeepSeekService $deepSeek, EliSqlExecutor $sqlExecutor)
    {
        $this->deepSeek = $deepSeek;
        $this->sqlExecutor = $sqlExecutor;
    }

    protected function safeLog(string $message, string $level = 'info'): void
    {
        try {
            Log::$level($message);
        } catch (Throwable $e) {
            // Previene que problemas de permisos en logs bloqueen la experiencia del usuario
        }
    }

    /**
     * Ejecuta el ciclo agéntico autónomo (ReAct: Reason + Act + Observe + Conclude).
     */
    public function executeAgent(
        string $question,
        ?string $module = null,
        ?string $route = null,
        ?string $additionalContext = null,
        ?int $userId = null
    ): array {
        $startTime = microtime(true);
        $toolsAudit = [];
        $accumulatedPromptTokens = 0;
        $accumulatedCompletionTokens = 0;
        $lastModel = config('deepseek.model', 'deepseek-chat');

        // 1. Obtener Diccionario Semántico para Eli Boutique
        $systemPrompt = EliSemanticDictionary::getSystemPromptWithDictionary($module);

        // 2. Definición formal de la herramienta analítica SQL
        $tools = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'ejecutar_consulta_analitica_sql',
                    'description' => 'Ejecuta una consulta SQL analítica de solo lectura (SELECT con sintaxis T-SQL / SQL Server) en tiempo real para obtener datos de stock por talla, ventas, compras, caja diaria, clientes o proveedores en Eli Boutique.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'sql_query' => [
                                'type' => 'string',
                                'description' => 'La consulta SQL SELECT válida (con TOP si aplica, JOINs correctos y filtros adecuados) a ejecutar.',
                            ],
                            'explicacion' => [
                                'type' => 'string',
                                'description' => 'Breve explicación del objetivo de esta consulta.',
                            ],
                        ],
                        'required' => ['sql_query'],
                    ],
                ],
            ],
        ];

        // 3. Inicialización del historial de mensajes
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        if (!empty($additionalContext)) {
            $messages[] = [
                'role' => 'system',
                'content' => "### CONTEXTO COMPLEMENTARIO DEL SISTEMA:\n{$additionalContext}",
            ];
        }

        $nowPeru = now()->setTimezone('America/Lima')->format('d/m/Y H:i:s');
        $modName = $module ?? 'Global';
        $userPromptWithMeta = "[Fecha y Hora Actual Perú: {$nowPeru}] [Módulo: {$modName}]\n{$question}";

        $messages[] = [
            'role' => 'user',
            'content' => $userPromptWithMeta,
        ];

        $finalContent = '';

        // 4. Bucle Agéntico ReAct
        for ($step = 1; $step <= self::MAX_AGENT_STEPS; $step++) {
            $this->safeLog("EliAgent paso {$step}/" . self::MAX_AGENT_STEPS . " para usuario {$userId}...");

            $stepResult = $this->deepSeek->consultarConHerramientas(
                $messages,
                $tools,
                0.1,
                2000
            );

            $accumulatedPromptTokens += $stepResult['promptTokens'];
            $accumulatedCompletionTokens += $stepResult['completionTokens'];
            $lastModel = $stepResult['model'] ?? $lastModel;

            $toolCalls = $stepResult['toolCalls'] ?? null;

            // Si el modelo solicita ejecutar herramientas
            if (!empty($toolCalls)) {
                $this->safeLog("EliAgent paso {$step}: DeepSeek solicitó " . count($toolCalls) . " tool calls.");

                // Agregar respuesta del asistente con ToolCalls al historial
                $assistantMsg = [
                    'role' => 'assistant',
                    'content' => $stepResult['content'] ?? null,
                    'tool_calls' => $toolCalls,
                ];
                $messages[] = $assistantMsg;

                // Ejecutar cada herramienta solicitada
                foreach ($toolCalls as $toolCall) {
                    $toolName = $toolCall['function']['name'] ?? '';
                    $rawArgs = $toolCall['function']['arguments'] ?? '{}';
                    $toolId = $toolCall['id'] ?? ('call_' . uniqid());

                    if ($toolName === 'ejecutar_consulta_analitica_sql') {
                        $parsedArgs = json_decode($rawArgs, true) ?? [];
                        $sqlQuery = $parsedArgs['sql_query'] ?? $rawArgs;

                        $this->safeLog("EliAgent ejecutando Tool SQL: {$sqlQuery}");

                        $sqlResult = $this->sqlExecutor->executeReadonlyQuery($sqlQuery, 50);

                        $toolsAudit[] = [
                            'toolName' => $toolName,
                            'queryOrInput' => $sqlResult['sanitizedQuery'] ?? $sqlQuery,
                            'rowCount' => $sqlResult['rowCount'] ?? 0,
                            'success' => $sqlResult['success'] ?? false,
                            'errorMessage' => $sqlResult['errorMessage'] ?? null,
                            'executionTimeMs' => $sqlResult['executionTimeMs'] ?? 0,
                        ];

                        if ($sqlResult['success']) {
                            $toolOutput = "[Resultado SQL ({$sqlResult['rowCount']} filas encontradas)]: \n" . $sqlResult['dataJson'];
                        } else {
                            $toolOutput = "[Error SQL]: " . ($sqlResult['errorMessage'] ?? 'Error desconocido') . ". Corrige la consulta o intenta una alternativa.";
                        }

                        $messages[] = [
                            'role' => 'tool',
                            'tool_call_id' => $toolId,
                            'content' => $toolOutput,
                        ];
                    } else {
                        $messages[] = [
                            'role' => 'tool',
                            'tool_call_id' => $toolId,
                            'content' => "[Error]: Herramienta '{$toolName}' no reconocida.",
                        ];
                    }
                }

                // Continuar bucle para que el modelo interprete los datos devueltos
                continue;
            }

            // Si no hay tool calls, el modelo tiene la respuesta final
            $finalContent = $stepResult['content'] ?? '';
            break;
        }

        $totalDurationMs = (int) round((microtime(true) - $startTime) * 1000);

        // Fallback si concluyó sin texto
        if (empty(trim($finalContent))) {
            $wrapResult = $this->deepSeek->consultarConHerramientas($messages, null, 0.1);
            $finalContent = $wrapResult['content'] ?? 'No se pudo generar respuesta final.';
            $accumulatedPromptTokens += $wrapResult['promptTokens'];
            $accumulatedCompletionTokens += $wrapResult['completionTokens'];
        }

        return [
            'finalContent' => $finalContent,
            'model' => $lastModel,
            'toolsUsed' => $toolsAudit,
            'promptTokens' => $accumulatedPromptTokens,
            'completionTokens' => $accumulatedCompletionTokens,
            'totalTokens' => $accumulatedPromptTokens + $accumulatedCompletionTokens,
            'durationMs' => $totalDurationMs,
        ];
    }
}
