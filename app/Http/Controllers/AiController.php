<?php

namespace App\Http\Controllers;

use App\Services\Ai\ChatHistoryService;
use App\Services\Ai\DeepSeekService;
use App\Services\Ai\EliAgentOrchestrator;
use App\Services\Ai\EliRAGContextProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AiController extends Controller
{
    protected DeepSeekService $deepSeek;
    protected EliAgentOrchestrator $orchestrator;
    protected EliRAGContextProvider $ragProvider;
    protected ChatHistoryService $chatHistory;

    public function __construct(
        DeepSeekService $deepSeek,
        EliAgentOrchestrator $orchestrator,
        EliRAGContextProvider $ragProvider,
        ChatHistoryService $chatHistory
    ) {
        $this->deepSeek = $deepSeek;
        $this->orchestrator = $orchestrator;
        $this->ragProvider = $ragProvider;
        $this->chatHistory = $chatHistory;
    }

    /**
     * Muestra la vista principal del Asistente IA (Centro de control y pruebas).
     */
    public function index()
    {
        return view('Ai.test-ai');
    }

    /**
     * Health check de conectividad con DeepSeek.
     */
    public function ping(): JsonResponse
    {
        $status = $this->deepSeek->ping();
        return response()->json($status);
    }

    /**
     * Obtiene el contexto en tiempo real generado desde la BD para el módulo actual.
     */
    public function contextoActual(Request $request): JsonResponse
    {
        $modulo = $request->input('modulo', 'General');
        $route = $request->input('route');
        $pregunta = $request->input('pregunta');

        $contexto = $this->ragProvider->buildContext($modulo, $route, $pregunta);

        return response()->json([
            'modulo' => $modulo,
            'route' => $route,
            'contexto' => $contexto,
        ]);
    }

    /**
     * Consulta principal: Motor Agéntico Autónomo con SQL Tool Calling en tiempo real.
     */
    public function consultar(Request $request): JsonResponse
    {
        $request->validate([
            'pregunta' => 'required|string',
            'modulo' => 'nullable|string',
            'route' => 'nullable|string',
        ]);

        $pregunta = $request->input('pregunta');
        $modulo = $request->input('modulo', 'General');
        $route = $request->input('route');
        $userId = Auth::id();

        try {
            $result = $this->orchestrator->executeAgent(
                $pregunta,
                $modulo,
                $route,
                null,
                $userId
            );

            // Auditoría y persistencia en chat_histories
            $toolsSummary = !empty($result['toolsUsed'])
                ? '[Tools: ' . count($result['toolsUsed']) . '] ' . implode(' | ', array_map(fn($t) => $t['toolName'] . ' (' . $t['rowCount'] . ' filas en ' . $t['executionTimeMs'] . 'ms)', $result['toolsUsed']))
                : 'Consulta directa';

            $this->chatHistory->recordChat(
                $userId,
                $modulo,
                $pregunta,
                $toolsSummary,
                $result['finalContent'],
                $result['model'],
                $result['promptTokens'],
                $result['completionTokens'],
                $result['totalTokens'],
                $result['durationMs'],
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'respuesta' => $result['finalContent'],
                'tools' => $result['toolsUsed'],
                'totalTokens' => $result['totalTokens'],
                'durationMs' => $result['durationMs'],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Error al procesar la consulta con el Asistente IA: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Consulta con contexto enriquecido (Patrón RAG con datos inyectados).
     */
    public function consultarConContexto(Request $request): JsonResponse
    {
        $request->validate([
            'pregunta' => 'required|string',
            'contexto' => 'nullable|string',
            'modulo' => 'nullable|string',
            'route' => 'nullable|string',
        ]);

        $pregunta = $request->input('pregunta');
        $modulo = $request->input('modulo', 'General');
        $route = $request->input('route');
        $contextoManual = $request->input('contexto');
        $userId = Auth::id();

        try {
            $liveContext = $this->ragProvider->buildContext($modulo, $route, $pregunta);
            $finalContext = !empty($contextoManual) ? "{$contextoManual}\n\n{$liveContext}" : $liveContext;

            $result = $this->deepSeek->consultarConContexto($pregunta, $finalContext);

            $this->chatHistory->recordChat(
                $userId,
                $modulo,
                $pregunta,
                $finalContext,
                $result['content'],
                $result['model'],
                $result['promptTokens'],
                $result['completionTokens'],
                $result['totalTokens'],
                $result['durationMs'],
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'respuesta' => $result['content'],
                'totalTokens' => $result['totalTokens'],
                'durationMs' => $result['durationMs'],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Error al procesar la consulta con contexto: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retorna el historial reciente de consultas del usuario.
     */
    public function historial(): JsonResponse
    {
        $userId = Auth::id();
        $items = $this->chatHistory->getUserRecentHistory($userId, 20);
        return response()->json($items);
    }

    /**
     * Retorna las métricas consolidadas de rendimiento y uso de tokens de la IA.
     */
    public function metricas(): JsonResponse
    {
        $summary = $this->chatHistory->getMetricsSummary();
        return response()->json($summary);
    }
}
