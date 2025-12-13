<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order | Roastly</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FEFDF8] px-4 md:px-20 py-8">

<header class="flex items-center gap-4 mb-10">
    <a href="{{ route('all') }}">
        <img src="{{ asset('icon/arrow-left.png') }}" class="w-8 h-8" alt="Back">
    </a>
    <div class="text-center w-full">
        <h1 class="text-xl font-bold text-[#B97D0E]">Order Tracking</h1>
        <h2 class="text-4xl font-bold text-[#402F0B]">Track Your Drink</h2>
    </div>
</header>

<main class="max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- LEFT: Order details --}}
    <section class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm text-gray-500">Invoice</p>
                    <p class="text-lg font-bold text-[#402F0B]">{{ $order->invoice_number }}</p>

                    <p class="text-sm text-gray-500 mt-4">Nama / No. HP</p>
                    <p class="text-[#402F0B] font-medium">{{ $order->nama_lengkap }} • {{ $order->no_telepon }}</p>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="inline-block mt-1 px-4 py-2 rounded-full text-sm font-semibold
                        @if(in_array($order->status, ['paid','processing'])) bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'ready') bg-blue-100 text-blue-800
                        @elseif($order->status === 'completed') bg-green-100 text-green-800
                        @else bg-red-100 text-red-700 @endif
                    ">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-[#402F0B] mb-4">Order Items</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-start gap-4 border-b pb-3 last:border-b-0 last:pb-0">
                        <div>
                            <p class="font-semibold text-[#402F0B]">{{ $item->drink->name ?? 'Drink' }}</p>
                            <p class="text-xs text-gray-500">
                                Qty: {{ $item->quantity }}
                                @if(!empty($item->toppings))
                                    • Topping:
                                    @foreach($item->toppings as $top)
                                        {{ $top->topping->name ?? '' }}@unless($loop->last), @endunless
                                    @endforeach
                                @endif
                            </p>
                        </div>
                        <p class="font-semibold text-[#B97D0E]">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 flex justify-between font-bold text-[#402F0B]">
                <span>Total</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>
    </section>

    {{-- RIGHT: Queue & table --}}
    <aside class="space-y-4">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-[#402F0B] mb-3">Table & Queue</h3>
            <div class="text-sm text-gray-700 space-y-2">
                <div class="flex justify-between">
                    <span>Table</span>
                    <span class="font-semibold text-[#402F0B]">
                        {{ $order->table->table_number ?? $order->table_id }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>Queue Now</span>
                    <span class="font-semibold text-[#402F0B]">#{{ $queueNow }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Estimated Time</span>
                    <span class="font-semibold text-[#402F0B]">{{ $estimatedMinutes }} min</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-[#402F0B] mb-2">Pick Up</h3>

            @if($order->status === 'ready')
                <p class="text-green-700 font-semibold">Pesanan kamu sudah siap. Silakan ambil di counter ☕</p>
            @elseif($order->status === 'completed')
                <p class="text-green-700 font-semibold">Pesanan sudah selesai. Terima kasih!</p>
            @elseif(in_array($order->status, ['paid','processing']))
                <p class="text-yellow-700 font-semibold">Pesanan sedang dibuat. Mohon tunggu ya 😉</p>
            @else
                <p class="text-red-600 font-semibold">Status pembayaran bermasalah ({{ $order->status }}).</p>
            @endif

            <a href="{{ route('all') }}"
               class="block mt-4 text-center w-full bg-yellow-700 text-white font-semibold py-2 rounded-full hover:bg-yellow-800 transition">
                Back to Menu
            </a>
        </div>
    </aside>
</main>

</body>
</html>
