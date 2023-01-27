<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $company->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <style>
        body {
            font-family: "Roboto", sans-serif;
        }
    </style>
</head>

<body class="container" style="background-color:{{  $company->background_color_catalog }}}">

    <x-menu logo="{{ env('DO_URL_BASE').'/'.$company->logo->thumbnail }}" :categories="@$categories" />

    <div class="row mt-5">
        <div class="col-md-4 text-center">
            <image class="img-fluid" src="{{ env('DO_URL_BASE').'/'.$company->logo->url }}" />
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

    @forelse ($categories as $category)
    <div id="{{ $category->name }}" class="mb-5">&nbsp;</div>
    <div class="row">
        <h3 class="text-center" style="background-color: #f1f1f1">{{ $category->name }}</h3>
        @forelse ($category->products as $product)
        <div class="col-md-4 p-1">
            <div class="text-center">
                <image class="img-fluid" src="{{ env('DO_URL_BASE').'/'.$product->image[0]->url }}" />
            </div>
            <p class="text-center">
                <span style="font-weight: bold; font-size: 14px">{{ $product->name }}</span><br />
                @if ($product->price>0)
                <span style="font-weight: bold; font-size: 16px">${{ $product->price }}</span><br />
                @endif
            </p>
        </div>
        @empty
        <div class="text-center">
            No hay productos para mostrar
        </div>
        @endforelse
    </div>
    @empty
    <div class="text-center">
        Por ahora no se puede mostrar el Catálogo
    </div>
    @endforelse
</body>

</html>
