<div class="widget widget-categories">
    <h5 class="title">categorias</h5>
    <ul>
        @forelse ($categories as $category)
            <li>
                <a href="{{ url('/blog?categoria=' . $category->slug) }}">
                    <span>{{ @$category->name }}</span><span>{{ @$category->posts_count }}</span>
                </a>
            </li>
        @empty
        @endforelse
    </ul>
</div>
