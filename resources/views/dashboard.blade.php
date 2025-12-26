<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ringkasan Bisnis Warung') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($lowStockProducts->count() > 0)
                <div
                    class="mb-8 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-r-lg shadow-md animate-pulse">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3 text-red-800 dark:text-red-300 font-bold uppercase tracking-wide text-sm">
                            Perhatian: Ada {{ $lowStockCount }} barang yang hampir habis! Segera belanja stok baru.
                        </div>
                    </div>
                </div>
            @endif
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-blue-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Jenis Barang</div>
                    <div class="text-2xl font-bold dark:text-white">{{ $totalProducts }}</div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-green-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Penjualan Hari Ini</div>
                    <div class="text-2xl font-bold dark:text-white">Rp {{ number_format($dailySales, 0, ',', '.') }}
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-purple-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Penjualan Bulan Ini</div>
                    <div class="text-2xl font-bold dark:text-white">Rp {{ number_format($monthlySales, 0, ',', '.') }}
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-red-500">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Barang Stok Rendah</div>
                    <div class="text-2xl font-bold text-red-600">{{ $lowStockCount }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg dark:text-white text-indigo-600 uppercase tracking-wider text-sm">
                            🔥 Barang Paling Laris</h3>
                    </div>
                    <div class="overflow-x-auto text-sm">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="p-3 text-gray-600 dark:text-gray-300">Produk</th>
                                    <th class="p-3 text-gray-600 dark:text-gray-300 text-center">Terjual</th>
                                </tr>
                            </thead>
                            <tbody class="dark:text-gray-200">
                                @forelse($topProducts as $item)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="p-3">
                                            {{ $item->product ? $item->product->name : 'Produk Dihapus' }}</td>
                                        <td class="p-3 text-center">
                                            <span
                                                class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full font-bold">
                                                {{ $item->total_qty }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="p-4 text-center text-gray-500">Belum ada transaksi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg dark:text-white text-red-600 uppercase tracking-wider text-sm">⚠️
                            Stok Hampir Habis</h3>
                        <a href="{{ route('products.index') }}" class="text-xs text-blue-500 hover:underline">Lihat
                            Semua</a>
                    </div>
                </div>
                <div class="overflow-x-auto text-sm">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="p-3 text-gray-600 dark:text-gray-300">Produk</th>
                                <th class="p-3 text-gray-600 dark:text-gray-300 text-center">Stok Tersisa</th>
                            </tr>
                        </thead>
                        <tbody class="dark:text-gray-200">
                            @forelse($lowStockProducts as $product)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="p-3">{{ $product->name }}</td>
                                    <td class="p-3 text-center">
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full font-bold">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="p-4 text-center text-gray-500">Semua stok dalam
                                        kondisi aman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
