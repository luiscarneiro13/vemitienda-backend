@extends('layouts.adminlte.index')
@section('content')

<div class="max-w-[1200px] mx-auto">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <span class="text-label-bold text-on-surface-variant uppercase tracking-widest">Música · Herramientas</span>
            <h1 class="text-display-title text-on-surface mt-1">Mis metrónomos</h1>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative flex-grow max-w-xs">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">search</span>
                <input type="text" id="metronome-search" placeholder="Buscar metrónomos..."
                    class="w-full pl-10 pr-3 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md focus:ring-2 focus:ring-primary transition-all"
                    style="outline:none">
            </div>
            <a href="{{ route('metronomos.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-button-text transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-lg">add</span>
                Nuevo metrónomo
            </a>
        </div>
    </div>

    {{-- Stats bar --}}
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-outline-variant">
        <span class="text-label-bold text-primary uppercase">Todos</span>
        <span class="text-label-sm text-outline">
            Mostrando {{ $data['infoData']->firstItem() ?? 0 }}-{{ $data['infoData']->lastItem() ?? 0 }} de {{ $data['infoData']->total() }} registros
        </span>
    </div>

    {{-- Listado --}}
    <div class="space-y-4" id="metronome-items-container">
        @forelse ($data['infoData'] as $metronome)
            @php
                $bpmRange = $metronome->has_metronome ? max(0, min(100, ($metronome->bpm - 20) / (300 - 20) * 100)) : 0;
            @endphp
            <article id="metronome-row-{{ $metronome->id }}" data-title="{{ strtolower($metronome->title) }}" data-artist="{{ strtolower($metronome->artist ?? '') }}"
                class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm group">
                {{-- overflow-hidden va solo acá (no en el <article>): si lo tuviera la tarjeta entera,
                     recortaría el menú de opciones (que es absolute y sobresale del borde inferior). --}}
                <div class="h-1 bg-surface-container-high group-hover:bg-primary/20 transition-colors rounded-t-xl overflow-hidden">
                    <div class="h-full bg-primary" style="width: {{ $bpmRange }}%"></div>
                </div>
                <div class="p-4 md:p-6 flex flex-col md:flex-row items-center gap-6">
                    <div class="w-24 h-24 bg-surface-container rounded-lg flex items-center justify-center border border-outline-variant shrink-0">
                        <span class="material-symbols-outlined text-outline text-4xl">music_note</span>
                    </div>

                    <div class="flex-grow text-center md:text-left min-w-0">
                        <div class="flex items-center justify-center md:justify-start gap-2 mb-1">
                            <span class="px-2 py-0.5 bg-surface-container-high text-label-sm text-on-surface-variant rounded-full">{{ $metronome->created_at->format('d-m-Y') }}</span>
                        </div>
                        <h2 class="text-headline-section text-on-surface truncate">{{ $metronome->title }}</h2>
                        @if ($metronome->artist)
                            <p class="text-on-surface-variant flex items-center justify-center md:justify-start gap-1.5 mt-1 text-body-md">
                                <span class="material-symbols-outlined text-base">person</span>
                                {{ $metronome->artist }}
                            </p>
                        @endif
                    </div>

                    <div class="hidden md:flex flex-col items-center px-8 border-x border-outline-variant shrink-0">
                        <span class="text-label-sm text-outline tracking-widest uppercase">BPM</span>
                        @if ($metronome->has_metronome)
                            <span class="text-3xl font-mono font-light text-on-surface leading-tight tabular-nums">{{ $metronome->bpm }}</span>
                        @else
                            <span class="px-2 py-0.5 mt-1 bg-surface-container-high text-on-surface-variant text-label-sm rounded-full whitespace-nowrap">Sin metrónomo</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 shrink-0">
                        @if ($metronome->has_metronome)
                            @php
                                $metronomeData = ['id' => $metronome->id, 'title' => $metronome->title, 'artist' => $metronome->artist, 'bpm' => $metronome->bpm];
                            @endphp
                            <button type="button"
                                onclick='MetronomePlayer.load(@json($metronomeData)); MetronomePlayer.play();'
                                class="flex items-center justify-center gap-2 bg-primary hover:opacity-90 text-on-primary px-6 py-2.5 rounded-lg text-button-text transition-colors" style="border:none">
                                <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1">play_arrow</span>
                                Reproducir
                            </button>
                        @else
                            <span class="text-outline px-4">—</span>
                        @endif
                        <div class="relative inline-block text-left">
                            <button type="button" class="metronome-options-trigger p-2.5 border border-outline-variant text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-colors" style="cursor:pointer">
                                <span class="material-symbols-outlined">more_vert</span>
                            </button>
                            <div class="metronome-options-menu hidden absolute right-0 mt-2 w-48 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-lg py-2 z-20">
                                <a class="flex items-center gap-3 px-4 py-2 text-body-md text-on-surface hover:bg-surface-container-low transition-colors" href="{{ route('metronomos.edit', $metronome->id) }}">
                                    <span class="material-symbols-outlined text-lg">edit</span> Editar
                                </a>
                                <hr class="my-2 border-outline-variant">
                                <form action="{{ route('metronomos.destroy', $metronome->id) }}" method="POST"
                                    onsubmit="return confirm('¿Eliminar este metrónomo?')">
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
            </article>
        @empty
        @endforelse

        <div id="metronome-no-results" class="hidden border-2 border-dashed border-outline-variant rounded-xl py-16 px-4 text-center">
            <p class="text-on-surface-variant">No se encontraron metrónomos que coincidan con la búsqueda.</p>
        </div>

        {{-- Invitación a crear uno nuevo --}}
        <a href="{{ route('metronomos.create') }}"
            class="border-2 border-dashed border-outline-variant rounded-xl py-16 px-4 flex flex-col items-center justify-center text-center opacity-70 hover:opacity-100 hover:border-primary transition-all group">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-surface-container-high text-on-surface-variant rounded-lg mb-4 group-hover:bg-primary-fixed transition-colors">
                <span class="material-symbols-outlined text-3xl">library_add</span>
            </div>
            <h3 class="text-headline-section text-on-surface-variant">Crea algo nuevo</h3>
            <p class="text-body-md text-outline max-w-xs mx-auto mt-1">Personaliza el tempo de tus canciones y mejora tu técnica musical.</p>
        </a>
    </div>

    @if ($data['infoData']->hasPages())
        <div class="mt-8">
            {{ $data['infoData']->links('pagination::tailwind') }}
        </div>
    @endif
