<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Barang Masuk (Restok)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    {{ session('error') }}
                </div>
            @endif
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b">
                        <tr>
                            <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Tanggal</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Nama Barang</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Jumlah Masuk</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Harga Beli</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Supplier</th>
                            <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-gray-700">
                        @foreach ($histories as $history)
                            <tr class="dark:text-white">
                                <td class="px-6 py-4">{{ $history->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 font-bold">{{ $history->product->name ?? 'Produk Terhapus' }}</td>
                                <td class="px-6 py-4 text-green-600 font-bold">+ {{ $history->quantity_added }} Pcs</td>
                                <td class="px-6 py-4">Rp
                                    {{ number_format($history->actual_purchase_price, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $history->supplier ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <form action="{{ route('stock-histories.destroy', $history->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin membatalkan restok ini? Stok akan dikurangi otomatis.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md text-xs font-bold transition">
                                            ✕ Batalkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $histories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
