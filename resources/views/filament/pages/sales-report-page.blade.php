<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Filter Form -->
        <x-filament::card>
            {{ $this->form }}
        </x-filament::card>

        @if(!empty($salesData))
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-filament::card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pesanan</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                {{ number_format($salesData['total_orders']) }}
                            </p>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                            <x-heroicon-o-shopping-bag class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Penjualan</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($salesData['total_sales'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <x-heroicon-o-currency-dollar class="w-8 h-8 text-green-600 dark:text-green-400" />
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata Pesanan</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($salesData['average_order'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                            <x-heroicon-o-chart-bar class="w-8 h-8 text-yellow-600 dark:text-yellow-400" />
                        </div>
                    </div>
                </x-filament::card>

                <x-filament::card>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Periode</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($data['date_from'])->format('d M') }} - 
                                {{ \Carbon\Carbon::parse($data['date_to'])->format('d M Y') }}
                            </p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <x-heroicon-o-calendar class="w-8 h-8 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                </x-filament::card>
            </div>

            <!-- Orders by Status -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">Pesanan Berdasarkan Status</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($salesData['orders_by_status'] as $status => $count)
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($status) }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $count }}</p>
                        </div>
                    @endforeach
                </div>
            </x-filament::card>

            <!-- Top Selling Menus -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">Menu Terlaris (Top 10)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-3 px-4">Peringkat</th>
                                <th class="text-left py-3 px-4">Nama Menu</th>
                                <th class="text-right py-3 px-4">Jumlah Terjual</th>
                                <th class="text-right py-3 px-4">Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesData['top_selling_menus'] as $index => $menu)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-lg">{{ $loop->iteration }}</span>
                                    </td>
                                    <td class="py-3 px-4">{{ $menu['menu_name'] }}</td>
                                    <td class="py-3 px-4 text-right">{{ $menu['quantity'] }}</td>
                                    <td class="py-3 px-4 text-right font-semibold">
                                        Rp {{ number_format($menu['total_sales'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::card>

            <!-- Orders by Date -->
            <x-filament::card>
                <h3 class="text-lg font-semibold mb-4">Penjualan Harian</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b dark:border-gray-700">
                                <th class="text-left py-3 px-4">Tanggal</th>
                                <th class="text-right py-3 px-4">Jumlah Pesanan</th>
                                <th class="text-right py-3 px-4">Total Penjualan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesData['orders_by_date'] as $date => $data)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="py-3 px-4">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td>
                                    <td class="py-3 px-4 text-right">{{ $data['count'] }}</td>
                                    <td class="py-3 px-4 text-right font-semibold">
                                        Rp {{ number_format($data['total'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::card>

            <!-- Payment Methods -->
            @if(!empty($salesData['orders_by_payment_method']))
                <x-filament::card>
                    <h3 class="text-lg font-semibold mb-4">Metode Pembayaran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($salesData['orders_by_payment_method'] as $method => $data)
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ strtoupper($method) }}</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $data['count'] }} Pesanan</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Rp {{ number_format($data['total'], 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </x-filament::card>
            @endif
        @endif
    </div>
</x-filament-panels::page>