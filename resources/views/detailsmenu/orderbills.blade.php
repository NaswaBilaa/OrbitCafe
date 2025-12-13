<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart | Orbit Cafe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="px-4 md:px-20 py-8 bg-[#FEFDF8]">

<header class="flex items-center gap-4 mb-10">
    <button onclick="history.back()">
        <img src="{{ asset('icon/arrow-left.png') }}" class="w-8 h-8" alt="Back">
    </button>
    <div class="text-center w-full">
        <h1 class="text-xl font-bold text-[#B97D0E]">Checkout your Orders</h1>
        <h2 class="text-4xl font-bold text-[#402F0B]">Shopping Cart</h2>
    </div>
</header>

<main>
    {{-- FORM UNTUK MEMILIH ITEM YANG AKAN DI-CHECKOUT --}}
    <form action="{{ route('checkout.show') }}" method="GET"
          class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Cart Items --}}
        <section class="md:col-span-2 space-y-4">
            @foreach (['success' => 'green', 'error' => 'red'] as $key => $color)
                @if (session($key))
                    <div class="bg-{{ $color }}-100 border border-{{ $color }}-400 text-{{ $color }}-700 px-4 py-3 rounded">
                        <strong class="font-bold">{{ ucfirst($key) }}!</strong> {{ session($key) }}
                    </div>
                @endif
            @endforeach

            {{-- Table Header --}}
            <div class="flex justify-between items-center bg-yellow-700 text-white font-semibold rounded-lg px-6 py-3 shadow">
                <div class="w-10 text-center">Select</div>
                <div class="w-1/3">Product</div>
                <div>Price</div>
                <div>Quantity</div>
                <div>Subtotal</div>
                <div class="w-16 text-center">Action</div>
            </div>

            {{-- Table Rows --}}
            <div id="cart-items-container" class="space-y-4">
                @forelse ($cartItems as $uuid => $item)
                    <div class="flex items-center bg-white text-[#B97D0E] rounded-lg px-4 py-3 shadow cart-item gap-3"
                         data-uuid="{{ $uuid }}" data-item-price="{{ $item['item_price'] }}">

                        {{-- Select checkbox --}}
                        <div class="w-1/12 flex justify-center">
                            <input type="checkbox"
                                   name="selected[]"
                                   value="{{ $uuid }}"
                                   class="h-4 w-4 text-yellow-700 rounded"
                                   checked>
                        </div>

                        {{-- Isi utama row --}}
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

                            {{-- Quantity --}}
                            <div class="flex items-center gap-2 bg-gray-100 px-4 py-1 rounded-full">
                                <input type="hidden" name="items[{{ $uuid }}][menu_id]" value="{{ $item['menu_id'] }}">
                                <input type="hidden" name="items[{{ $uuid }}][quantity]" class="quantity-input" value="{{ $item['quantity'] }}">
                                <button type="button" class="quantity-minus text-lg font-bold">-</button>
                                <span class="quantity-display border-x px-3">{{ $item['quantity'] }}</span>
                                <button type="button" class="quantity-plus text-lg font-bold">+</button>
                            </div>

                            {{-- Subtotal --}}
                            <div class="item-subtotal">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </div>

                            {{-- Delete --}}
                            <div class="w-16 text-center">
                                <button type="button"
                                        class="text-xs text-red-500 underline btn-delete-item"
                                        data-uuid="{{ $uuid }}">
                                    Delete
                                </button>
                            </div>
                        </div>

                        {{-- Toppings --}}
                        @foreach ($item['toppings'] as $topping)
                            <input type="hidden" name="items[{{ $uuid }}][toppings][]" value="{{ $topping['id'] }}">
                        @endforeach
                    </div>
                @empty
                    <div class="bg-white text-center py-10 rounded shadow">
                        <p class="text-xl text-gray-600">Your cart is empty!</p>
                        <a href="{{ route('all') }}" class="mt-4 inline-block bg-yellow-700 text-white px-6 py-2 rounded-full hover:bg-yellow-800">Start Shopping</a>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Order Summary --}}
        <aside class="bg-white rounded-lg px-6 py-6 shadow-md space-y-4 h-fit">
            <h2 class="text-lg font-semibold">Order Summary</h2>
            <div class="space-y-2 text-sm text-gray-700">
                <div class="flex justify-between">
                    <span>Items</span>
                    <span id="summary-total-items">{{ count($cartItems) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Sub Total</span>
                    <span id="summary-subtotal">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between"><span>Shipping</span><span>Rp 0</span></div>
                <div class="flex justify-between"><span>Taxes</span><span>Rp 0</span></div>
                <hr>
                <div class="flex justify-between font-bold text-[#B97D0E]">
                    <span>Total</span>
                    <span id="summary-grand-total">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Proceed to Checkout (pakai selected[]) --}}
            <button type="submit"
                    class="w-full bg-yellow-700 text-white font-semibold py-2 rounded-full
                           {{ count($cartItems) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ count($cartItems) === 0 ? 'disabled' : '' }}>
                Proceed to Checkout
            </button>
        </aside>
    </form>

    {{-- Clear Cart: SAMA STYLE DENGAN CHECKOUT --}}
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

{{-- JavaScript --}}
<script type="module">
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('cart-items-container');

    // plus / minus quantity
    container?.addEventListener('click', e => {
        const target = e.target;
        if (!target.matches('.quantity-plus, .quantity-minus')) return;

        const item = target.closest('.cart-item');
        const uuid = item.dataset.uuid;
        const display = item.querySelector('.quantity-display');
        const inputQty = item.querySelector('.quantity-input');
        let qty = parseInt(display.textContent);

        qty += target.classList.contains('quantity-plus') ? 1 : -1;
        if (qty < 0) return;

        fetch('{{ route('cart.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ uuid, quantity: qty })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return alert(data.message);

            if (qty === 0) {
                item.remove();
            } else {
                display.textContent = data.item.quantity;
                inputQty.value = data.item.quantity;
                item.querySelector('.item-subtotal').textContent = formatRupiah(data.item.subtotal);
            }

            updateSummary(data.total_price);
        })
        .catch(() => alert('Gagal memperbarui keranjang.'));
    });

    // delete item
    document.querySelectorAll('.btn-delete-item').forEach(btn => {
        btn.addEventListener('click', () => {
            const uuid = btn.dataset.uuid;
            if (!confirm('Hapus item ini dari keranjang?')) return;

            fetch('{{ url('/menu/cart/remove') }}/' + uuid, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Gagal menghapus item.');
                    return;
                }
                const row = document.querySelector('.cart-item[data-uuid="' + uuid + '"]');
                row?.remove();
                updateSummary(data.total_price);
            })
            .catch(() => alert('Terjadi kesalahan.'));
        });
    });

    function updateSummary(total) {
        document.getElementById('summary-total-items').textContent =
            document.querySelectorAll('.cart-item').length;
        document.getElementById('summary-subtotal').textContent =
        document.getElementById('summary-grand-total').textContent = formatRupiah(total);
    }

    const formatRupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
});
</script>

</body>
</html>
