<?php

namespace App\Jobs;

use App\Exceptions\ClaudeException;
use App\Exceptions\OpenAIException;
use App\Models\AiContentGeneration;
use App\Models\PostCategory;
use App\Services\ClaudeService;
use App\Services\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateAiContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;

    private AiContentGeneration $generation;

    public function __construct(AiContentGeneration $generation)
    {
        $this->generation = $generation;
    }

    /**
     * Intenta generar el contenido con Claude (proveedor por defecto); si falla,
     * reintenta con OpenAI antes de marcar el registro como failed. OpenAIService
     * se resuelve de forma perezosa (solo si Claude falló) para no exigir
     * OPENAI_API_KEY cuando Claude funciona con normalidad.
     */
    public function handle(ClaudeService $claudeService)
    {
        $payload = $this->buildPayload();

        try {
            $result = $claudeService->generateContent($payload);

            $this->generation->update([
                'status' => 'completed',
                'provider' => 'claude',
                'response' => $result,
            ]);

            return;
        } catch (ClaudeException $e) {
            Log::warning('GenerateAiContentJob: Claude falló, reintentando con OpenAI', ['message' => $e->getMessage()]);
            $claudeError = $e->getMessage();
        }

        try {
            $result = app(OpenAIService::class)->generateContent($payload);

            $this->generation->update([
                'status' => 'completed',
                'provider' => 'openai',
                'response' => $result,
            ]);
        } catch (OpenAIException $e) {
            $this->generation->update([
                'status' => 'failed',
                'error_message' => "Claude: {$claudeError} | OpenAI: {$e->getMessage()}",
            ]);
        } catch (Throwable $e) {
            Log::error('GenerateAiContentJob: error inesperado en fallback a OpenAI', ['message' => $e->getMessage()]);

            $this->generation->update([
                'status' => 'failed',
                'error_message' => "Claude: {$claudeError} | OpenAI: error inesperado al generar el contenido.",
            ]);
        }
    }

    private function buildPayload(): array
    {
        $brief = $this->generation->brief;

        $context = array_filter([
            'tono' => $brief['tone'] ?? null,
            'objetivo' => $brief['objective'] ?? null,
            'audiencia' => $brief['audience'] ?? null,
            'categoria' => $this->resolveCategoryName($brief['category_id'] ?? null),
        ]);

        return [
            'prompt' => $brief['description'],
            'context' => $context,
        ];
    }

    private function resolveCategoryName($categoryId): ?string
    {
        if (empty($categoryId)) {
            return null;
        }

        return optional(PostCategory::find($categoryId))->name;
    }
}
