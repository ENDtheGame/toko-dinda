<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Stok Kritis') }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('reports.stock.csv') }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                    Export Excel/CSV
                </a>
                <a href="{{ route('reports.stock.pdf') }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                    Cetak PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Produk Aktif</p>
                    <p class="text-3xl font-bold dark:text-white">{{ $totalProducts }} <span
                            class="text-sm font-normal text-gray-400">Jenis Barang</span></p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Barang Perlu Restock</p>
                    <p class="text-3xl font-bold text-red-600">{{ $lowStockProducts->count() }} <span
                            class="text-sm font-normal text-gray-400">Barang</span></p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold dark:text-white text-gray-700 italic underline">Daftar Barang Harus
                            Segera Dibeli</h3>
                        <span class="text-xs text-gray-400 font-mono">Standar minimal stok: < 10 unit</span>
                    </div>

                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700 text-xs uppercase font-bold text-gray-500">
                                <th class="px-6 py-4">Kode Barang</th>
                                <th class="px-6 py-4">Nama Barang</th>
                                <th class="px-6 py-4 text-center">Sisa Stok</th>
                                <th class="px-6 py-4">Satuan</th>
                                <th class="px-6 py-4 text-right">Harga Jual</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse ($lowStockProducts as $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                    <td class="px-6 py-4 font-mono text-xs dark:text-gray-300">{{ $product->code }}</td>
                                    <td class="px-6 py-4 font-bold dark:text-white">{{ $product->name }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="text-lg font-bold {{ $product->stock < 5 ? 'text-red-600' : 'text-orange-500' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $product->unit }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium dark:text-gray-200">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($product->stock < 5)
                                            <span
                                                class="px-2 py-1 text-[10px] bg-red-100 text-red-600 font-bold uppercase rounded">Gawat</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-[10px] bg-orange-100 text-orange-600 font-bold uppercase rounded">Peringatan</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                        Semua stok masih aman! Belum ada barang yang perlu dibeli.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
