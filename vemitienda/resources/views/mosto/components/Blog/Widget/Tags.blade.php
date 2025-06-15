<div class="widget widget-tags">
    <h5 class="title">Etiquetas</h5>
    <ul>
        @forelse ($tags as $tag)
            <li>
                <a href="{{ url('/blog?etiqueta=' . $tag->slug) }}">{{ $tag->name }}</a>
            </li>
        @empty
        @endforelse
    </ul>
</div>
