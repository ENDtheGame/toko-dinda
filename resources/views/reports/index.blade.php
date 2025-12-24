<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Penjualan Harian') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('reports.export.csv') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                    Export CSV
                </a>
                <a href="{{ route('reports.daily.pdf') }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                    Cetak PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-blue-700 font-bold uppercase tracking-wider">Total Omzet Hari Ini</p>
                        <p class="text-3xl font-black text-blue-900">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Tanggal</p>
                        <p class="font-bold text-gray-800">{{ now()->format('d F Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">No. Invoice</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">Kasir</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">Jam</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600">Items</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase text-gray-600 text-right">Total Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                                    <td class="px-4 py-4 font-mono text-sm font-bold text-blue-600">{{ $sale->invoice_number }}</td>
                                    <td class="px-4 py-4 dark:text-gray-300">{{ $sale->user->name ?? 'Admin' }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-500">{{ $sale->created_at->format('H:i') }}</td>
                                    <td class="px-4 py-4">
                                        <ul class="text-xs text-gray-600 dark:text-gray-400">
                                            @foreach($sale->saleDetails as $detail)
                                                <li>• {{ $detail->product->name }} (x{{ $detail->quantity }})</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="px-4 py-4 text-right font-bold text-green-600">
                                        Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-gray-500 italic">
                                        Belum ada transaksi untuk hari ini.
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
