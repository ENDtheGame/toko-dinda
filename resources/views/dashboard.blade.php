<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ringkasan Bisnis Warung') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-blue-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Jenis Barang</div>
                    <div class="text-2xl font-bold dark:text-white">{{ $totalProducts }}</div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-green-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Penjualan Hari Ini</div>
                    <div class="text-2xl font-bold dark:text-white">Rp {{ number_format($dailySales, 0, ',', '.') }}</div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-purple-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Penjualan Bulan Ini</div>
                    <div class="text-2xl font-bold dark:text-white">Rp {{ number_format($monthlySales, 0, ',', '.') }}</div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-red-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Barang Stok Rendah</div>
                    <div class="text-2xl font-bold text-red-600">{{ $lowStockCount }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-bold text-lg mb-4 dark:text-white">Tren Penjualan 7 Hari Terakhir</h3>
                    <canvas id="salesChart" height="200"></canvas>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg dark:text-white text-red-600">Peringatan Stok Rendah</h3>
                        <a href="{{ route('products.index') }}" class="text-sm text-blue-500 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                                <tr>
                                    <th class="p-2 text-gray-600">Barang</th>
                                    <th class="p-2 text-gray-600 text-center">Sisa</th>
                                    <th class="p-2 text-gray-600 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="dark:text-gray-200">
                                @forelse($lowStockProducts as $product)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="p-2">{{ $product->name }}</td>
                                    <td class="p-2 text-center font-bold text-red-500">{{ $product->stock }} {{ $product->unit }}</td>
                                    <td class="p-2 text-center">
                                        <a href="{{ route('products.edit', $product->id) }}" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs">Isi Stok</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center text-gray-500">Semua stok aman.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: {!! json_encode($totals) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1,
                    fill: true,
                    backgroundColor: 'rgba(75, 192, 192, 0.1)'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>
