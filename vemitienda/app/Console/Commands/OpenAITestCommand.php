<?php

namespace App\Console\Commands;

use App\Exceptions\OpenAIException;
use App\Services\OpenAIService;
use Illuminate\Console\Command;

class OpenAITestCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'openai:test {--prompt=Genera una publicación promocional para una tienda online de ropa.}';

    /**
     * @var string
     */
    protected $description = 'Verifica la integración con la API de OpenAI generando un contenido de prueba';

    public function handle(OpenAIService $openAIService): int
    {
        $this->info('Consultando OpenAI...');

        try {
            $result = $openAIService->generateContent([
                'prompt' => $this->option('prompt'),
            ]);
        } catch (OpenAIException $e) {
            $this->error('Falló la integración con OpenAI: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('Respuesta recibida:');
        $this->line(json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }
}
