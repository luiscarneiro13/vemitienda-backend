<?php

namespace App\Contracts;

interface AiContentGenerator
{
    /**
     * Genera contenido estructurado (title, content, hashtags, cta, variants) a partir de un payload.
     */
    public function generateContent(array $payload): array;
}
