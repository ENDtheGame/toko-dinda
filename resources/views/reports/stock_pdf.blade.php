<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Inventaris Stok') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reports.stock.pdf') }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                    Cetak PDF Stok
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Total Jenis Produk</p>
                    <p class="text-2xl font-bold dark:text-white">{{ $totalProducts }} Item</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-red-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Perlu Re-stock ( < 10 )</p>
                            <p class="text-2xl font-bold text-red-600">{{ $lowStockProducts->count() }} Item</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500 uppercase">Status Gudang</p>
                    <p class="text-2xl font-bold text-green-600">Aktif</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-4 dark:text-white">Daftar Produk Stok Rendah</h3>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700">
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500">Kode</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500">Nama Produk</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500 text-center">Sisa Stok
                                </th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500">Satuan</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500 text-right">Harga Jual
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse($lowStockProducts as $product)
                                <tr class="hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    <td class="px-4 py-4 font-mono text-sm dark:text-gray-300">{{ $product->code }}</td>
                                    <td class="px-4 py-4 font-bold dark:text-white">{{ $product->name }}</td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-black">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $product->unit }}
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold dark:text-gray-200">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500 italic">
                                        Luar biasa! Semua stok barang mencukupi.
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
