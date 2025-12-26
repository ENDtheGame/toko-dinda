<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Penjualan Harian') }}
            </h2>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
        <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-bold text-amber-800 dark:text-amber-200 uppercase">
                        Penting: Prosedur Tukar Barang
                    </h3>
                    <p class="text-sm text-amber-700 dark:text-amber-300 mt-1">
                        Jika ada pelanggan ingin tukar barang/retur, ikuti langkah berikut agar <strong>Stok Tidak
                            Selisih</strong>:
                    </p>
                    <ul class="list-decimal pl-5 mt-2 text-sm text-amber-700 dark:text-amber-300 space-y-1">
                        <li>Cari transaksi yang dimaksud pada tabel di bawah.</li>
                        <li>Klik tombol <span class="px-1 bg-red-100 text-red-700 rounded font-bold">Hapus</span> (ikon
                            tempat sampah).</li>
                        <li>Sistem akan <strong>mengembalikan stok</strong> secara otomatis ke database.</li>
                        <li>Input ulang barang yang baru di menu <strong>Kasir</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm mb-6">
                <form action="{{ route('reports.daily') }}" method="GET" class="flex items-end space-x-4">
                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih
                            Tanggal</label>
                        <input type="date" name="date" value="{{ $date }}"
                            class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500">
                    </div>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Filter</button>
                    <a href="{{ route('reports.daily') }}" class="text-gray-500 hover:underline text-sm pb-2">Hari
                        Ini</a>
                    <a href="{{ route('reports.daily.pdf', ['date' => $date]) }}"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Document-download">
                            </path>
                        </svg>
                        Cetak ke PDF
                    </a>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-green-100 border-l-4 border-green-500 p-4 rounded shadow-sm">
                    <p class="text-sm text-green-700 uppercase font-bold">Total Pendapatan
                        ({{ \Carbon\Carbon::parse($date)->format('d/m/Y') }})</p>
                    <p class="text-3xl font-extrabold text-green-900">Rp {{ number_format($totalSales, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-blue-100 border-l-4 border-blue-500 p-4 rounded shadow-sm">
                    <p class="text-sm text-blue-700 uppercase font-bold">Total Transaksi</p>
                    <p class="text-3xl font-extrabold text-blue-900">{{ $sales->count() }} Invoice</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700">
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500">No. Invoice</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500">Waktu</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500 text-right">Total Bayar
                                </th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                    <td class="px-4 py-4 font-mono text-sm dark:text-gray-200">
                                        {{ $sale->invoice_number }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $sale->created_at->format('H:i') }} WIB
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold text-green-600">
                                        Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <button class="text-blue-500 hover:underline text-sm">Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Tidak ada
                                        transaksi ditemukan untuk tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
