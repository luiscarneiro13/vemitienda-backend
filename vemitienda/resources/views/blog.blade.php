@include('mosto.headerBlog')

<!--============= Blog Section Starts Here =============-->
<section class="blog-section padding-top padding-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @include('mosto.components.Blog.Widget.Pagination', [
                    'data' => $datos,
                ])

                @forelse ($datos as $data)
                    @include('mosto.components.Post', [
                        'name' => $data->name,
                        'extract' => $data->extract,
                        'url' => url('blog') . '/' . $data->slug,
                    ])
                @empty
                @endforelse

                @include('mosto.components.Blog.Widget.Pagination', [
                    'data' => $datos,
                ])

            </div>
            <div class="col-lg-4 col-md-8 col-sm-10">
                <aside class="sticky-menu">
                    {{-- @include('mosto.components.Blog.Widget.Search') --}}
                    @include('mosto.components.Blog.Widget.LatestPosts', ['latests' => $latests])
                    {{-- @include('mosto.components.Blog.Widget.FollowUs') --}}
                    @include('mosto.components.Blog.Widget.Categories', ['categories' => $categories])
                    @include('mosto.components.Blog.Widget.Tags', ['tags' => $tags])
                </aside>

            </div>
        </div>
    </div>
</section>
<!--============= Blog Section Ends Here =============-->

@include('mosto.footerBlog')
