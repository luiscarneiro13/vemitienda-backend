<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ @$company->slug }}</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <div class="bg-white">

        <x-encabezadoTienda :company="@$company" />

        <div class="sticky top-0 z-30"
            style="box-shadow: rgba(0, 0, 0, 0.1) 0px 5px 14px -10px, rgba(0, 0, 0, 0) 0px 4px 6px -2px;">
            <div class="flex flex-col lg:flex-row pl-2 bg-white z-30 pb-3 pt-4 w-full relative">
                <div class="flex flex-no-wrap w-full max-w-full menu lg:pb-4 overflow-auto text-gray-700">

                    @php
                        $cat = @request()->query()['cat'];
                        $active = true;
                    @endphp

                    <x-iconCart :slug="$slug" />

                    @if (!$cat)
                        <x-itemMenu title="Todo" url="{{ @$slug . '?cat=0' }}" :active="true"
                            theme="{{ @$company->theme->name }}" />
                    @else
                        <x-itemMenu title="Todo" url="{{ @$slug . '?cat=0' }}" :active="false" />
                    @endif

                    @forelse (@$categories as $category)
                        @php
                            if ($cat && $category->id == $cat) {
                                $active = true;
                            } else {
                                $active = false;
                            }
                        @endphp
                        <x-itemMenu title="{{ $category->name }}" url="{{ @$slug . '?cat=' . $category->id }}"
                            theme="{{ @$company->theme->name }}" :active="$active" />
                    @empty
                    @endforelse
                </div>
            </div>
        </div>



        <main class="my-8">
            <style type="text/css">
                .ajax-load {
                    background: #fff;
                    padding: 10px 0px;
                    width: 100%;
                }
            </style>

            <div class="container px-6 mx-auto">
                <div id="post-data" class="grid grid-cols-2 gap-6 mt-6 sm:grid-cols-2 lg:grid-cols-6 xl:grid-cols-6">
                    @include('V2.data')
                </div>
                {{-- <div>
                    <button onclick="loadMore()"
                        class="mt-5 bg-gray-100 lg:px-6 rounded inline-flex py-2 w-full justify-center items-center focus:outline-none shadow-xs transition duration-500 ease-in-out">
                        <span class="text-sm font-semibold text-black ">
                            Clic para ver más
                        </span>
                    </button>
                </div> --}}
                <div class="ajax-load text-center" style="display:none">
                    <center><img width="50px" src="{{ asset('img/loader.gif') }}" alt=""></center>
                </div>
            </div>


            <script type="text/javascript">
                var page = 1;
                const pages = '{{ $pages }}'
                let loading = false

                $(window).scroll(function() {
                    if (!(pages > 0)) {
                        alert('Suspendida momentáneamente')
                    }
                    if ($(window).scrollTop() + $(window).height() + 100 >= $(document).height()) {
                        if ((page < pages) && !loading) {
                            $('.ajax-load').show();
                            page++;
                            loadMoreData(page);
                        }
                    }
                });

                // function loadMore() {
                //     if (page < pages) {
                //         page++;
                //         loadMoreData(page);
                //     }
                // }

                function loadMoreData(page) {
                    const cat = '{{ $cat }}'
                    const url = '{{ url($slug) }}' + '?cat=' + cat + '&page=' + page

                    $.ajax({
                            url: url,
                            type: "get",
                            beforeSend: function() {
                                loading = true
                            }
                        })
                        .done(function(data) {

                            if (data.html == " ") {
                                $('.ajax-load').html("No more records found");
                                return;
                            }

                            $('.ajax-load').hide();
                            $("#post-data").append(data.html);


                            loading = false

                        })
                        .fail(function(jqXHR, ajaxOptions, thrownError) {
                            alert('server not responding...');
                        });
                }
            </script>
        </main>
    </div>
</body>

</html>
