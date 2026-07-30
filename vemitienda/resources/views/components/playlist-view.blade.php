@props([
    'playlist',
    'availableMetronomes' => collect(),
    'editable' => true,
    'reorderUrl',
    'backUrl' => null,
])

<div class="max-w-[1200px] mx-auto">

    {{-- Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            @if ($backUrl)
                <a href="{{ $backUrl }}"
                    class="inline-flex items-center gap-1 text-primary text-label-bold uppercase tracking-wide hover:underline mb-2">
                    <span class="material-symbols-outlined text-base">arrow_back</span>
                    Volver
                </a>
            @endif
            <h1 class="text-display-title text-on-surface">{{ $playlist->name }}</h1>
            @if ($playlist->description)
                <p class="text-on-surface-variant text-body-md mt-1">{{ $playlist->description }}</p>
            @endif
        </div>
        @if ($editable)
            <a href="{{ route('playlists.edit', $playlist->id) }}"
                class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-lg text-button-text transition-colors self-start md:self-auto">
                <span class="material-symbols-outlined text-lg">edit</span>
                Editar playlist
            </a>
        @endif
    </div>

    {{-- Búsqueda y orden (sticky bajo el navbar) --}}
    <div class="sticky top-16 z-30 bg-[#f8f9fb]/90 backdrop-blur-sm py-3 -mx-1 px-1 mb-4">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                <input type="text" id="playlist-search" placeholder="Buscar por canción o artista..."
                    class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary transition-all"
                    style="outline:none">
            </div>
            <div class="flex gap-2">
                <button type="button" id="sort-name-btn" onclick="sortPlaylistRows('title', this)"
                    class="flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-label-bold text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-lg">sort_by_alpha</span>
                    Nombre
                </button>
                <button type="button" id="sort-bpm-btn" onclick="sortPlaylistRows('bpm', this)"
                    class="flex items-center gap-2 px-4 py-2 bg-surface-container-lowest border border-outline-variant rounded-lg text-label-bold text-on-surface-variant hover:bg-surface-container-high transition-colors">
                    <span class="material-symbols-outlined text-lg">speed</span>
                    BPM
                </button>
            </div>
        </div>
    </div>

    @if ($playlist->metronomes->count() > 1)
        <p class="text-on-surface-variant text-label-sm mb-3 flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">drag_indicator</span>
            Arrastrá las canciones desde el ícono para reordenar la playlist.
        </p>
    @endif

    {{-- Listado de canciones --}}
    <div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
        <div class="hidden md:grid grid-cols-12 px-4 py-3 bg-surface-container text-on-surface-variant text-label-bold uppercase tracking-wide">
            <div class="col-span-6">Canción / Artista</div>
            <div class="col-span-2 text-center">BPM</div>
            <div class="col-span-2 text-center">Reproducir</div>
            <div class="col-span-2"></div>
        </div>

        <div id="playlist-songs-body" class="divide-y divide-outline-variant">
            @forelse ($playlist->metronomes as $metronome)
                <div id="metronome-row-{{ $metronome->id }}" data-metronome-id="{{ $metronome->id }}"
                    data-title="{{ strtolower($metronome->title) }}" data-artist="{{ strtolower($metronome->artist ?? '') }}" data-bpm="{{ $metronome->bpm ?? 0 }}"
                    class="playlist-row grid grid-cols-12 items-center gap-2 px-4 py-2.5 odd:bg-surface-container-lowest even:bg-surface-container-low hover:bg-surface-container transition-colors group">

                    <div class="col-span-7 md:col-span-6 flex items-center gap-3 min-w-0">
                        <span class="drag-handle material-symbols-outlined text-outline cursor-move shrink-0" title="Arrastrar para reordenar">drag_indicator</span>
                        <div class="w-9 h-9 rounded-lg bg-primary-fixed flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-lg">music_note</span>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-body-md font-semibold text-on-surface truncate leading-tight">{{ $metronome->title }}</h4>
                            @if ($metronome->artist)
                                <p class="text-on-surface-variant text-label-sm truncate">{{ $metronome->artist }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="col-span-2 text-center">
                        @if ($metronome->has_metronome)
                            <span class="md:hidden text-label-sm text-outline mr-1">BPM</span>
                            <span class="font-mono font-semibold text-on-surface tabular-nums">{{ $metronome->bpm }}</span>
                        @else
                            <span class="inline-block px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-label-sm whitespace-nowrap">Sin metrónomo</span>
                        @endif
                    </div>

                    <div class="col-span-1 md:col-span-2 flex justify-center">
                        @if ($metronome->has_metronome)
                            @php
                                $metronomeData = ['id' => $metronome->id, 'title' => $metronome->title, 'artist' => $metronome->artist, 'bpm' => $metronome->bpm];
                            @endphp
                            <button type="button" title="Reproducir"
                                onclick='MetronomePlayer.load(@json($metronomeData)); MetronomePlayer.play();'
                                class="w-9 h-9 flex items-center justify-center rounded-full bg-primary text-on-primary hover:opacity-90 transition-opacity" style="border:none">
                                <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1">play_arrow</span>
                            </button>
                        @else
                            <span class="text-outline">—</span>
                        @endif
                    </div>

                    <div class="col-span-2 flex justify-end">
                        @if ($editable)
                            <form action="{{ route('playlists.metronomos.detach', ['playlist' => $playlist->id, 'metronome' => $metronome->id]) }}" method="POST"
                                onsubmit="return confirm('¿Quitar esta canción de la playlist?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Quitar de la playlist"
                                    class="w-8 h-8 flex items-center justify-center rounded-full text-error hover:bg-error-container transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100" style="border:none;background:transparent">
                                    <span class="material-symbols-outlined text-lg">close</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-on-surface-variant">
                    Esta playlist todavía no tiene canciones
                </div>
            @endforelse
        </div>

        <div id="playlist-no-results" class="hidden p-8 text-center text-on-surface-variant">
            No se encontraron canciones que coincidan con la búsqueda.
        </div>

        {{-- Agregar canción --}}
        @if ($editable)
            <div class="p-4 md:p-6 bg-surface-container-low border-t border-outline-variant">
                @if ($availableMetronomes->isEmpty())
                    <div class="flex flex-col items-center gap-2 text-center max-w-sm mx-auto">
                        <span class="material-symbols-outlined text-outline text-3xl">info</span>
                        <p class="text-on-surface-variant text-body-md">No tienes más metrónomos disponibles para agregar.</p>
                        <a href="{{ route('metronomos.create') }}" class="text-primary font-semibold hover:underline">Crea uno nuevo.</a>
                    </div>
                @else
                    <form action="{{ route('playlists.metronomos.attach', ['playlist' => $playlist->id, 'metronome' => '__ID__']) }}"
                        method="POST" id="attach-metronome-form"
                        class="flex flex-col sm:flex-row gap-2"
                        onsubmit="this.action = this.action.replace('__ID__', document.getElementById('attach-metronome-select').value); return document.getElementById('attach-metronome-select').value !== '';">
                        @csrf
                        <select id="attach-metronome-select"
                            class="flex-1 border border-outline-variant rounded-lg text-body-md bg-surface-container-lowest"
                            style="padding:10px 14px;outline:none">
                            @foreach ($availableMetronomes as $metronome)
                                <option value="{{ $metronome->id }}">{{ $metronome->title }} ({{ $metronome->has_metronome ? $metronome->bpm . ' BPM' : 'sin metrónomo' }})</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 bg-slate-900 hover:bg-slate-800 text-white px-4 py-2.5 rounded-lg text-button-text transition-colors" style="border:none">
                            <span class="material-symbols-outlined text-lg">add_circle</span>
                            Agregar a la playlist
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>

<x-metronome-player :offset-sidebar="$editable" />

<style>
    .playlist-row-placeholder {
        background: rgba(0, 91, 191, 0.08);
        border: 1px dashed #94a3b8;
        border-radius: 0.5rem;
    }
</style>
