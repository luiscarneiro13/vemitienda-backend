<?php

namespace App\Services;

use Anthropic\Client;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\RateLimitException;
use App\Contracts\AiContentGenerator;
use App\Exceptions\ClaudeException;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClaudeService implements AiContentGenerator
{
    protected Client $client;

    protected string $model;

    protected const RESPONSE_SCHEMA = [
        'title' => '',
        'content' => '',
        'hashtags' => [],
        'cta' => '',
        'variants' => [],
    ];

    protected const JSON_SCHEMA = [
        'type' => 'object',
        'properties' => [
            'title' => ['type' => 'string'],
            'content' => ['type' => 'string'],
            'hashtags' => ['type' => 'array', 'items' => ['type' => 'string']],
            'cta' => ['type' => 'string'],
            'variants' => ['type' => 'array', 'items' => ['type' => 'string']],
        ],
        'required' => ['title', 'content', 'hashtags', 'cta', 'variants'],
        'additionalProperties' => false,
    ];

    public function __construct()
    {
        $apiKey = config('services.claude.api_key');

        if (empty($apiKey)) {
            throw new ClaudeException('ANTHROPIC_API_KEY no está configurada.');
        }

        $this->model = config('services.claude.model', 'claude-haiku-4-5');
        $this->client = new Client(apiKey: $apiKey);
    }

    /**
     * Genera contenido estructurado a partir de un payload de entrada.
     *
     * Payload soportado (mismo contrato que OpenAIService):
     * - prompt (string, requerido)
     * - system_prompt (string, opcional)
     * - context (array, opcional)
     * - variants_count (int, opcional)
     * - model (string, opcional)
     * - max_tokens (int, opcional)
     */
    public function generateContent(array $payload): array
    {
        $raw = $this->chat($payload);

        return $this->parseJsonResponse($raw);
    }

    protected function buildSystemPrompt(array $payload): string
    {
        if (! empty($payload['system_prompt'])) {
            return $payload['system_prompt'];
        }

        return 'Eres un asistente experto en marketing de contenido para tiendas online. '
            .'Responde siempre en el formato JSON solicitado, sin texto adicional.';
    }

    protected function buildUserPrompt(array $payload): string
    {
        $prompt = $payload['prompt'] ?? '';
        $variantsCount = $payload['variants_count'] ?? 1;

        $lines = [$prompt];

        if (! empty($payload['context'])) {
            $lines[] = 'Contexto adicional: '.json_encode($payload['context'], JSON_UNESCAPED_UNICODE);
        }

        if ($variantsCount > 1) {
            $lines[] = "Genera {$variantsCount} variantes de contenido en el campo \"variants\".";
        }

        return implode("\n", array_filter($lines));
    }

    protected function chat(array $payload): string
    {
        try {
            $message = $this->client->messages->create(
                maxTokens: $payload['max_tokens'] ?? 1024,
                model: $payload['model'] ?? $this->model,
                system: $this->buildSystemPrompt($payload),
                messages: [
                    ['role' => 'user', 'content' => $this->buildUserPrompt($payload)],
                ],
                outputConfig: [
                    'format' => [
                        'type' => 'json_schema',
                        'schema' => self::JSON_SCHEMA,
                    ],
                ],
            );

            foreach ($message->content as $block) {
                if ($block->type === 'text') {
                    return $block->text;
                }
            }

            throw new ClaudeException('Claude no devolvió contenido de tipo texto.');
        } catch (RateLimitException $e) {
            Log::error('Claude: límite de rate excedido', ['message' => $e->getMessage()]);
            throw new ClaudeException('Se excedió el límite de solicitudes a Claude. Intenta nuevamente más tarde.', 0, $e);
        } catch (APIStatusException $e) {
            Log::error('Claude: error de la API', ['message' => $e->getMessage()]);
            throw new ClaudeException('Claude devolvió un error: '.$e->getMessage(), 0, $e);
        } catch (ClaudeException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Claude: error inesperado', ['message' => $e->getMessage()]);
            throw new ClaudeException('Error inesperado al consultar Claude.', 0, $e);
        }
    }

    protected function parseJsonResponse(string $raw): array
    {
        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            Log::error('Claude: respuesta no es un JSON válido', ['raw' => $raw]);
            throw new ClaudeException('La respuesta de Claude no es un JSON válido.');
        }

        return array_merge(self::RESPONSE_SCHEMA, $decoded);
    }
}
