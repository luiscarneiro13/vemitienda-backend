<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $company->name }}</title>
    <meta property="og:title" content="{{ $company->name }}" />
    <meta name="description" content="{{ $company->slogan }}" />
    <meta property="og:description" content="{{ $company->slogan }}" />
    <meta property="og:image" content="{{ $company->logo ? env('DO_URL_BASE').'/'.$company->logo->thumbnail:'' }}" />
    <meta property="og:type" content="article" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css"
        rel="stylesheet">


    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>
    <style>
        body {
            font-family: "Roboto", sans-serif;
        }
    </style>
    <script type="text/javascript">
        // $('ul.pagination').hide();
        $(function() {
            $('.scrolling-pagination').jscroll({
                autoTrigger: true,
                padding: 0,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.scrolling-pagination',
                callback: function() {
                    $('ul.pagination').remove();
                },
                loadingHtml:'<center><small>Cargando...</small></center>'
            });
        });
    </script>
</head>

@if($company)

<body style="background-color:{{  $company->background_color_catalog }}}">
    <x-menu logo="{{ $company->logo? env('DO_URL_BASE').'/'.$company->logo->thumbnail:'' }}" :categories="@$categories"
        idEncriptado='{{ $id_encriptado }}' />


    <div class="row mt-5">
        <div class="col-md-4 text-center">
            <image class="img-fluid p-5" src="{{$company->logo? env('DO_URL_BASE').'/'.$company->logo->url:'' }}" />
        </div>
        <div class="col-md-8">
            <div class="text-center" style="font-size: 52px; font-weight: bolder; margin-top: 65px">
                {{ $company->name }}
            </div>
            <div class="text-center" style="font-size: 32px">
                {{ $company->slogan }}
            </div>
        </div>
    </div>

    <div class="scrolling-pagination pb-5 mb-4">
        @foreach ($products as $product)
        <div class="col-md-4 p-1">
            <div class="text-center">
                <image class="img-fluid"
                    src="{{ count(@$product->image)>0 ? env('DO_URL_BASE').'/'.$product->image[0]->url:'' }}" />
            </div>
            <p class="text-center">
                <span style="font-weight: bold; font-size: 14px">{{ $product->name }}</span><br />
                @if ($product->price>0)
                <span style="font-weight: bold; font-size: 16px">${{ $product->price }}</span><br />
                @endif
            </p>
        </div>
        @endforeach
        <div class="d-none">
            {{ $products->links() }}
        </div>
    </div>

</body>
@else

<body>
    <div class="text-center">
        <h1>Falta información de la Tienda</h1>
    </div>
</body>

@endif

</html>
