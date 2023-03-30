<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ @$company->slug }}</title>
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>

<body>
    <div class="bg-white">
        <x-encabezadoTienda :company="@$company" />



        <div class="px-4 text-sm scrolling-auto  lg:px-6">
            <div class="mt-3 mb-4 lg:px-2">
                <h2 class="uppercase text-sm font-medium ">
                    Resumen del Pedido
                </h2>
            </div>
            @foreach ($cartItems as $item)
                <x-cardCart :product="$item" :company="@$company" />
            @endforeach

            <div class="my-6 block text-center">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $slug }}" name="slug">

                    <button
                        class="flex flex-row justify-center items-center m-auto pb-1 border-b-2 border-dashed border-gray-300 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            class="h-4 w-4 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        <span class="ml-1 font-normal text-sm text-gray-400">
                            Vaciar Carrito
                        </span>
                    </button>
                </form>
            </div>

            <div
                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b text-{{ $company->theme->name }}">
                Total a pagar: $<span id="total">{{ Cart::getTotal() }}</span>
            </div>
            <div class="flex items-center p-2 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <a href="{{ url($slug) }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 ">
                    Seguir comprando
                </a>
                @if (Cart::getTotalQuantity() > 0)
                    <button data-modal-hide="defaultModal" type="button"
                        class="close-modal text-white bg-{{ $company->theme->name }} focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Finalizar pedido
                    </button>
                @endif
            </div>
        </div>



        {{-- Carrito viejo --}}
        {{-- <main class="my-8">
            <div class="container px-6 mx-auto">
                <div class="flex justify-center my-6">
                    <div
                        class="flex flex-col w-full p-8 text-gray-800 bg-white shadow-lg pin-r pin-y md:w-4/5 lg:w-4/5">
                        @if ($message = Session::get('success'))
                            <div class="p-4 mb-3 bg-green-400 rounded">
                                <p class="text-green-800">{{ $message }}</p>
                            </div>
                        @endif
                        <h3 class="text-3xl text-bold">Cart List</h3>
                        <div class="flex-1">
                            <table class="w-full text-sm lg:text-base" cellspacing="0">
                                <thead>
                                    <tr class="h-12 uppercase">
                                        <th class="hidden md:table-cell"></th>
                                        <th class="text-left">Name</th>
                                        <th class="pl-5 text-left lg:text-right lg:pl-0">
                                            <span class="lg:hidden" title="Quantity">Qtd</span>
                                            <span class="hidden lg:inline">Quantity</span>
                                        </th>
                                        <th class="hidden text-right md:table-cell"> price</th>
                                        <th class="hidden text-right md:table-cell"> Remove </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($cartItems as $item)
                                        <tr>
                                            <td class="hidden pb-4 md:table-cell">
                                                <a href="#">
                                                    <img src="{{ $item->attributes->image }}" class="w-20 rounded"
                                                        alt="Thumbnail">
                                                </a>
                                            </td>
                                            <td>
                                                <a href="#">
                                                    <p class="mb-2 md:ml-4">{{ $item->name }}</p>

                                                </a>
                                            </td>
                                            <td class="justify-center mt-6 md:justify-end md:flex">
                                                <div class="h-10 w-28">
                                                    <div class="relative flex flex-row w-full h-8">

                                                        <form action="{{ route('cart.update') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="slug"
                                                                value="{{ $slug }}">
                                                            <input type="hidden" name="id"
                                                                value="{{ $item->id }}">
                                                            <input type="number" name="quantity"
                                                                value="{{ $item->quantity }}"
                                                                class="w-6 text-center bg-gray-300" />
                                                            <button type="submit"
                                                                class="px-2 pb-2 ml-2 text-white bg-blue-500">update</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="hidden text-right md:table-cell">
                                                <span class="text-sm font-medium lg:text-base">
                                                    ${{ $item->price }}
                                                </span>
                                            </td>
                                            <td class="hidden text-right md:table-cell">
                                                <form action="{{ route('cart.remove') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" value="{{ $item->id }}" name="id">
                                                    <input type="hidden" value="{{ $slug }}" name="slug">
                                                    <button class="px-4 py-2 text-white bg-red-600">x</button>
                                                </form>

                                            </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                            <div>
                                Total: ${{ Cart::getTotal() }}
                            </div>
                            <div>
                                <form action="{{ route('cart.clear') }}" method="POST">
                                    @csrf
                                    <input type="hidden" value="{{ $slug }}" name="slug">
                                    <button class="px-6 py-2 text-red-800 bg-red-300">Remove All Cart</button>
                                </form>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </main> --}}

    </div>
    <script>
        const BASE_URL = "{{ url('remove') }}"

        async function deleteProductCart(id, slug) {
            const resp = await axios.post(BASE_URL, {
                id: id
            })
            location.reload()
        }
    </script>
</body>

</html>
