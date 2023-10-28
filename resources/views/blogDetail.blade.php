@include('mosto.headerBlog')
<x-metaComponents "title"={{ $post->name }} "description"={{ $post->extract }} "image"={{ asset($post->image->url) }} />
<!--============= Blog Section Starts Here =============-->
<section class="blog-section padding-top padding-bottom">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @include('mosto.components.Post', [
                    'name' => $post->name,
                    'extract' => $post->extract,
                    'url' => url('blog') . '/' . $post->slug,
                    'body' => $post->body,
                    'detail' => true,
                    'image' => $post->image->url,
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
