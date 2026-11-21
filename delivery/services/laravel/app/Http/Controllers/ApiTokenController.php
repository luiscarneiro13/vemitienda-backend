<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token = $request->user()->createToken($validated['name']);

        return redirect()->route('profile.edit')
            ->with('status', 'api-token-created')
            ->with('plainTextToken', $token->plainTextToken);
    }

    public function destroy(Request $request, string $tokenId): RedirectResponse
    {
        $request->user()->tokens()->where('id', $tokenId)->delete();

        return redirect()->route('profile.edit')->with('status', 'api-token-deleted');
    }
}
