<div class="bg-[#fefaf4] p-8 rounded-xl w-full max-w-3xl font-serif text-brown-900 space-y-8" style="font-family: 'Georgia', serif">
    {{-- Header --}}
    <div class="space-y-2">
        <h1 class="text-5xl font-extrabold tracking-wide text-brown-800">INVOICE</h1>
        <p class="text-brown-600">Invoice number # {{ $order->id }} &nbsp; {{ $order->created_at->format('F j') }}</p>
    </div>

    {{-- Order Info --}}
    <div class="grid md:grid-cols-2 text-sm text-brown-800 gap-4 border-t border-b border-brown-300 py-4">
        <div>
            <p><strong>Billed to:</strong></p>
            <p>{{ $order->user->name ?? '-' }}</p>
            <p>{{ $order->user->email ?? '-' }}</p>
        </div>
        <div>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p><strong>Total Price:</strong> Rp {{ number_format($order->total_price) }}</p>
            <p><strong>Payment Method:</strong> Rp {{ $order->payment->payment_method ?? '-' }}</p>
        </div>
    </div>

    {{-- Item Table --}}
    <table class="w-full text-sm text-brown-800 border-t border-b border-brown-300 border-separate" style="border-collapse: separate; border-spacing: 0">
        <thead class="text-left text-brown-600 border-b border-brown-300">
            <tr>
                <th class="py-2 border-r border-brown-300 pr-3">ITEM</th>
                <th class="py-2 border-r border-brown-300 px-3 text-center">QTY</th>
                <th class="py-2 border-r border-brown-300 px-3">TOPPINGS</th>
                <th class="py-2 border-r border-brown-300 px-3 text-right">PRICE</th>
                <th class="py-2 border-r border-brown-300 px-3 text-right">TOPPING PRICE</th>
                <th class="py-2 px-3 text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                @php
                    $grouped = $item->toppings->groupBy('topping.type');
                    $size  = $grouped['size'][0]->topping->name ?? '-';
                    $sugar = $grouped['sugar'][0]->topping->name ?? '-';
                    $ice   = $grouped['ice'][0]->topping->name ?? '-';
                    $toppingPrice = $item->toppings->sum(fn($t) => $t->topping->price ?? 0);
                    $drinkPrice = $item->subtotal / $item->quantity - $toppingPrice;
                    $subtotal = $item->subtotal;
                @endphp
                <tr class="border-b border-brown-200">
                    <td class="py-2 border-r border-brown-200 pr-3">{{ $item->drink->name ?? '-' }}</td>
                    <td class="py-2 border-r border-brown-200 px-3 text-center">{{ $item->quantity }}</td>
                    <td class="py-2 border-r border-brown-200 px-3">
                        size: {{ $size }}, sugar: {{ $sugar }}, ice: {{ $ice }}
                    </td>
                    <td class="py-2 border-r border-brown-200 px-3 text-right">Rp {{ number_format($drinkPrice) }}</td>
                    <td class="py-2 border-r border-brown-200 px-3 text-right">Rp {{ number_format($toppingPrice) }}</td>
                    <td class="py-2 px-3 text-right">Rp {{ number_format($subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total --}}
    <div class="flex justify-end items-center text-lg font-bold text-brown-900 pt-4">
        <div class="text-right">
            <p class="text-2xl mt-2">TOTAL: Rp {{ number_format($order->total_price) }}</p>
        </div>
    </div>

    {{-- Print Button --}}
    <div class="text-right mt-4 print:hidden">
        <button onclick="window.print()" class="px-5 py-2 rounded-md bg-yellow-400 text-brown-900 font-semibold hover:bg-yellow-500 transition">
            Cetak Invoice
        </button>
    </div>
</div>

<style>
    @media print {
        button {
            display: none !important;
        }
        body {
            background: white !important;
            color: black !important;
        }
        table {
            width: 100% !important;
            border-color: black !important;
            color: black !important;
        }
        thead {
            background-color: #ddd !important;
            color: black !important;
        }
        tbody tr {
            background-color: white !important;
            color: black !important;
        }
    }
</style>
