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
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10 5V10L13.3333 11.6667" stroke="#402F0B" stroke-opacity="0.3" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9.99984 18.3332C14.6022 18.3332 18.3332 14.6022 18.3332 9.99984C18.3332 5.39746 14.6022 1.6665 9.99984 1.6665C5.39746 1.6665 1.6665 5.39746 1.6665 9.99984C1.6665 14.6022 5.39746 18.3332 9.99984 18.3332Z" stroke="#402F0B" stroke-opacity="0.3" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span class="step-label">Ready</span>
                    </div>
                    
                    {{-- 4. SERVING --}}
                    <div class="progress-step {{ in_array($order->status, ['serving', 'completed']) ? 'active' : '' }} {{ $order->status === 'serving' ? 'current' : '' }} {{ $order->status === 'completed' ? 'completed' : '' }}">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M23.5325 11.23H21.9875L19.0609 8.30339C18.9958 8.23854 18.9104 8.1888 18.8131 8.15966L17.3761 7.7286L17.5493 5.69493H17.959C18.0608 5.69493 18.1587 5.66258 18.2308 5.6049C18.3029 5.54722 18.3433 5.4691 18.3433 5.38747V4.15747C18.3429 3.96673 18.2689 3.78088 18.1314 3.62508C17.9937 3.46944 17.7992 3.35164 17.5747 3.28786V1.69743C17.5743 1.45284 17.4526 1.21845 17.2365 1.04544C17.0202 0.872559 16.7272 0.775207 16.4215 0.774902H6.42786C6.12211 0.775208 5.82894 0.872559 5.61287 1.04544C5.39658 1.21848 5.27508 1.45286 5.2747 1.69743V3.28786C5.05001 3.35164 4.85546 3.46944 4.71796 3.62508C4.58025 3.78088 4.50625 3.96673 4.50586 4.15747V5.38747C4.50586 5.4691 4.54649 5.54722 4.61858 5.6049C4.69068 5.66258 4.78834 5.69493 4.89038 5.69493H5.30008L5.55433 8.68106C5.16465 8.89591 4.92068 9.24243 4.89285 9.6204C4.86519 9.99821 5.05688 10.3652 5.41243 10.615C5.08055 10.848 4.89041 11.1841 4.89041 11.5375C4.89041 11.8908 5.08057 12.2269 5.41243 12.46C5.08055 12.693 4.89041 13.0292 4.89041 13.3824C4.89041 13.7358 5.08057 14.072 5.41243 14.305C5.11622 14.5133 4.9314 14.8049 4.89632 15.1193C4.86141 15.4337 4.9791 15.7468 5.22477 15.9937C5.47062 16.2406 5.8256 16.4025 6.21642 16.4456L6.4289 18.9384C6.44263 19.0996 6.61009 19.2249 6.81228 19.225H16.0374C16.2394 19.225 16.407 19.0997 16.4208 18.9384L16.5436 17.5005L19.033 18.1091C19.6055 18.2488 20.2251 18.188 20.7386 17.9421L21.9094 17.3799H23.5327C23.8508 17.3798 24.1089 17.1733 24.1093 16.9186V11.6913C24.1089 11.4368 23.8509 11.2303 23.5327 11.2302L23.5325 11.23ZM6.42802 1.3898H16.4217C16.6338 1.38995 16.8058 1.52759 16.806 1.69727V3.23473H6.04331V1.69727C6.04369 1.52759 6.21554 1.38995 6.42783 1.3898H6.42802ZM5.27484 4.1573C5.27503 3.98762 5.44708 3.84999 5.65918 3.84984H17.1904C17.4027 3.84999 17.5746 3.98762 17.5749 4.1573V5.07984H5.27465L5.27484 4.1573ZM6.07079 5.69476H16.7788L16.6247 7.50293L15.8209 7.26184C15.7561 7.24231 15.6874 7.23223 15.618 7.23223H13.1543C12.7291 7.23269 12.3189 7.35843 12.0018 7.58517C11.6848 7.81207 11.4832 8.12427 11.4355 8.46224H6.4277C6.38689 8.46224 6.34645 8.46392 6.3064 8.46636L6.07079 5.69476ZM16.0713 13.9975H9.29638C9.47452 13.7517 9.54052 13.4639 9.48311 13.1834C9.4255 12.9031 9.24812 12.6474 8.9811 12.46C9.31298 12.2268 9.50332 11.8907 9.50332 11.5375C9.50332 11.1841 9.31297 10.8479 8.9811 10.6149C9.24814 10.4275 9.42552 10.1718 9.48311 9.89136C9.54052 9.61091 9.47452 9.32328 9.29638 9.07746H11.5244C11.6439 9.34693 11.8648 9.58039 12.1566 9.7455C12.4485 9.91076 12.7971 9.99956 13.1546 10H15.0629L16.2868 11.4688L16.0713 13.9975ZM6.42772 12.1524C6.17939 12.1521 5.94669 12.0558 5.80287 11.8941C5.65887 11.7323 5.62129 11.525 5.70216 11.3373C5.78303 11.1495 5.97205 11.0045 6.21007 10.9479C6.28064 10.9311 6.35389 10.9226 6.4277 10.9224H7.9652C8.23986 10.9224 8.49352 11.0397 8.63087 11.23C8.7682 11.4203 8.7682 11.6547 8.63087 11.8449C8.49355 12.0352 8.23987 12.1524 7.9652 12.1524L6.42772 12.1524ZM6.42772 9.07746H7.96522C8.23988 9.07746 8.49354 9.19464 8.6309 9.38492C8.76825 9.5752 8.76823 9.80973 8.6309 10C8.49357 10.1903 8.23989 10.3075 7.96522 10.3075H6.42772C6.15307 10.3075 5.89921 10.1903 5.76186 10C5.6245 9.80972 5.62453 9.5752 5.76186 9.38492C5.89918 9.19465 6.15305 9.07746 6.42772 9.07746ZM5.65888 13.3825C5.65907 13.2196 5.74013 13.0632 5.88433 12.948C6.02852 12.8326 6.22383 12.7678 6.42773 12.7676H7.96523C8.23989 12.7676 8.49355 12.8848 8.63091 13.0751C8.76826 13.2653 8.76824 13.4997 8.63091 13.69C8.49358 13.8804 8.2399 13.9976 7.96523 13.9976H6.42773C6.22384 13.9973 6.02853 13.9325 5.88433 13.8173C5.74013 13.7019 5.65907 13.5457 5.65888 13.3825ZM5.65888 15.2276C5.65907 15.0645 5.74013 14.9082 5.88433 14.7929C6.02852 14.6777 6.22383 14.6127 6.42773 14.6125H7.19639C7.47104 14.6125 7.7249 14.7297 7.86226 14.92C7.99958 15.1103 7.99958 15.3448 7.86226 15.5351C7.72493 15.7253 7.47106 15.8425 7.19639 15.8425H6.42773C6.22384 15.8424 6.02853 15.7775 5.88433 15.6622C5.74013 15.547 5.65907 15.3906 5.65888 15.2276ZM15.6779 18.61H7.17108L6.9876 16.4575L7.19645 16.4576C7.55808 16.4578 7.90807 16.356 8.18522 16.1702C8.46217 15.9843 8.64852 15.7261 8.7111 15.4413C8.77368 15.1564 8.70862 14.8629 8.52742 14.6126H16.0191L15.6779 18.61ZM23.34 16.7651H21.8558C21.7512 16.7651 21.6488 16.7878 21.5592 16.8309L20.3427 17.4147C20.0159 17.5714 19.6215 17.61 19.2572 17.521L16.5969 16.8706L17.0581 11.4584C17.0664 11.357 17.0329 11.2561 16.9627 11.1714L15.6454 9.59061H15.6453C15.5384 9.46213 15.3582 9.38507 15.1654 9.38522H13.1541C12.8108 9.38522 12.4936 9.23874 12.3219 9.00086C12.1503 8.76297 12.1503 8.47 12.3219 8.23211C12.4936 7.99422 12.8108 7.84774 13.1541 7.84774H15.5829L18.4935 8.72098L21.4449 11.6722C21.554 11.7819 21.7198 11.8455 21.895 11.8452H23.3401L23.34 16.7651Z" stroke="#402F0B" stroke-opacity="0.3" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round"/>            
                            </svg>
                        </div>
                        <span class="step-label">Serving</span>
                    </div>

                    {{-- 5. COMPLETED --}}
                    <div class="progress-step {{ $order->status === 'completed' ? 'active current' : '' }}">
                        <div class="step-icon">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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

        @if(in_array($order->status, ['paid', 'processing']))
        setInterval(() => {
            location.reload();
        }, 30000);
        @endif
    </script>
</body>
</html>