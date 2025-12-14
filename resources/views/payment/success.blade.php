<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - Orbit Coffee</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="px-4 md:px-20 py-8 bg-[#FEFDF8]">

    <!-- Header -->
    <header class="flex items-center gap-4 mb-10">
        <button onclick="window.location.href='{{ route('all') }}'">
            <img src="{{ asset('icon/arrow-left.png') }}" class="w-8 h-8" alt="Back">
        </button>
        <div class="text-center w-full">
            <h1 class="text-xl font-bold text-[#B97D0E]">Payment</h1>
            <h2 class="text-4xl font-bold text-[#402F0B]">QRIS</h2>
        </div>
    </header>

    <!-- Main Content -->
    <main class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Transaction Details (Left Side - 2 columns) -->
        <section class="md:col-span-2 space-y-4 bg-white rounded-2xl shadow-md p-8">
            
            
            <h2 class="text-3xl font-bold text-[#402F0B] mb-8">Transaction</h2>
            <!-- Merchant Header -->
            <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-200">
                <div>
                    <h3 class="text-2xl font-bold text-[#402F0B]">Payment To ORBIT COFFEE</h3>
                    <p class="text-[#B97D0E] font-semibold text-lg">Payment</p>
                </div>
            </div>

            <!-- Transaction Information -->
            <div class="space-y-5">
                <!-- Date & Invoice -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600 text-sm">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y - H:i') }}</span>
                    <span class="text-[#402F0B] font-mono font-semibold">{{ $order->invoice_number }}</span>
                </div>

                <!-- Payer Name -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-[#402F0B] font-semibold">Payer Name</span>
                    <span class="text-[#402F0B] font-semibold">{{ $order->nama_lengkap }}</span>
                </div>

                <!-- Destination Account -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-[#402F0B] font-semibold">Destination Account</span>
                    <span class="text-[#402F0B] font-semibold">Orbit Coffee</span>
                </div>

                <!-- Payment Method -->
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-[#402F0B] font-semibold">payment method</span>
                    <span class="text-[#402F0B] font-semibold uppercase">{{ $payment->payment_method ?? 'DANA' }}</span>
                </div>

                <!-- Transaction Details (Expandable) -->
                <div class="py-3">
                    <button onclick="toggleDetails()" class="flex justify-between items-center w-full text-left">
                        <span class="text-[#402F0B] font-semibold">transaction details</span>
                        <svg id="chevron-icon" class="w-8 h-8 text-[#402F0B] transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Expandable Content -->
                    <div id="details-content" class="hidden mt-4 space-y-3 pl-4 border-l-4 border-[#B97D0E]">
                        @foreach($order->items as $item)
                        <div class="flex justify-between text-sm py-2">
                            <span class="text-gray-700">{{ $item->menu->name }} <span class="text-[#B97D0E] font-semibold">(x{{ $item->quantity }})</span></span>
                            <span class="text-[#402F0B] font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Order Summary (Right Side - 1 column) -->
        <aside class="bg-white rounded-2xl shadow-md px-6 py-6 h-fit space-y-6">
            <h2 class="text-2xl font-bold text-[#402F0B]">Order Summary</h2>

            <!-- Summary Items -->
            <div class="space-y-4 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Items</span>
                    <span class="text-[#402F0B] font-semibold">{{ $totalItems }}</span>
                </div>

                <div class="flex justify-between text-gray-600">
                    <span>Sub Total</span>
                    <span class="text-[#402F0B] font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>

                <!-- Total -->
                <div class="flex justify-between text-lg font-bold pt-2">
                    <span class="text-[#402F0B]">Total</span>
                    <span class="text-[#B97D0E]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Success Status -->
            <div class="flex items-center justify-center gap-2 bg-green-50 rounded-xl p-4 border border-green-200">
                <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span class="text-green-700 font-bold">Transaction Successful</span>
            </div>
        </aside>
    </main>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4 justify-center mt-10 max-w-4xl mx-auto">
        <a href="{{ route('all') }}" 
           class="flex-1 bg-yellow-700 hover:bg-yellow-800 text-white font-semibold py-3 px-8 rounded-full text-center transition-colors shadow-md">
            Back to Homepage
        </a>
        <a href="{{ route('download-receipt', ['invoice' => $order->invoice_number]) }}" 
           class="flex-1 bg-yellow-700 hover:bg-yellow-800 text-white font-semibold py-3 px-8 rounded-full text-center transition-colors shadow-md">
            Download Receipt
        </a>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleDetails() {
            const content = document.getElementById('details-content');
            const chevron = document.getElementById('chevron-icon');
            
            content.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }
    </script>

</body>
</html>