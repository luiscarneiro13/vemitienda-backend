<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ @$company->slug }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('sweetalert::alert')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .swal2-html-container {
            margin: 0;
        }
        .bg-orange {
            background: #F44C04
        }
    </style>
</head>

<body>
    <div class="bg-white">
        <x-encabezadoTienda :company="@$company" />



        <div class="px-4 text-sm scrolling-auto  lg:px-6">
            @if (Cart::getTotalQuantity() > 0)
                <div class="mt-3 mb-4 lg:px-2">
                    <h2 class="uppercase text-sm font-medium flex justify-center items-center">
                        Resumen del Pedido
                    </h2>
                </div>
            @endif

            <div class="flex justify-center items-center space-x-2">
                <a href="{{ url($slug) }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 ">
                    Seguir comprando
                </a>
                @if (Cart::getTotalQuantity() > 0)
                    <button type="button" onclick="enviarPedido()"
                        class="text-white bg-{{ $company->theme->name }} focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        Enviar pedido
                    </button>
                @endif
            </div>

            @foreach ($cartItems as $item)
                <x-cardCart :product="$item" :company="@$company" />
            @endforeach

            <div class="my-6 block text-center">
                <form action="{{ route('cart.clear') }}" method="POST">
                    @csrf
                    <input type="hidden" value="{{ $slug }}" name="slug">
                    @if (Cart::getTotalQuantity() > 0)
                        <button id="vaciarCarrito"
                            class="flex flex-row justify-center items-center m-auto pb-1 border-b-2 border-dashed border-gray-300 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" class="h-4 w-4 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            <span class="ml-1 font-normal text-sm text-gray-400">
                                Vaciar Carrito
                            </span>
                        </button>
                    @endif
                </form>
            </div>

            <div
                class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b text-{{ $company->theme->name }}">

            </div>

            <div id="flashMessage"
                class="w-full h-10 bg-{{ $company->theme->name }} border-t-2 border-white
            fixed left-0 bottom-0
            flex justify-center items-center
            text-white text-2xl z-10 ">
                &nbsp;
                Total a pagar: $<span id="total">{{ Cart::getTotal() }}</span>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        const BASE_URL = "{{ url('') }}"
        const SLUG = "{{ $slug }}"
        const TOTAL = "{{ Cart::getTotal() }}"
        // las siguientes 2 líneas llevan el ; al final
        const pedido = <?php echo json_encode(Cart::getContent()); ?>;
        let company = <?php echo json_encode($company); ?>;

        async function deleteProductCart(id, slug) {
            const resp = await axios.post(BASE_URL + '/remove', {
                id: id
            })
            location.reload()
        }

        function enviarPedido() {
            let validado = false

            Swal.fire({
                title: 'Realizar el pedido',
                html: `
                <input id="orderName" name="name" type="text" class="swal2-input" placeholder="Nombre y Apellido">
                <input id="orderEmail" name="email" type="text" class="swal2-input" placeholder="Correo electrónico">
                <input id="orderPhone" name="phone" type="text" class="swal2-input" placeholder="Teléfono">
                <h1>&nbsp;</h1>
                `,
                showCancelButton: true,
                cancelButtonText: 'Cerrar',
                confirmButtonColor: company.theme.hexadecimal,
                confirmButtonText: 'Enviar pedido',
                focusConfirm: false,
                preConfirm: () => {
                    const name = Swal.getPopup().querySelector('#orderName').value
                    const email = Swal.getPopup().querySelector('#orderEmail').value
                    const phone = Swal.getPopup().querySelector('#orderPhone').value

                    if (!name) {
                        Swal.showValidationMessage(`Ingrese su nombre y apellido`)
                    }

                    if (!email) {
                        Swal.showValidationMessage(`Ingrese su correo electrónico`)
                    }

                    if (!phone) {
                        Swal.showValidationMessage(`Ingrese su número de teléfono`)
                    }

                    return {
                        name: name,
                        email: email,
                        phone: phone,
                        validado: true
                    }
                }
            }).then(async (result) => {

                if (result.value?.validado && !result.isDismissed) {

                    const params = {
                        name: result.value?.name,
                        email: result.value?.email,
                        phone: result.value?.phone,
                        company_id: company.id,
                        total: TOTAL,
                        cart: pedido
                    }
                    console.log(pedido)

                    const resp = await axios.post(BASE_URL + '/order', params)

                    if (resp.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Pedido enviado',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $("#vaciarCarrito").trigger("click");
                        })
                    }

                }
                // Swal.fire(`
            //         Name: ${result.value?.name}
            //         Email: ${result.value?.email}
            //         Phone: ${result.value?.phone}
            //     `.trim())

                // Aquí se envía todo para el backend
                // Se envían datos del pedido y el carrito



            })

        }
    </script>
</body>

</html>
