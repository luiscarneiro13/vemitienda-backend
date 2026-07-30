{{--
    Requiere $reorderUrl (string) en scope. Se incluye desde @section('js') de cada
    vista que renderice <x-playlist-view>, para asegurar que jQuery ya esté cargado
    (admin y el layout público cargan jQuery/jQuery UI justo antes de @yield('js')).
--}}
<script>
    // Reordenamiento drag & drop de las canciones de la playlist con jQuery UI Sortable.
    $(function () {
        $('#playlist-songs-body').sortable({
            items: '> .playlist-row',
            handle: '.drag-handle',
            axis: 'y',
            cursor: 'move',
            placeholder: 'playlist-row-placeholder',
            forcePlaceholderSize: true,
            helper: function (e, item) {
                return item.clone().css('width', item.outerWidth());
            },
            update: function () {
                var order = $('#playlist-songs-body .playlist-row').map(function () {
                    return $(this).data('metronome-id');
                }).get();

                $.ajax({
                    url: '{{ $reorderUrl }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order,
                    },
                }).fail(function () {
                    toastr.error('No se pudo guardar el nuevo orden de la playlist');
                });
            },
        });

        // Búsqueda por canción o artista
        var $rows = $('#playlist-songs-body .playlist-row');
        $('#playlist-search').on('input', function () {
            var term = this.value.toLowerCase().trim();
            var visibleCount = 0;
            $rows.each(function () {
                var $row = $(this);
                var matches = $row.data('title').toString().includes(term) || $row.data('artist').toString().includes(term);
                $row.toggle(matches);
                if (matches) visibleCount++;
            });
            $('#playlist-no-results').toggleClass('hidden', term === '' || visibleCount > 0);
        });

        // Orden visual por nombre / BPM (no persiste hasta que se reordene manualmente arrastrando)
        var sortState = {};
        window.sortPlaylistRows = function (key, btn) {
            $('#sort-name-btn, #sort-bpm-btn').removeClass('bg-surface-container-high').addClass('bg-surface-container-lowest');
            $(btn).removeClass('bg-surface-container-lowest').addClass('bg-surface-container-high');

            sortState[key] = sortState[key] === 'asc' ? 'desc' : 'asc';
            var dir = sortState[key] === 'asc' ? 1 : -1;

            var sorted = $rows.get().sort(function (a, b) {
                var av = key === 'bpm' ? parseInt($(a).data('bpm'), 10) : $(a).data('title').toString();
                var bv = key === 'bpm' ? parseInt($(b).data('bpm'), 10) : $(b).data('title').toString();
                if (av < bv) return -1 * dir;
                if (av > bv) return 1 * dir;
                return 0;
            });

            $.each(sorted, function () {
                $('#playlist-songs-body').append(this);
            });
        };
    });
</script>
