<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostTemplate;
use App\Repositories\PostTemplatesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostTemplatesController extends Controller
{
    /**
     * Lista las plantillas del sistema (user_id null) y las privadas del usuario autenticado.
     */
    public function index()
    {
        $templates = PostTemplatesRepository::getTemplates();

        return response()->json(['templates' => $templates]);
    }

    /**
     * Crea una plantilla privada para el usuario autenticado.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'prompt' => ['required', 'string', 'max:5000'],
            'tone' => ['nullable', 'string', 'max:50'],
            'objective' => ['nullable', 'string', 'max:50'],
            'audience' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'in:primary,secondary,tertiary'],
        ]);

        $template = PostTemplatesRepository::storeTemplate();

        return response()->json(['template' => PostTemplatesRepository::formatTemplate($template)], 201);
    }

    /**
     * Actualiza una plantilla propia del usuario autenticado.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:255'],
            'prompt' => ['required', 'string', 'max:5000'],
            'tone' => ['nullable', 'string', 'max:50'],
            'objective' => ['nullable', 'string', 'max:50'],
            'audience' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'in:primary,secondary,tertiary'],
        ]);

        $template = PostTemplate::findOrFail($id);
        abort_if($template->user_id !== Auth::id(), 403);

        $template = PostTemplatesRepository::updateTemplate($id);

        return response()->json(['template' => PostTemplatesRepository::formatTemplate($template)]);
    }

    /**
     * Elimina una plantilla propia del usuario autenticado.
     */
    public function destroy($id)
    {
        $template = PostTemplate::findOrFail($id);
        abort_if($template->user_id !== Auth::id(), 403);

        PostTemplatesRepository::deleteTemplate($id);

        return response()->json(['success' => true]);
    }
}
