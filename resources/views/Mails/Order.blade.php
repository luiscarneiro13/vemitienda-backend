<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ve mi Tienda</title>
</head>

<body>

    <center>
        <img width="200px" src="{{ env('APP_URL') . '/' . $order->company->logo->url }}" alt="">
    </center>

    <h3>Pedido n° {{ $order->id }}</h3>
    <h4>Total a pagar: ${{ $order->total }}</h4>
    <p><strong>Cliente: </strong>{{ $order->name }}</p>
    <p><strong>Email: </strong>{{ $order->email }}</p>
    <p><strong>Teléfono: </strong>{{ $order->phone }}</p>
    <h1>&nbsp;</h1>

    <h3>Productos:</h3>

    <div style="overflow-x:auto">
        <table width="100%">
            @foreach ($order->details as $item)
                <tr style="border: 1px solid black">
                    <td><img width="100px" src="{{ $item->image }}" alt=""></td>
                    <td>
                        <p>{{ $item->name }}</p>
                        <p>
                            Cantidad: {{ $item->quantity }} <br/>
                            $ {{ $item->price }}
                        </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</body>

</html>
