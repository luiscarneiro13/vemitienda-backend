<section>
    <header>
        <h2 class="font-display font-bold text-lg text-[#242034]">
            {{ __('API Tokens') }}
        </h2>

        <p class="mt-1 text-sm text-[#665f74]">
            {{ __('Crea tokens de acceso personal para consumir la API desde aplicaciones externas.') }}
        </p>
    </header>

    @if (session('status') === 'api-token-created' && session('plainTextToken'))
        <div role="status" class="mt-4 rounded-[12px] bg-[#ffbe7b]/20 border border-[#ffbe7b] p-4 text-sm text-[#242034]">
            <p class="font-semibold">{{ __('Copia este token ahora, no volverá a mostrarse:') }}</p>
            <code class="block mt-2 p-2.5 rounded-[8px] bg-white border border-[#e3deec] font-mono text-xs text-[#6135bc] break-all select-all">{{ session('plainTextToken') }}</code>
        </div>
    @endif

    <form method="post" action="{{ route('api-tokens.store') }}" class="mt-6">
        @csrf

        <x-input-label for="token_name" :value="__('Nombre del token')" />
        <div class="mt-1.5 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1">
                <x-text-input id="token_name" name="name" type="text" class="block w-full" required autocomplete="off" />
            </div>

            <x-primary-button class="shrink-0">{{ __('Crear') }}</x-primary-button>
        </div>
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </form>

    <div class="mt-6 space-y-2.5">
        @forelse ($tokens as $token)
            <div class="flex items-center justify-between gap-4 rounded-[12px] border border-[#e3deec] bg-[#faf9fc] px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-[#242034]">{{ $token->name }}</p>
                    <p class="text-xs text-[#665f74] mt-0.5">
                        {{ __('Creado') }}: {{ $token->created_at->diffForHumans() }}
                        @if ($token->last_used_at)
                            · {{ __('Último uso') }}: {{ $token->last_used_at->diffForHumans() }}
                        @endif
                    </p>
                </div>

                <form method="post" action="{{ route('api-tokens.destroy', $token->id) }}"
                      onsubmit="return confirm('{{ __('¿Revocar este token?') }}')">
                    @csrf
                    @method('delete')
                    <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-3 rounded-[8px] text-sm font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2 transition-colors">
                        {{ __('Revocar') }}
                    </button>
                </form>
            </div>
        @empty
            <p class="text-sm text-[#665f74]">{{ __('Todavía no tienes tokens de API.') }}</p>
        @endforelse
    </div>
</section>
