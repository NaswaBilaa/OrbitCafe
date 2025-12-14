<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1a1a1a;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        .summary-item .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 25px 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #333;
            color: #1a1a1a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        table thead {
            background-color: #f5f5f5;
        }
        table th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background-color: #fafafa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .status-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .status-item {
            display: table-cell;
            width: 25%;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            text-align: center;
        }
        .status-item .status-label {
            font-size: 10px;
            color: #666;
            text-transform: capitalize;
        }
        .status-item .status-value {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .payment-grid {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .payment-item {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PENJUALAN</h1>
        <p>{{ config('app.name', 'Restaurant') }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d F Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d F Y') }}</p>
        <p>Dicetak: {{ now()->format('d F Y H:i:s') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Pesanan</div>
            <div class="value">{{ number_format($salesData['total_orders']) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Penjualan</div>
            <div class="value">Rp {{ number_format($salesData['total_sales'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Rata-rata Order</div>
            <div class="value">Rp {{ number_format($salesData['average_order'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Status Filter</div>
            <div class="value">{{ ucfirst($status) }}</div>
        </div>
    </div>

    <!-- Orders by Status -->
    <div class="section-title">Pesanan Berdasarkan Status</div>
    <div class="status-grid">
        @foreach($salesData['orders_by_status'] as $orderStatus => $count)
            <div class="status-item">
                <div class="status-label">{{ ucfirst($orderStatus) }}</div>
                <div class="status-value">{{ $count }}</div>
            </div>
        @endforeach
    </div>

    <!-- Top Selling Menus -->
    <div class="section-title">Menu Terlaris (Top 10)</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;" class="text-center">Id Menu</th>
                <th style="width: 35%;">Nama Menu</th>
                <th style="width: 25%;" class="text-right">Jumlah Terjual</th>
                <th style="width: 25%;" class="text-right">Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData['top_selling_menus'] as $index => $menu)
                <tr>
                    <td class="text-center">{{ $index }}</td>
                    <td>{{ $menu['menu_name'] }}</td>
                    <td class="text-right">{{ $menu['quantity'] }}</td>
                    <td class="text-right">Rp {{ number_format($menu['total_sales'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Page Break -->
    <div class="page-break"></div>

    <!-- Daily Sales -->
    <div class="section-title">Penjualan Harian</div>
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Tanggal</th>
                <th style="width: 30%;" class="text-right">Jumlah Pesanan</th>
                <th style="width: 30%;" class="text-right">Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData['orders_by_date'] as $date => $data)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</td>
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">Rp {{ number_format($data['total'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #e0e0e0; font-weight: bold;">
                <td>TOTAL</td>
                <td class="text-right">{{ $salesData['total_orders'] }}</td>
                <td class="text-right">Rp {{ number_format($salesData['total_sales'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Payment Methods -->
    @if(!empty($salesData['orders_by_payment_method']))
        <div class="section-title">Metode Pembayaran</div>
        <div class="payment-grid">
            @foreach($salesData['orders_by_payment_method'] as $method => $data)
                <div class="payment-item">
                    <div style="font-weight: bold; margin-bottom: 5px;">{{ strtoupper($method) }}</div>
                    <div style="font-size: 16px; font-weight: bold; margin: 5px 0;">{{ $data['count'] }} Pesanan</div>
                    <div style="color: #666;">Rp {{ number_format($data['total'], 0, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem {{ config('app.name', 'Restaurant') }}</p>
        <p>&copy; {{ now()->year }} {{ config('app.name', 'Restaurant') }}. All rights reserved.</p>
    </div>
</body>
</html>