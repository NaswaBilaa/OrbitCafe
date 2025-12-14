<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Receipt</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            text-align: center;
        }

        #receipt {
            background: white;
            width: 350px;
            padding: 25px 20px;
            margin: 0 auto 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-family: 'Courier New', monospace;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .shop-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }

        .shop-info {
            font-size: 11px;
            line-height: 1.6;
            color: #333;
        }

        .divider {
            border: none;
            border-top: 2px dotted #000;
            margin: 12px 0;
        }

        .info-section {
            font-size: 11px;
            margin-bottom: 12px;
            text-align: left;
        }

        .info-row {
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 11px;
        }

        table thead th {
            font-weight: bold;
            padding: 8px 0;
            border-bottom: 1px solid #000;
        }

        table thead th:first-child {
            text-align: left;
            padding-left: 0;
        }

        table thead th:nth-child(2) {
            text-align: center;
        }

        table thead th:last-child {
            text-align: right;
            padding-right: 0;
        }

        table tbody td {
            padding: 6px 0;
            vertical-align: top;
        }

        table tbody td:first-child {
            text-align: left;
            padding-left: 0;
        }

        table tbody td:nth-child(2) {
            text-align: center;
        }

        table tbody td:last-child {
            text-align: right;
            padding-right: 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 12px;
            font-size: 11px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total-row.main {
            font-size: 16px;
            font-weight: bold;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 2px solid #000;
        }

        .thank-you {
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            margin: 15px 0 10px;
        }

        .invoice-code {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin: 12px 0;
            letter-spacing: 1px;
        }

        .barcode-container {
            text-align: center;
            margin: 15px 0;
            padding: 10px 0;
        }

        .barcode {
            display: inline-flex;
            gap: 2px;
            justify-content: center;
        }

        .bar {
            width: 2px;
            height: 40px;
            background: #000;
        }

        .bar.wide {
            width: 4px;
        }

        .footer {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 12px;
            line-height: 1.5;
        }

        .download-btn {
            background: #B97D0E;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            margin: 10px;
            transition: background 0.3s;
        }

        .download-btn:hover {
            background: #a06b0b;
        }

        .back-btn {
            background: #6b7280;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 25px;
            cursor: pointer;
            margin: 10px;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn:hover {
            background: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="receipt">
            <!-- Header -->
            <div class="header">
                <div class="shop-name">ORBIT COFFEE</div>
                <div class="shop-info">
                    Coffee & Eatery<br>
                    Jl. Contoh No. 123<br>
                    Jakarta - 12345<br>
                    Tel.: +62-XXX-XXXX-XXXX
                </div>
            </div>

            <hr class="divider">

            <!-- Transaction Info -->
            <div class="info-section">
                <div class="info-row">Invoice Number: {{ $order->invoice_number }}</div>
                <div class="info-row">Date: {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y h:i A') }}</div>
                <div class="info-row">Customer: {{ $order->nama_lengkap }}</div>
                <div class="info-row">Phone: {{ $order->no_telepon }}</div>
            </div>

            <hr class="divider">

            <!-- Items Table -->
            <table>
                <thead>
                    <tr>
                        <th style="width: 50%;">Name</th>
                        <th style="width: 15%;">Qty</th>
                        <th style="width: 35%;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->menu->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <hr class="divider">

            <!-- Total Section -->
            <div class="total-section">
                <div class="total-row main">
                    <span>Price</span>
                    <span>Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
                
                <div class="total-row">
                    <span>{{ strtolower($payment->payment_method ?? 'qris') }}</span>
                    <span>Rp{{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            <hr class="divider">

            <!-- Thank You -->
            <div class="thank-you">THANK YOU!</div>

            <!-- Invoice Code -->
            <div class="invoice-code">{{ $order->invoice_number }}</div>

            <!-- Barcode -->
            <div class="barcode-container">
                <div class="barcode">
                    @for($i = 0; $i < 45; $i++)
                        <div class="bar {{ $i % 3 == 0 ? 'wide' : '' }}"></div>
                    @endfor
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                This receipt is valid as proof of payment<br>
                {{ now()->format('Y') }} © Orbit Coffee
            </div>
        </div>

        <!-- Action Buttons -->
        <div>
            <button class="download-btn" onclick="downloadReceipt()">Download as Image</button>
            <a href="{{ route('order.success', ['invoice' => $order->invoice_number]) }}" class="back-btn">Back</a>
        </div>
    </div>

    <script>
        function downloadReceipt() {
            const receipt = document.getElementById('receipt');
            const button = document.querySelector('.download-btn');
            
            // Disable button
            button.disabled = true;
            button.textContent = 'Generating...';
            
            html2canvas(receipt, {
                scale: 3, // Higher quality
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true
            }).then(canvas => {
                // Convert to image
                const link = document.createElement('a');
                link.download = 'receipt-{{ $order->invoice_number }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
                
                // Re-enable button
                button.disabled = false;
                button.textContent = 'Download as Image';
            }).catch(error => {
                console.error('Error generating image:', error);
                alert('Failed to generate receipt image');
                button.disabled = false;
                button.textContent = 'Download as Image';
            });
        }
    </script>
</body>
</html>