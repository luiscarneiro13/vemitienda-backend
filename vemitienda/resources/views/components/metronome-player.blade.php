{{--
    Barra flotante del reproductor de metrónomo. Se incluye una sola vez por página con <x-metronome-player />.
    :offset-sidebar="true" (default) deja el hueco de 220px del sidebar admin; pasar false en páginas sin sidebar (ej. vista pública de playlist).
--}}
@props(['offsetSidebar' => true])
<div id="metronome-player-bar" class="hidden fixed bottom-0 left-0 {{ $offsetSidebar ? 'lg:left-[220px]' : '' }} right-0 z-40 bg-surface-container-lowest border-t border-outline-variant shadow-lg">
    <div class="max-w-4xl mx-auto px-4 py-3 flex items-center gap-4 flex-wrap">
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <span id="metronome-beat-dot" class="w-3 h-3 rounded-full bg-outline-variant flex-shrink-0 transition-colors duration-75"></span>
            <div class="min-w-0">
                <p id="metronome-player-title" class="text-label-bold text-on-surface truncate">—</p>
                <p id="metronome-player-artist" class="text-label-sm text-on-surface-variant/70 truncate"></p>
            </div>
        </div>

        <div class="flex items-center gap-1">
            <button type="button" onclick="MetronomePlayer.decreaseBpm()" title="Disminuir BPM"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container-high text-on-surface-variant" style="border:none;background:transparent">
                <span class="material-symbols-outlined text-[18px]">remove</span>
            </button>
            <input type="number" id="metronome-player-bpm" min="20" max="300" value="120"
                class="w-16 text-center border border-outline-variant rounded-lg py-1 text-body-md"
                style="outline:none"
                onchange="MetronomePlayer.setBpmFromInput(this.value)">
            <span class="text-label-sm text-on-surface-variant/70">BPM</span>
            <button type="button" onclick="MetronomePlayer.increaseBpm()" title="Aumentar BPM"
                class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-surface-container-high text-on-surface-variant" style="border:none;background:transparent">
                <span class="material-symbols-outlined text-[18px]">add</span>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" id="metronome-player-toggle" onclick="MetronomePlayer.toggle()" title="Play / Pausa"
                class="w-10 h-10 flex items-center justify-center rounded-full bg-primary text-on-primary hover:opacity-90 transition-opacity" style="border:none">
                <span class="material-symbols-outlined" id="metronome-player-toggle-icon">play_arrow</span>
            </button>
            <button type="button" onclick="MetronomePlayer.stop()" title="Detener"
                class="w-9 h-9 flex items-center justify-center rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container-high" style="background:transparent">
                <span class="material-symbols-outlined text-[18px]">stop</span>
            </button>
            <button type="button" onclick="MetronomePlayer.reset()" title="Reiniciar"
                class="w-9 h-9 flex items-center justify-center rounded-full border border-outline-variant text-on-surface-variant hover:bg-surface-container-high" style="background:transparent">
                <span class="material-symbols-outlined text-[18px]">restart_alt</span>
            </button>
            <button type="button" onclick="MetronomePlayer.close()" title="Cerrar reproductor"
                class="w-9 h-9 flex items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container-high" style="border:none;background:transparent">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
    </div>
</div>

<style>
    /* Fila (tr o div) correspondiente a la canción que está sonando actualmente. */
    tr.metronome-row-playing > td,
    div.metronome-row-playing {
        background-color: rgba(0, 82, 204, 0.12) !important;
        box-shadow: inset 3px 0 0 0 #0052cc;
    }
</style>

