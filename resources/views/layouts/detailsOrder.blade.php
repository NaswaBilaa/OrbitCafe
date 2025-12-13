<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Ordered Details | Orbit Cafe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="px-20 py-8 bg-[#FEFDF8]">
    <header class="flex items-start">
        <button onclick="history.back()">
            <img src="{{ asset('icon/arrow-left.png') }}" alt="" class="w-8 h-8">
        </button>
        <div class="w-full flex flex-col items-center mb-10">
            <h1 class="text-4xl font-bold text-[#402F0B]">{{ $menu->name }}</h1>
            <h1 class="text-xl font-bold text-[#B97D0E]">
                price: Rp {{ number_format($menu->price, 0, ',', '.') }}
            </h1>
        </div>
    </header>

    <main class="my-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 *:h-[30rem]">

            {{-- LEFT: Image --}}
            <div class="bg-white p-6 rounded-lg shadow-md flex justify-center">
                @if($menu->image)
                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="max-h-full object-contain">
                @else
                    <img src="https://via.placeholder.com/400x400?text=No+Image" alt="No Image" class="max-h-full object-contain">
                @endif
            </div>

            {{-- RIGHT: Detail & Form --}}
            <div class="bg-white col-span-2 p-6 rounded-lg shadow-md flex flex-col justify-between">
                
                <p class="text-sm mb-6">{{ $menu->description ?? 'No description available.' }}</p>

                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf

                    <input type="hidden" name="menu_id" value="{{ $menu->id }}">

                    {{-- QUANTITY UI --}}
                    <div class="mb-6">
                        <p class="font-semibold mb-2">Quantity :</p>

                        <div class="inline-flex items-center gap-2 bg-gray-100 rounded-full px-3 py-1 shadow-sm border border-gray-200">
                            <button
                                type="button"
                                class="qty-minus w-7 h-7 flex items-center justify-center rounded-full text-lg font-bold hover:bg-gray-200">
                                −
                            </button>

                            <span class="qty-display w-8 text-center font-semibold text-[#402F0B] border-x border-gray-200">
                                1
                            </span>

                            <button
                                type="button"
                                class="qty-plus w-7 h-7 flex items-center justify-center rounded-full text-lg font-bold hover:bg-gray-200">
                                +
                            </button>
                        </div>

                        <input type="hidden" id="quantity-input" name="quantity" value="1" min="1">
                    </div>

                    {{-- SIZE --}}
                    <div class="mb-4">
                        <p class="font-semibold mb-2">Size :</p>
                        <div class="flex gap-4">
                            @forelse ($toppings['size'] as $topping)
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="size_id"
                                        value="{{ $topping->id }}"
                                        class="form-radio text-orange-900 rounded-md"
                                        @if($loop->first) checked @endif
                                    >
                                    <span>{{ $topping->name }}</span>
                                    @if ($topping->price > 0)
                                        (+Rp{{ number_format($topping->price, 0, ',', '.') }})
                                    @endif
                                </label>
                            @empty
                                <p class="text-gray-500 text-sm">No size options available.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- SUGAR --}}
                    <div class="mb-4">
                        <p class="font-semibold mb-2">Sugar:</p>
                        <div class="flex gap-x-12">
                            @forelse ($toppings['sugar'] as $topping)
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="sugar_id"
                                        value="{{ $topping->id }}"
                                        class="form-radio text-orange-900 rounded-md"
                                        @if($loop->first) checked @endif
                                    >
                                    <span>{{ $topping->name }}</span>
                                    @if ($topping->price > 0)
                                        (+Rp{{ number_format($topping->price, 0, ',', '.') }})
                                    @endif
                                </label>
                            @empty
                                <p class="text-gray-500 text-sm">No sugar options available.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- ICE --}}
                    <div class="mb-6">
                        <p class="font-semibold mb-2">Ice Cube:</p>
                        <div class="flex gap-4">
                            @forelse ($toppings['ice'] as $topping)
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="radio"
                                        name="ice_id"
                                        value="{{ $topping->id }}"
                                        class="form-radio text-orange-900 rounded-md"
                                        @if($loop->first) checked @endif
                                    >
                                    <span>{{ $topping->name }}</span>
                                    @if ($topping->price > 0)
                                        (+Rp{{ number_format($topping->price, 0, ',', '.') }})
                                    @endif
                                </label>
                            @empty
                                <p class="text-gray-500 text-sm">No ice options available.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="flex justify-end gap-4 mt-6">
                        <button type="submit" name="mode" value="cart"
                            class="px-6 py-2 border border-yellow-600 text-yellow-600 rounded-full hover:bg-yellow-100 transition">
                            ADD TO CART
                        </button>

                        <button type="submit" name="mode" value="buy_now"
                            class="px-6 py-2 bg-yellow-700 text-white rounded-full hover:bg-yellow-800 transition">
                            BELI SEKARANG
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    {{-- JS: Quantity Handler --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const minusBtn = document.querySelector('.qty-minus');
            const plusBtn  = document.querySelector('.qty-plus');
            const display  = document.querySelector('.qty-display');
            const input    = document.getElementById('quantity-input');

            let qty = parseInt(input.value) || 1;

            const sync = () => {
                if (qty < 1) qty = 1;
                display.textContent = qty;
                input.value = qty;
            };

            minusBtn.addEventListener('click', () => {
                qty--;
                sync();
            });

            plusBtn.addEventListener('click', () => {
                qty++;
                sync();
            });

            sync();
        });
    </script>

</body>
</html>
