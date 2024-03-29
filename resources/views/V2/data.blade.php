@foreach ($products as $product)
    @if (@$product->image[0])
        <form action="{{ route('cart.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{{ $product->id }}" name="id">
            <input type="hidden" value="{{ $slug }}" name="slug">
            <input type="hidden" value="{{ $product->name }}" name="name">
            <input type="hidden" value="1" name="quantity">
            <input type="hidden" value="{{ $product->price }}" name="price">
            <input type="hidden" value="{{ env('APP_URL') . '/' . $product->image[0]->url }}" name="image">
            <div
                class="h-80 w-full max-w-sm mx-auto overflow-hidden rounded-md shadow-md relative bg-white rounded-md p-3 sm:p-4 flex flex-col">
                <div class="relative flex items-center fade-in cursor-pointer __background-image-grid justify-center">
                    <img src="{{ env('APP_URL') . '/' . $product->image[0]->url }}" alt="Mango Importado"
                        title="Mango Importado" width="112px" height="112px" class="lazyLoad isLoaded">
                </div>
                <div class="w-full flex flex-col relative justify-start flex-auto">
                    <h2 class="text-xs font-semibold leading-snug  cursor-pointer text-black elipsis-item mt-2">
                        <center>
                            {{ \Illuminate\Support\Str::limit($product->name, 40, $end = '...') }}

                            <br /><br />
                            @if ($product->code)
                                Código: {{ $product->code }}<br>
                            @endif
                            @if ($product->available > 0)
                                <span
                                    class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                    @if ($product->available == 1)
                                        {{ $product->available }} disponible
                                    @else
                                        {{ $product->available }} disponibles
                                    @endif
                                </span>
                            @else
                                <span
                                    class="bg-red-100 text-red-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                    Agotado
                                </span>
                            @endif
                        </center>
                    </h2>
                </div>
                <div class="flex flex-col">
                    @if ($product->available > 0)
                        <div class="flex items-center pt-1 pb-2 justify-center">
                            <div class="__promo flex flex-row items-center">
                                <span class="text-sm lg:text-base font-semibold text-{{ @$company->theme->name }}">
                                    ${{ $product->price }}
                                </span>
                            </div>
                        </div>
                        <div id="button-{{ $product->id }}" class="relative w-full">
                            <div class="flex">
                                <a onclick="addCart({{ $product }},'{{ $slug }}')"
                                    class="lg:px-6 rounded inline-flex py-2 w-full justify-center items-center focus:outline-none shadow-xs transition duration-500 ease-in-out
                    bg-{{ @$company->theme->name }}">
                                    <div class="h-4 w-4 mr-1">
                                        <svg class="w-5 h-5" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="white">
                                            <path
                                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold text-white cursor-pointer">
                                        AGREGAR
                                    </span>
                                </a>
                            </div>
                        </div>
                    @endif
                    <div id="loadingButton-{{ $product->id }}" style="display:none">
                        <div class="text-center">
                            <center><img width="50px" src="{{ asset('img/loader.gif') }}" alt=""></center>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endforeach
