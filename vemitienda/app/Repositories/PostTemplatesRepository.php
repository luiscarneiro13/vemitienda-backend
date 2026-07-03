<?php

namespace App\Repositories;

use App\Models\PostTemplate;
use Illuminate\Support\Facades\Auth;

class PostTemplatesRepository
{
    static function getTemplates()
    {
        $templates = PostTemplate::where(function ($q) {
            $q->whereNull('user_id')->orWhere('user_id', Auth::id());
        })->orderByDesc('id')->get();

        return $templates->map(function ($template) {
            return self::formatTemplate($template);
        });
    }

    static function storeTemplate()
    {
        $template = PostTemplate::create([
            'user_id' => Auth::id(),
            'title' => request()->title,
            'description' => request()->description,
            'prompt' => request()->prompt,
            'tone' => request()->tone,
            'objective' => request()->objective,
            'audience' => request()->audience,
            'color' => request()->color ?? 'primary',
        ]);

        return $template;
    }

    static function updateTemplate($id)
    {
        $template = PostTemplate::findOrFail($id);
        abort_if($template->user_id !== Auth::id(), 403);

        $template->title = request()->title;
        $template->description = request()->description;
        $template->prompt = request()->prompt;
        $template->tone = request()->tone;
        $template->objective = request()->objective;
        $template->audience = request()->audience;
        $template->color = request()->color ?? 'primary';
        $template->save();

        return $template;
    }

    static function deleteTemplate($id)
    {
        $template = PostTemplate::findOrFail($id);
        abort_if($template->user_id !== Auth::id(), 403);

        return $template->delete();
    }

    static function formatTemplate($template)
    {
        return [
            'id' => $template->id,
            'title' => $template->title,
            'description' => $template->description,
            'prompt' => $template->prompt,
            'tone' => $template->tone,
            'objective' => $template->objective,
            'audience' => $template->audience,
            'color' => $template->color,
            'editable' => $template->user_id === Auth::id(),
        ];
    }
}
