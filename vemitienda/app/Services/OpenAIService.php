<?php

namespace App\Services;

use App\Contracts\AiContentGenerator;
use App\Exceptions\OpenAIException;
use Illuminate\Support\Facades\Log;
use OpenAI;
use OpenAI\Client;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Exceptions\RateLimitException;
use OpenAI\Exceptions\TransporterException;
use Throwable;

class OpenAIService implements AiContentGenerator
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

    public function __construct()
    {
        $apiKey = config('services.openai.api_key');

        if (empty($apiKey)) {
            throw new OpenAIException('OPENAI_API_KEY no está configurada.');
        }

        $this->model = config('services.openai.model', 'gpt-4o-mini');

        $this->client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withOrganization(config('services.openai.organization'))
            ->make();
    }

    /**
     * Genera contenido estructurado a partir de un payload de entrada.
     *
     * Payload soportado:
     * - prompt (string, requerido): instrucción/brief del usuario
     * - system_prompt (string, opcional): reemplaza el system prompt por defecto
     * - context (array, opcional): datos adicionales para enriquecer el prompt
     * - variants_count (int, opcional): cantidad de variantes a solicitar
     * - model (string, opcional): sobreescribe el modelo configurado
     * - temperature (float, opcional)
     */
    public function generateContent(array $payload): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->buildSystemPrompt($payload)],
            ['role' => 'user', 'content' => $this->buildUserPrompt($payload)],
        ];

        $raw = $this->chat($messages, $payload);

        return $this->parseJsonResponse($raw);
    }

    protected function buildSystemPrompt(array $payload): string
    {
        if (! empty($payload['system_prompt'])) {
            return $payload['system_prompt'];
        }

        return 'Eres un asistente experto en marketing de contenido para tiendas online. '
            .'Responde EXCLUSIVAMENTE con un JSON válido, sin texto adicional, con exactamente esta forma: '
            .'{"title": string, "content": string, "hashtags": string[], "cta": string, "variants": string[]}.';
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

    /**
     * Llama al endpoint de chat completions y devuelve el contenido crudo del modelo.
     */
    protected function chat(array $messages, array $payload = []): string
    {
        try {
            $response = $this->client->chat()->create([
                'model' => $payload['model'] ?? $this->model,
                'messages' => $messages,
                'temperature' => $payload['temperature'] ?? 0.7,
                'response_format' => ['type' => 'json_object'],
            ]);

            return $response->choices[0]->message->content ?? '';
        } catch (RateLimitException $e) {
            Log::error('OpenAI: límite de rate excedido', ['message' => $e->getMessage()]);
            throw new OpenAIException('Se excedió el límite de solicitudes a OpenAI. Intenta nuevamente más tarde.', 0, $e);
        } catch (ErrorException $e) {
            Log::error('OpenAI: error de la API', ['message' => $e->getMessage()]);
            throw new OpenAIException('OpenAI devolvió un error: '.$e->getMessage(), 0, $e);
        } catch (TransporterException $e) {
            Log::error('OpenAI: error de transporte/timeout', ['message' => $e->getMessage()]);
            throw new OpenAIException('No se pudo conectar con OpenAI (timeout o red).', 0, $e);
        } catch (Throwable $e) {
            Log::error('OpenAI: error inesperado', ['message' => $e->getMessage()]);
            throw new OpenAIException('Error inesperado al consultar OpenAI.', 0, $e);
        }
    }

    protected function parseJsonResponse(string $raw): array
    {
        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            Log::error('OpenAI: respuesta no es un JSON válido', ['raw' => $raw]);
            throw new OpenAIException('La respuesta de OpenAI no es un JSON válido.');
        }

        return array_merge(self::RESPONSE_SCHEMA, $decoded);
    }
}
