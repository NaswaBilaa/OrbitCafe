<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | Orbit Cafe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="px-4 md:px-20 py-8 bg-[#FEFDF8]">

<header class="flex items-center gap-4 mb-10">
    <button onclick="history.back()">
        <img src="{{ asset('icon/arrow-left.png') }}" class="w-8 h-8" alt="Back">
    </button>
    <div class="text-center w-full">
        <h1 class="text-xl font-bold text-[#B97D0E]">Review & Confirm</h1>
        <h2 class="text-4xl font-bold text-[#402F0B]">Checkout</h2>
    </div>
</header>

<main>
    {{-- FORM CHECKOUT --}}
    <form action="{{ route('orders.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @csrf

        {{-- LEFT SIDE: Items --}}
        <section class="md:col-span-2 space-y-4">

            {{-- Error validasi --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <strong class="font-bold">Error!</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @foreach (['success' => 'green', 'error' => 'red'] as $key => $color)
                @if (session($key))
                    <div class="bg-{{ $color }}-100 border border-{{ $color }}-400 text-{{ $color }}-700 px-4 py-3 rounded">
                        <strong class="font-bold">{{ ucfirst($key) }}!</strong> {{ session($key) }}
                    </div>
                @endif
            @endforeach

            {{-- Table Header --}}
            <div class="flex justify-between items-center bg-yellow-700 text-white font-semibold rounded-lg px-6 py-3 shadow">
                <div class="w-1/12 text-center">Select</div>
                <div class="w-1/3">Product</div>
                <div>Price</div>
                <div>Quantity</div>
                <div>Subtotal</div>
                <div class="w-16 text-center">Action</div>
            </div>

            {{-- Items --}}
            <div class="space-y-4">
                @forelse ($cartItems as $uuid => $item)
                    <div class="flex items-center bg-white text-[#B97D0E] rounded-lg px-4 py-3 shadow gap-3">

                        {{-- Select checkbox --}}
                        <div class="w-1/12 flex justify-center">
                            <input type="checkbox"
                                   name="selected[]"
                                   value="{{ $uuid }}"
                                   class="h-4 w-4 text-yellow-700 rounded"
                                   checked>
                        </div>

                        {{-- Product + Price + Qty + Subtotal --}}
                        <div class="flex justify-between items-center w-full gap-4">

                            {{-- Product --}}
                            <div class="w-1/3 flex items-center gap-4">
                                <img src="{{ $item['menu_image'] ? asset('storage/' . $item['menu_image']) : 'https://via.placeholder.com/80x80?text=No+Image' }}"
                                     class="w-20 h-20 rounded-full object-cover" alt="{{ $item['menu_name'] }}">
                                <div>
                                    <p>{{ $item['menu_name'] }}</p>
                                    @if (!empty($item['toppings']))
                                        <p class="text-xs text-gray-500">
                                            @foreach ($item['toppings'] as $topping)
                                                {{ $topping['name'] }}@if($topping['price'] > 0) (+Rp{{ number_format($topping['price']) }}) @endif
                                                @unless($loop->last), @endunless
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Price --}}
                            <div>Rp {{ number_format($item['item_price'], 0, ',', '.') }}</div>

                            {{-- Quantity (read-only di checkout) --}}
                            <div class="px-4 py-1 rounded-full bg-gray-100 text-center">
                                x {{ $item['quantity'] }}
                            </div>

                            {{-- Subtotal --}}
                            <div>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>

                            {{-- Delete button (AJAX) --}}
                            <div class="w-16 text-center">
                                <button type="button"
                                        class="text-xs text-red-500 underline delete-item"
                                        data-uuid="{{ $uuid }}">
                                    Delete
                                </button>
                            </div>
                        </div>

                        {{-- Hidden inputs utk OrderController@store (semua item, nanti difilter by selected) --}}
                        <input type="hidden" name="items[{{ $uuid }}][menu_id]" value="{{ $item['menu_id'] }}">
                        <input type="hidden" name="items[{{ $uuid }}][quantity]" value="{{ $item['quantity'] }}">
                        @foreach ($item['toppings'] as $topping)
                            <input type="hidden" name="items[{{ $uuid }}][toppings][]" value="{{ $topping['id'] }}">
                        @endforeach
                    </div>
                @empty
                    <div class="bg-white text-center py-10 rounded shadow">
                        <p class="text-xl text-gray-600">Your cart is empty!</p>
                        <a href="{{ route('all') }}"
                           class="mt-4 inline-block bg-yellow-700 text-white px-6 py-2 rounded-full hover:bg-yellow-800">
                            Start Shopping
                        </a>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- RIGHT SIDE: Customer info + Summary --}}
        <aside class="bg-white rounded-lg px-6 py-6 shadow-md space-y-6 h-fit">
            <h2 class="text-lg font-semibold text-[#402F0B]">Customer Details</h2>

            {{-- nama_lengkap --}}
            <div class="space-y-1">
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="nama_lengkap" name="nama_lengkap"
                       value="{{ old('nama_lengkap')}}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-700"
                       required>
            </div>

            {{-- no_telepon --}}
            <div class="space-y-1">
                <label for="no_telepon" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" id="no_telepon" name="no_telepon"
                       value="{{ old('no_telepon') }}"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-700"
                       required>
            </div>

            {{-- table_id --}}
            <div class="space-y-1">
                <label for="table_id" class="block text-sm font-medium text-gray-700">Table</label>
                <select id="table_id" name="table_id"
                        class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-700"
                        required>
                    <option value="">Select table</option>
                    @foreach ($tables as $table)
                        <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                            {{ $table->no_meja ?? $table->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <hr>

            {{-- Order summary --}}
            <div class="space-y-2 text-sm text-gray-700">
                <div class="flex justify-between">
                    <span>Items</span>
                    <span>{{ count($cartItems) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Sub Total</span>
                    <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
                <hr>
                <div class="flex justify-between font-bold text-[#B97D0E]">
                    <span>Total</span>
                    <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Tombol Beli Sekarang --}}
            <button type="submit"
                    class="w-full bg-yellow-700 text-white font-semibold py-2 rounded-full disabled:opacity-50
                           {{ count($cartItems) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ count($cartItems) === 0 ? 'disabled' : '' }}>
                Beli Sekarang
            </button>

            <p class="text-xs text-gray-400 text-center">
                Pilih item yang ingin kamu checkout. Item yang tidak dicentang tidak akan ikut diproses.
            </p>
        </aside>
    </form>

    {{-- FORM CLEAR CART (terpisah, di bawah) --}}
    @if(count($cartItems) > 0)
        <form action="{{ route('cart.clear') }}" method="POST" class="mt-4 flex justify-end">
            @csrf
            <button type="submit"
                    class="px-4 py-2 text-sm text-red-600 border border-red-400 rounded-full hover:bg-red-50">
                Clear Cart
            </button>
        </form>
    @endif
</main>

{{-- JS untuk tombol delete per item --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-item').forEach(btn => {
            btn.addEventListener('click', () => {
                const uuid = btn.dataset.uuid;

                if (!confirm('Hapus item ini dari keranjang?')) return;

                fetch('{{ url('/menu/cart/remove') }}/' + uuid, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus item.');
                    }
                })
                .catch(() => alert('Terjadi kesalahan.'));
            });
        });
    });
</script>

</body>
</html>
