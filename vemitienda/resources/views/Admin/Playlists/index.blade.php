@extends('layouts.adminlte.index')
@section('content')

<div class="max-w-[1200px] mx-auto">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <span class="text-label-bold text-on-surface-variant uppercase tracking-widest">Música · Curación</span>
            <h1 class="text-display-title text-on-surface mt-1">Mis playlists</h1>
        </div>
        <a href="{{ route('playlists.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-button-text transition-colors self-start md:self-auto">
            <span class="material-symbols-outlined text-lg">add</span>
            Nueva playlist
        </a>
    </div>

    {{-- Stats bar --}}
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-outline-variant">
        <span class="text-label-bold text-primary uppercase">Todas</span>
        <span class="text-label-sm text-outline">
            Mostrando {{ $data['infoData']->firstItem() ?? 0 }}-{{ $data['infoData']->lastItem() ?? 0 }} de {{ $data['infoData']->total() }} playlists
        </span>
    </div>

    {{-- Listado --}}
    <div class="space-y-4">
        @forelse ($data['infoData'] as $playlist)
            <div class="group bg-surface-container-lowest border border-outline-variant rounded-xl playlist-hover-transition hover:border-primary hover:shadow-lg">
                <div class="flex flex-col md:flex-row items-stretch md:items-center p-4 md:p-6 gap-6">

                    {{-- Carátula (placeholder, no hay imagen de portada en el modelo) --}}
                    <div class="w-full md:w-40 h-40 bg-primary-fixed rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-5xl">music_note</span>
                    </div>

                    {{-- Metadata --}}
                    <div class="flex-grow space-y-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-label-sm px-2 py-0.5 bg-surface-container-high text-on-surface-variant rounded-full">{{ $playlist->created_at->format('d-m-Y') }}</span>
                            <span class="w-1 h-1 bg-outline-variant rounded-full"></span>
                            <span class="text-label-sm text-on-surface-variant">Pública</span>
                        </div>
                        <h2 class="text-headline-section text-on-surface group-hover:text-primary transition-colors truncate">{{ $playlist->name }}</h2>
                        @if ($playlist->description)
                            <p class="text-body-md text-on-surface-variant truncate">{{ $playlist->description }}</p>
                        @endif
                        <div class="flex items-center gap-2 text-body-md text-on-surface-variant">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">music_note</span>
                                {{ $playlist->metronomes_count }} {{ $playlist->metronomes_count == 1 ? 'canción' : 'canciones' }}
                            </span>
                            <span class="w-1 h-1 bg-outline-variant rounded-full"></span>
                            <span>Actualizada {{ $playlist->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Acciones --}}
                    <div class="flex flex-wrap md:flex-nowrap items-center gap-3 pt-4 md:pt-0 border-t md:border-t-0 border-outline-variant">
                        <a href="{{ route('playlists.show', $playlist->id) }}"
                            class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-primary text-on-primary rounded-lg text-button-text hover:opacity-90 transition-all active:scale-95">
                            <span class="material-symbols-outlined text-lg">folder_open</span>
                            Abrir
                        </a>
                        <a href="{{ route('playlist.public.show', $playlist->slug) }}" target="_blank" rel="noopener"
                            class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-surface-container-high text-on-surface rounded-lg text-button-text hover:bg-surface-container-highest transition-all active:scale-95">
                            <span class="material-symbols-outlined text-lg">link</span>
                            Enlace público
                        </a>
                        <div class="relative inline-block text-left">
                            <button type="button" class="playlist-options-trigger p-2.5 bg-surface-container-lowest border border-outline-variant text-on-surface rounded-lg hover:bg-surface-container-low transition-colors active:scale-95 flex items-center justify-center" style="cursor:pointer">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                            <div class="playlist-options-menu hidden absolute right-0 mt-2 w-48 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-lg py-2 z-20">
                                <a class="flex items-center gap-3 px-4 py-2 text-body-md text-on-surface hover:bg-surface-container-low transition-colors" href="{{ route('playlists.edit', $playlist->id) }}">
                                    <span class="material-symbols-outlined text-lg">edit</span> Editar
                                </a>
                                <hr class="my-2 border-outline-variant">
                                <form action="{{ route('playlists.destroy', $playlist->id) }}" method="POST"
                                    onsubmit="return confirm('¿Eliminar esta playlist?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2 text-body-md text-error hover:bg-error-container transition-colors" style="border:none;background:transparent;cursor:pointer">
                                        <span class="material-symbols-outlined text-lg">delete</span> Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
        @endforelse

        {{-- Invitación a crear una nueva --}}
        <a href="{{ route('playlists.create') }}"
            class="border-2 border-dashed border-outline-variant rounded-xl p-12 flex flex-col items-center justify-center text-center opacity-70 hover:opacity-100 hover:border-primary transition-all group">
            <div class="w-16 h-16 rounded-full bg-surface-container-high flex items-center justify-center mb-4 group-hover:bg-primary-fixed transition-colors">
                <span class="material-symbols-outlined text-3xl text-on-surface-variant">library_add</span>
            </div>
            <h3 class="text-headline-section text-on-surface-variant">Crea algo nuevo</h3>
            <p class="text-body-md text-outline max-w-xs mt-1">Organiza tus pistas favoritas y crea una experiencia sonora única.</p>
        </a>
    </div>

    @if ($data['infoData']->hasPages())
        <div class="mt-8">
            {{ $data['infoData']->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<style>
    .playlist-hover-transition {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endsection

@section('js')
<script>
    // Menú de opciones (Editar / Eliminar) por fila: cada botón abre solo su propio menú.
    document.querySelectorAll('.playlist-options-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            var menu = trigger.nextElementSibling;
            document.querySelectorAll('.playlist-options-menu').forEach(function (m) {
                if (m !== menu) m.classList.add('hidden');
            });
            menu.classList.toggle('hidden');
        });
    });

    document.addEventListener('click', function () {
        document.querySelectorAll('.playlist-options-menu').forEach(function (m) {
            m.classList.add('hidden');
        });
    });

    document.querySelectorAll('.playlist-options-menu').forEach(function (menu) {
        menu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });
</script>
@endsection
