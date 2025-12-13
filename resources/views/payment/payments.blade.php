<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payments | Orbit Cafe</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="w-full px-4 md:px-20 py-8 bg-[#FEFDF8]">
    <header class="flex items-start">
        <button onclick="history.back()">
            <img src="{{ asset('icon/arrow-left.png') }}" alt="" class="w-8 h-8" />
        </button>
        <div class="w-full flex flex-col text-center mb-10">
            <h1 class="text-xl font-bold text-[#B97D0E]">Payment Options</h1>
            <h1 class="text-4xl font-bold text-[#402F0B]">Payment Methods</h1>
        </div>
    </header>

    <main class="w-full flex flex-col lg:flex-row gap-6 mt-10">
        {{-- Kiri: Informasi Order --}}
        <section class="lg:w-3/4 w-full space-y-4">
            <div class="bg-white rounded-2xl shadow-md px-6 py-6">
                <h2 class="text-xl font-bold text-[#402F0B] mb-4">Order Detail</h2>

                <p class="text-sm text-gray-600 mb-1">
                    <span class="font-semibold text-[#402F0B]">Invoice:</span>
                    {{ $order->invoice_number }}
                </p>
                <p class="text-sm text-gray-600 mb-1">
                    <span class="font-semibold text-[#402F0B]">Name:</span>
                    {{ $order->nama_lengkap }}
                </p>
                <p class="text-sm text-gray-600 mb-4">
                    <span class="font-semibold text-[#402F0B]">Phone:</span>
                    {{ $order->no_telepon }}
                </p>

                <h3 class="text-lg font-semibold text-[#402F0B] mb-2">Items</h3>
                <div class="space-y-2 text-sm">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between items-center border-b pb-2">
                            <div>
                                <p class="font-medium text-[#402F0B]">{{ $item->menu->name }}</p>
                                @if ($item->toppings->count())
                                    <p class="text-xs text-gray-500">
                                        @foreach ($item->toppings as $topping)
                                            {{ $topping->topping->name }}
                                            @unless($loop->last), @endunless
                                        @endforeach
                                    </p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-[#A19580]">x {{ $item->quantity }}</p>
                                <p class="font-semibold text-[#402F0B]">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Tombol Bayar Sekarang --}}
                <div class="mt-6">
                    @if($payment && $payment->snap_token)
                        @if($order->status === 'pending' && $payment->payment_status === 'pending')
                            <button id="pay-button"
                                    class="w-full md:w-auto px-6 py-3 rounded-full bg-[#B97D0E] text-white font-semibold hover:bg-[#402F0B] transition">
                                Bayar Sekarang
                            </button>
                        @else
                            <p class="text-sm text-green-600 font-semibold">
                                Pembayaran sudah diproses dengan status: {{ ucfirst($payment->payment_status) }}.
                            </p>
                        @endif
                    @else
                        <p class="text-sm text-red-600 font-semibold">
                            Snap Token tidak tersedia. Silakan coba lagi atau hubungi admin.
                        </p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Kanan: Ringkasan Order --}}
        <aside class="lg:w-1/4 w-full">
            <div class="min-h-96 bg-white rounded-2xl shadow-md shadow-neutral-300/40 px-6 pt-6 pb-12 text-sm">
                <h2 class="text-base font-bold text-[#402F0B] mb-3">Order Summary</h2>
                <hr class="border-t border-[#E0DCD6] mb-4" />

                <div class="flex justify-between text-[#A19580] mb-2">
                    <span>Items</span>
                    <span class="text-[#402F0B] font-medium">{{ $totalItems ?? $order->items->sum('quantity') }}</span>
                </div>

                <div class="flex justify-between text-[#A19580] mb-2">
                    <span>Sub Total</span>
                    <span class="text-[#402F0B] font-semibold">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between text-[#A19580] mb-2">
                    <span>Shipping</span>
                    <span class="text-[#402F0B] font-medium">Rp 0</span>
                </div>

                <div class="flex justify-between text-[#A19580] mb-4">
                    <span>Taxes</span>
                    <span class="text-[#402F0B] font-medium">Rp 0</span>
                </div>

                <hr class="border-t border-[#E0DCD6] mb-3" />

                <div class="flex justify-between text-[#402F0B] font-bold text-base">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </aside>
    </main>

    {{-- Snap JS Midtrans --}}
    @if($payment && $payment->snap_token && $order->status === 'pending' && $payment->payment_status === 'pending')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script type="text/javascript">
            document.getElementById('pay-button').onclick = function () {
                snap.pay('{{ $payment->snap_token }}', {
                    onSuccess: function (result) {
                        console.log('Success:', result);
                        window.location.href = "{{ route('order.success') }}";
                    },
                    onPending: function (result) {
                        console.log('Pending:', result);
                        alert("Menunggu pembayaran...");
                    },
                    onError: function (result) {
                        console.log('Error:', result);
                        alert("Terjadi kesalahan pembayaran!");
                    }
                });
            };
        </script>
    @endif
</body>
</html>