</div>

<x-metronome-player />
@endsection

@section('js')
<script>
    // Menú de opciones (Editar / Eliminar) por fila: cada botón abre solo su propio menú.
    document.querySelectorAll('.metronome-options-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            var menu = trigger.nextElementSibling;
            document.querySelectorAll('.metronome-options-menu').forEach(function (m) {
                if (m !== menu) m.classList.add('hidden');
            });
            menu.classList.toggle('hidden');
        });
    });

    document.addEventListener('click', function () {
        document.querySelectorAll('.metronome-options-menu').forEach(function (m) {
            m.classList.add('hidden');
        });
    });

    document.querySelectorAll('.metronome-options-menu').forEach(function (menu) {
        menu.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    // Búsqueda por título o artista
    var $metronomeRows = document.querySelectorAll('#metronome-items-container > article');
    var metronomeSearch = document.getElementById('metronome-search');
    if (metronomeSearch) {
        metronomeSearch.addEventListener('input', function () {
            var term = this.value.toLowerCase().trim();
            var visibleCount = 0;
            $metronomeRows.forEach(function (row) {
                var matches = row.dataset.title.includes(term) || row.dataset.artist.includes(term);
                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });
            document.getElementById('metronome-no-results').classList.toggle('hidden', term === '' || visibleCount > 0);
        });
    }
</script>
@endsection
