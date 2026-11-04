<?php

namespace App\Services\Ai;

use App\Models\ChatHistory;
use Illuminate\Support\Facades\Log;
use Throwable;

class ChatHistoryService
{
    /**
     * Registra una interacción conversacional en el historial para auditoría y métricas.
     */
    public function recordChat(
        ?int $userId,
        string $module,
        string $question,
        ?string $context,
        string $response,
        string $model,
        int $promptTokens,
        int $completionTokens,
        int $totalTokens,
        int $responseTimeMs,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): ?ChatHistory {
        try {
            // Estimación de costo en USD según tarifas oficiales DeepSeek-V3
            // Prompt: $0.14 / 1M tokens, Completion: $0.28 / 1M tokens
            $cost = ($promptTokens * 0.00000014) + ($completionTokens * 0.00000028);

            return ChatHistory::create([
                'user_id' => $userId,
                'module' => $module,
                'question' => $question,
                'context' => $context,
                'response' => $response,
                'model' => $model,
                'prompt_tokens' => $promptTokens,
                'completion_tokens' => $completionTokens,
                'total_tokens' => $totalTokens,
                'cost' => $cost,
                'response_time_ms' => $responseTimeMs,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        } catch (Throwable $e) {
            Log::warning("Error al registrar auditoría de chat: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtiene el historial reciente para el usuario actual.
     */
    public function getUserRecentHistory(?int $userId, int $limit = 20)
    {
        if (!$userId) {
            return collect();
        }

        return ChatHistory::where('user_id', $userId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtiene el resumen consolidado de métricas de uso y rendimiento de la IA.
     */
    public function getMetricsSummary(): array
    {
        try {
            $totalConsultas = ChatHistory::count();
            $totalTokens = (int) ChatHistory::sum('total_tokens');
            $costoTotalUSD = (float) ChatHistory::sum('cost');
            $avgDurationMs = (int) round((float) ChatHistory::avg('response_time_ms'));

            $consultasHoy = ChatHistory::whereDate('created_at', today())->count();
            $tokensHoy = (int) ChatHistory::whereDate('created_at', today())->sum('total_tokens');

            return [
                'total_consultas' => $totalConsultas,
                'total_tokens' => $totalTokens,
                'costo_total_usd' => round($costoTotalUSD, 6),
                'avg_duration_ms' => $avgDurationMs,
                'consultas_hoy' => $consultasHoy,
                'tokens_hoy' => $tokensHoy,
            ];
        } catch (Throwable $e) {
            return [
                'total_consultas' => 0,
                'total_tokens' => 0,
                'costo_total_usd' => 0,
                'avg_duration_ms' => 0,
                'consultas_hoy' => 0,
                'tokens_hoy' => 0,
            ];
        }
    }
}
