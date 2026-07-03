<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateAiContentJob;
use App\Models\AiContentGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostAiGenerationController extends Controller
{
    /**
     * Recibe el brief del Paso 1 y encola la generación de contenido con IA.
     */
    public function generate(Request $request)
    {
        $brief = $request->validate([
            'description' => ['required', 'string', 'min:10', 'max:5000'],
            'tone' => ['required', 'string', 'max:100'],
            'objective' => ['required', 'string', 'max:100'],
            'audience' => ['required', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:post_categories,id'],
        ]);

        $generation = AiContentGeneration::create([
            'user_id' => Auth::id(),
            'status' => 'pending',
            'brief' => $brief,
        ]);

        GenerateAiContentJob::dispatch($generation);

        return response()->json([
            'id' => $generation->id,
            'status' => $generation->status,
        ], 202);
    }

    /**
     * Consulta el estado de una generación de contenido (usado para polling desde el frontend).
     */
    public function status(AiContentGeneration $generation)
    {
        abort_if($generation->user_id !== Auth::id(), 403);

        return response()->json([
            'status' => $generation->status,
            'data' => $generation->status === 'completed' ? $generation->response : null,
            'error' => $generation->status === 'failed' ? $generation->error_message : null,
        ]);
    }
}
