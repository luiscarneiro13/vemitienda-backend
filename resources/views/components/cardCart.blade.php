<div class="flex justify-between py-1 border-b border-gray-100 relative w-full">
    <span class="text-gray-600 text-xs ml-3 underline cursor-pointer absolute left-0" style="top: 12px;">
        <div class="h-6 w-6 cursor-pointer">

            <input type="hidden" value="{{ $product->id }}" name="id">
            <input type="hidden" value="{{ $company->slug }}" name="slug">
            <a href="#" onclick="deleteProductCart('{{ $product->id }}','{{ $company->slug }}')">
                <svg class="mt-3" version="1.1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1000 1000"
                    enable-background="new 0 0 1000 1000" xml:space="preserve">
                    <g>
                        <path
                            d="M872.4,127.6h-196V88.4c0-43.3-35.1-78.4-78.4-78.4H402c-43.3,0-78.4,35.1-78.4,78.4v39.2h-196v39.2H168l39.2,744.8c0,43.3,35.1,78.4,78.4,78.4h431.2c43.3,0,78.4-35.1,78.4-78.4l38.6-744.8h38.6L872.4,127.6L872.4,127.6z M362.8,88.3c0-21.6,17.6-39.2,39.2-39.2h196c21.6,0,39.2,17.6,39.2,39.2v39.2H362.7L362.8,88.3L362.8,88.3z M756.1,909.5l-0.1,1v1c0,21.6-17.6,39.2-39.2,39.2H285.6c-21.6,0-39.2-17.6-39.2-39.2v-1l0-1l-39.2-742.8h587.3L756.1,909.5L756.1,909.5z" />
                        <path d="M480.4,245.1h39.2v627.2h-39.2V245.1L480.4,245.1z" />
                        <path d="M402.8,871.1l-40.1-626l-39.1,2.5l40.1,626L402.8,871.1z" />
                        <path d="M676.6,246.4l-39.1-2.5L598,871.1l39.1,2.5L676.6,246.4z" />
                    </g>
                </svg>
            </a>

        </div>
    </span>
    <div class="ml-8">
        <div class="h-24 relative flex p-3">
            <img src="{{ $product->attributes->image }}" alt="">
        </div>
    </div>
    <div class="w-9/12 lg:w-10/12 mt-3">
        <div class="flex flex-col  mr-8">
            <span class="flex text-sm leading-tight elipsis font-semibold mb-2 text-black">
                {{ $product->name }}
            </span>
            <div class="flex items-center pt-1 pb-2">
                <div class="__promo flex flex-wrap pb-2">
                    <span class="font-semibold text-sm" style="">
                        {{-- Cantidad: &nbsp;&nbsp;&nbsp;<input type="text" class="border border-gray-200" size="3" value="{{ $product->quantity }}"> --}}
                        Cant.: {{ $product->quantity }}
                    </span>
                    <span class="font-semibold ml-5 lg:text-base text-{{ $company->theme->name }}">
                        Precio: ${{ $product->price }}
                    </span>
                </div>
            </div>
        </div>
        <div class="flex items-end">
            <div class="flex">
                <div
                    class="flex justify-between control w-full border-solid rounded transition duration-500 ease-in-out">

                </div>
            </div>
        </div>
    </div>
</div>
