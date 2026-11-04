<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;

class DeepSeekService
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $model;
    protected float $temperature;
    protected int $maxTokens;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('deepseek.api_key');
        $this->baseUrl = rtrim((string) config('deepseek.base_url', 'https://api.deepseek.com'), '/');
        $this->model = (string) config('deepseek.model', 'deepseek-chat');
        $this->temperature = (float) config('deepseek.temperature', 0.2);
        $this->maxTokens = (int) config('deepseek.max_tokens', 2000);
        $this->timeout = (int) config('deepseek.timeout', 60);
    }

    protected function safeLog(string $message, string $level = 'info'): void
    {
        try {
            Log::$level($message);
        } catch (Throwable $e) {
            // Silencioso para prevenir interrupciones de permisos
        }
    }

    /**
     * Prueba básica de conectividad con la API de DeepSeek.
     */
    public function ping(): array
    {
        $nowLima = now()->setTimezone('America/Lima')->format('Y-m-d H:i:s');

        if (empty($this->apiKey)) {
            return [
                'status' => 'offline',
                'message' => 'API Key de DeepSeek no configurada en .env.',
                'timestamp' => $nowLima,
            ];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->get("{$this->baseUrl}/models");

            if ($response->successful()) {
                return [
                    'status' => 'online',
                    'ok' => true,
                    'message' => 'Servicio DeepSeek activo y conectado.',
                    'timestamp' => $nowLima,
                ];
            }

            return [
                'status' => 'offline',
                'ok' => false,
                'message' => 'DeepSeek respondió con error HTTP ' . $response->status(),
                'timestamp' => $nowLima,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'offline',
                'ok' => false,
                'message' => 'Fallo de conexión: ' . $e->getMessage(),
                'timestamp' => $nowLima,
            ];
        }
    }

    /**
     * Consulta con soporte para mensajes y herramientas (Function / Tool Calling).
     */
    public function consultarConHerramientas(
        array $messages,
        ?array $tools = null,
        ?float $temperature = null,
        ?int $maxTokens = null
    ): array {
        $startTime = microtime(true);

        if (empty($this->apiKey)) {
            throw new Exception("La clave de API de DeepSeek no está configurada. Por favor verifica DEEPSEEK_API_KEY en .env.");
        }

        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => $temperature ?? $this->temperature,
            'max_tokens' => $maxTokens ?? $this->maxTokens,
            'stream' => false,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
            $payload['tool_choice'] = 'auto';
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout($this->timeout)
                ->post("{$this->baseUrl}/chat/completions", $payload);

            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                $errorBody = $response->body();
                $this->safeLog("DeepSeek API error ({$response->status()}): {$errorBody}", 'error');
                throw new Exception("Error en la API de DeepSeek [{$response->status()}]: {$errorBody}");
            }

            $json = $response->json();
            $choice = $json['choices'][0] ?? null;
            $usage = $json['usage'] ?? [];

            $promptTokens = (int) ($usage['prompt_tokens'] ?? 0);
            $completionTokens = (int) ($usage['completion_tokens'] ?? 0);
            $totalTokens = (int) ($usage['total_tokens'] ?? ($promptTokens + completionTokens));

            $content = $choice['message']['content'] ?? '';
            $toolCalls = $choice['message']['tool_calls'] ?? null;
            $finishReason = $choice['finish_reason'] ?? 'stop';

            $this->safeLog("DeepSeek OK: in={$promptTokens}, out={$completionTokens}, total={$totalTokens}, duration={$durationMs}ms");

            return [
                'content' => $content,
                'toolCalls' => $toolCalls,
                'finishReason' => $finishReason,
                'promptTokens' => $promptTokens,
                'completionTokens' => $completionTokens,
                'totalTokens' => $totalTokens,
                'model' => $json['model'] ?? $this->model,
                'durationMs' => $durationMs,
            ];
        } catch (Exception $e) {
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);
            $this->safeLog("Excepción en DeepSeekService: " . $e->getMessage(), 'error');
            throw $e;
        }
    }

    /**
     * Consulta simple texto a texto.
     */
    public function consultar(string $prompt, ?string $systemPrompt = null, ?float $temperature = null): array
    {
        $messages = [];
        if (!empty($systemPrompt)) {
            $messages[] = ['role' => 'system', 'content' => $systemPrompt];
        }
        $messages[] = ['role' => 'user', 'content' => $prompt];

        return $this->consultarConHerramientas($messages, null, $temperature);
    }

    /**
     * Consulta enriquecida con contexto de datos (RAG).
     */
    public function consultarConContexto(string $pregunta, string $contexto, ?string $systemPrompt = null): array
    {
        $prompt = <<<TEXT
### CONTEXTO DEL SISTEMA ERP ELI BOUTIQUE:
{$contexto}

### PREGUNTA DEL USUARIO:
{$pregunta}

### INSTRUCCIONES ESTRICTAS:
1. Responde de forma precisa basándote en el contexto del sistema proporcionado.
2. Si se trata de listas o stocks de prendas, utiliza tablas Markdown formateadas.
3. Si la información no está en el contexto, indícalo con amabilidad y precisión.
4. Mantén un tono elegante y profesional de boutique de modas.
TEXT;

        $sys = $systemPrompt ?? "Eres ELI BOUTIQUE AI, asistente inteligente de la tienda Eli Boutique.";
        return $this->consultar($prompt, $sys, 0.2);
    }
}
