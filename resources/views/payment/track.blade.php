<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lacak Pesanan - Orbit Cafe</title>
    {{-- Asumsi kamu sudah ganti nama file CSS/JS ke nama project --}}
    @vite(['resources/css/order-track.css', 'resources/js/order-track.js'])
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <svg class="logo-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 1v3M10 1v3M14 1v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span class="logo-text">Orbit Cafe</span>
            </div>
            <div class="header-right">
                <span class="invoice-label">Nomor Invoice</span>
                <span class="invoice-number">{{ $order->invoice_number }}</span>
                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $order->status)) }}">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="order-summary-card">
                <h2 class="card-title">Ringkasan Pesanan</h2>
                
                <div class="summary-item">
                    <label>Nomor Meja</label>
                    {{-- Asumsi kolom di table model kamu adalah table_number --}}
                    <p class="summary-value" id="table-number-value">{{ $order->table->no_meja }}</p> 
                </div>

                <div class="summary-item">
                    <label>Nama Pelanggan</label>
                    <p class="summary-value">{{ $order->nama_lengkap }}</p>
                </div>

                <div class="summary-item">
                    <label>Waktu Pemesanan</label>
                    <p class="summary-value">{{ $order->created_at->format('H:i') }}</p>
                </div>

                @if(in_array($order->status, ['paid', 'processing', 'ready', 'serving']))
                <div class="summary-item">
                    <label>Estimasi Selesai</label>
                    <p class="summary-value estimated-time">
                        <span class="time-minutes">{{ $estimatedMinutes }}</span>
                        <span class="time-label">menit</span>
                    </p>
                </div>

                <div class="queue-info">
                    Anda urutan ke-#{{ $queueNow }} dalam antrian
                </div>
                @endif

                @if($order->status === 'ready')
                <div class="ready-banner">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Pesanan Anda sudah siap!</span>
                </div>
                @endif
                
                @if($order->status === 'serving')
                <div class="ready-banner bg-blue-100 text-blue-700">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Pesanan sedang diantar ke meja Anda!</span>
                </div>
                @endif

                @if($order->status === 'completed')
                <div class="completed-banner">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                    <span>Pesanan telah selesai. Terima kasih!</span>
                </div>
                @endif

                <p class="tracking-note">Tracking aktif. Status diperbarui secara otomatis.</p>

                <div class="total-amount">
                    <span>Total Pembayaran</span>
                    <span class="amount">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>

                @if($order->payment)
                <div class="payment-info">
                    <div class="payment-row">
                        <span class="payment-label">Metode Pembayaran</span>
                        <span class="payment-value">{{ strtoupper($order->payment->payment_method) }}</span>
                    </div>
                    <div class="payment-row">
                        <span class="payment-label">Status Pembayaran</span>
                        <span class="payment-value status-{{ $order->payment->status }}">
                            @if ($order->payment->payment_status == 'paid')
                                Sukses
                            @else
                                {{ ucfirst($order->payment->payment_status) }} 
                            @endif
                        </span>
                    </div>
                </div>
                @endif

                <button class="btn btn-whatsapp" onclick="contactWhatsApp()">
                    <svg viewBox="0 0 24 24" fill="currentColor"></svg>
                    Hubungi via WhatsApp
                </button>
                <a href="{{ url('/') }}" class="btn-link">Kembali ke Halaman Utama</a>
            </div>

            <div class="order-progress-card">
                <h2 class="card-title">Status Pesanan</h2>
                
                <div class="progress-timeline" data-status="{{ $order->status }}">
                    
                    {{-- 1. PAID/RECEIVED --}}
                    <div class="progress-step {{ in_array($order->status, ['paid', 'processing', 'ready', 'serving', 'completed']) ? 'active completed' : '' }}">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <span class="step-label">Order Received</span>
                    </div>

                    {{-- 2. PROCESSING --}}
                    <div class="progress-step {{ in_array($order->status, ['processing', 'ready', 'serving', 'completed']) ? 'active' : '' }} {{ $order->status === 'processing' ? 'current' : '' }} {{ in_array($order->status, ['ready', 'serving', 'completed']) ? 'completed' : '' }}">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z" stroke="currentColor" stroke-width="2"/>
                                <path d="M6 1v3M10 1v3M14 1v3" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <span class="step-label">Processing</span>
                    </div>

                    {{-- 3. READY --}}
                    <div class="progress-step {{ in_array($order->status, ['ready', 'serving', 'completed']) ? 'active' : '' }} {{ $order->status === 'ready' ? 'current' : '' }} {{ in_array($order->status, ['serving', 'completed']) ? 'completed' : '' }}">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="step-label">Ready</span>
                    </div>

                    {{-- 4. SERVING --}}
                    <div class="progress-step {{ in_array($order->status, ['serving', 'completed']) ? 'active' : '' }} {{ $order->status === 'serving' ? 'current' : '' }} {{ $order->status === 'completed' ? 'completed' : '' }}">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                           
                            </svg>
                        </div>
                        <span class="step-label">Serving</span>
                    </div>

                    {{-- 5. COMPLETED --}}
                    <div class="progress-step {{ $order->status === 'completed' ? 'active current' : '' }}">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <span class="step-label">Completed</span>
                    </div>
                </div>

                @if(in_array($order->status, ['expired', 'failed', 'cancelled']))
                <div class="error-message">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <p>Pesanan {{ $statusLabel }}. Silakan hubungi kami jika ada pertanyaan.</p>
                </div>
                @endif
            </div>

            <div class="order-items-card">
                <h2 class="card-title">Daftar Pesanan</h2>
                
                <div class="items-list">
                    @foreach($order->items as $item)
                    <div class="item-row">
                        {{-- Perbaikan Image Source (Asumsi $item->menu->image berisi path 'menus/xxx.jpg') --}}
                        <img src="{{ $item->menu->image ? asset('storage/' . $item->menu->image) : 'https://via.placeholder.com/80x80?text=No+Image' }}" 
                             alt="{{ $item->menu->name }}" 
                             class="item-image"
                             onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                             
                        <div class="item-details">
                            <h3 class="item-name">{{ $item->menu->name }}</h3>
                            @if($item->notes || $item->toppings->count() > 0)
                            <p class="item-description">
                                @if($item->toppings->count() > 0)
                                    {{ $item->toppings->pluck('topping.name')->join(', ') }}
                                @endif
                                @if($item->notes)
                                    {{ $item->toppings->count() > 0 ? ' • ' : '' }}{{ $item->notes }}
                                @endif
                            </p>
                            @endif
                            <p class="item-quantity">Qty: {{ $item->quantity }}</p>
                            @if($item->toppings->count() > 0)
                            <p class="item-extras">
                                Topping: Rp {{ number_format($item->toppings->sum('topping.price'), 0, ',', '.') }}
                            </p>
                            @endif
                        </div>
                        <div class="item-pricing">
                            <span class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mobile-actions">
                    <div class="total-amount">
                        <span>Total Pembayaran</span>
                        <span class="amount">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                    </div>
                    <button class="btn btn-whatsapp" onclick="contactWhatsApp()">
                        <svg viewBox="0 0 24 24" fill="currentColor"></svg>
                        Hubungi via WhatsApp
                    </button>
                    <a href="{{ url('/') }}" class="btn-link">Kembali ke Halaman Utama</a>
                </div>
            </div>
        </div>
    </main>

    <script>
       function contactWhatsApp() {
            const phoneNumber = '6281944518617'; 
            const message = encodeURIComponent(
                `Halo, saya ingin menanyakan tentang pesanan saya.\n` +
                `Invoice: {{ $order->invoice_number }}\n` +
                `Meja: {{ $order->table->no_meja ?? 'N/A' }}`
            );
            window.open(`https://wa.me/${phoneNumber}?text=${message}`, '_blank');
        }

        // Auto refresh setiap 30 detik jika pesanan masih dalam proses
        @if(in_array($order->status, ['paid', 'processing']))
        setInterval(() => {
            location.reload();
        }, 30000);
        @endif
    </script>
</body>
</html>