<script>
    // Motor del metrónomo con Web Audio API. Sin archivos de audio: el click se genera
    // con un OscillatorNode + envolvente de GainNode. Scheduler basado en el patrón de
    // Chris Wilson ("A Tale of Two Clocks") para mantener el tempo estable aunque el hilo
    // principal se bloquee brevemente.
    (function () {
        class MetronomeEngine {
            constructor() {
                this.audioContext = null;
                this.isRunning = false;
                this.bpm = 120;
                this.nextNoteTime = 0.0;
                this.lookahead = 25.0; // ms entre chequeos del scheduler
                this.scheduleAheadTime = 0.1; // segundos de anticipación al programar clicks
                this.timerID = null;
                this.onBeat = null; // callback opcional para feedback visual
            }

            ensureContext() {
                if (!this.audioContext) {
                    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
                    this.audioContext = new AudioContextClass();
                }
                if (this.audioContext.state === 'suspended') {
                    this.audioContext.resume();
                }
            }

            setBpm(bpm) {
                const parsed = parseInt(bpm, 10);
                this.bpm = Math.min(300, Math.max(20, isNaN(parsed) ? 120 : parsed));
            }

            scheduleClick(time) {
                const osc = this.audioContext.createOscillator();
                const gain = this.audioContext.createGain();
                osc.connect(gain);
                gain.connect(this.audioContext.destination);

                osc.frequency.value = 1000;
                gain.gain.setValueAtTime(0.6, time);
                gain.gain.exponentialRampToValueAtTime(0.001, time + 0.05);

                osc.start(time);
                osc.stop(time + 0.05);

                if (typeof this.onBeat === 'function') {
                    const delayMs = Math.max(0, (time - this.audioContext.currentTime) * 1000);
                    setTimeout(this.onBeat, delayMs);
                }
            }

            scheduler() {
                while (this.nextNoteTime < this.audioContext.currentTime + this.scheduleAheadTime) {
                    this.scheduleClick(this.nextNoteTime);
                    this.nextNoteTime += 60.0 / this.bpm;
                }
                this.timerID = setTimeout(() => this.scheduler(), this.lookahead);
            }

            play() {
                if (this.isRunning) return;
                this.ensureContext();
                this.isRunning = true;
                this.nextNoteTime = this.audioContext.currentTime + 0.05;
                this.scheduler();
            }

            pause() {
                this.isRunning = false;
                clearTimeout(this.timerID);
            }

            stop() {
                this.pause();
                this.nextNoteTime = 0;
            }
        }

        const engine = new MetronomeEngine();
        let current = null;

        const bar = () => document.getElementById('metronome-player-bar');
        const toggleIcon = () => document.getElementById('metronome-player-toggle-icon');
        const bpmInput = () => document.getElementById('metronome-player-bpm');
        const beatDot = () => document.getElementById('metronome-beat-dot');

        engine.onBeat = function () {
            const dot = beatDot();
            if (!dot) return;
            dot.classList.remove('bg-outline-variant');
            dot.classList.add('bg-primary');
            setTimeout(() => {
                dot.classList.remove('bg-primary');
                dot.classList.add('bg-outline-variant');
            }, 90);
        };

        function syncToggleIcon() {
            const icon = toggleIcon();
            if (icon) icon.textContent = engine.isRunning ? 'pause' : 'play_arrow';
        }

        // Resalta con un background la fila (<tr id="metronome-row-{id}">) de la canción
        // que está sonando en este momento, si esa fila existe en la página actual.
        function syncRowHighlight() {
            document.querySelectorAll('.metronome-row-playing').forEach(function (row) {
                row.classList.remove('metronome-row-playing');
            });

            if (engine.isRunning && current) {
                const row = document.getElementById('metronome-row-' + current.id);
                if (row) row.classList.add('metronome-row-playing');
            }
        }

        window.MetronomePlayer = {
            /**
             * Carga una canción en el reproductor y muestra la barra.
             * Si ya había otra canción sonando, detiene el motor para que el próximo
             * play() arranque con el BPM correcto de la nueva canción.
             * song = { id, title, artist, bpm }
             */
            load(song) {
                const isDifferentSong = !current || String(current.id) !== String(song.id);
                if (isDifferentSong && engine.isRunning) {
                    engine.stop();
                }

                current = song;
                engine.setBpm(song.bpm);

                document.getElementById('metronome-player-title').textContent = song.title || '—';
                document.getElementById('metronome-player-artist').textContent = song.artist || '';
                bpmInput().value = engine.bpm;

                bar().classList.remove('hidden');
                document.body.classList.add('pb-20');
            },
            play() {
                if (!current) return;
                engine.play();
                syncToggleIcon();
                syncRowHighlight();
            },
            pause() {
                engine.pause();
                syncToggleIcon();
                syncRowHighlight();
            },
            toggle() {
                if (engine.isRunning) {
                    this.pause();
                } else {
                    this.play();
                }
            },
            stop() {
                engine.stop();
                syncToggleIcon();
                syncRowHighlight();
            },
            reset() {
                engine.stop();
                this.play();
            },
            increaseBpm() {
                this.setBpmFromInput(engine.bpm + 1);
            },
            decreaseBpm() {
                this.setBpmFromInput(engine.bpm - 1);
            },
            setBpmFromInput(value) {
                engine.setBpm(value);
                bpmInput().value = engine.bpm;
            },
            close() {
                engine.stop();
                current = null;
                syncToggleIcon();
                syncRowHighlight();
                bar().classList.add('hidden');
                document.body.classList.remove('pb-20');
            },
        };
    })();
</script>
