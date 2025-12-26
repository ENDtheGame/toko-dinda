<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        openModal: false,
        invoiceTitle: '',
        modalItems: [],
        showDetail(invoice, items) {
            this.invoiceTitle = invoice;
            this.modalItems = items;
            this.openModal = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm mb-6">
                <form action="{{ route('reports.monthly') }}" method="GET" class="flex items-end space-x-4">
                    <div class="w-48">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih
                            Bulan</label>
                        <select name="month"
                            class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 text-sm">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-32">
                        <label
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 text-sm">Tahun</label>
                        <input type="number" name="year" value="{{ $year }}"
                            class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 text-sm">
                    </div>

                    <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-bold text-sm h-10 transition">
                        Lihat Laporan
                    </button>

                    @if ($sales->count() > 0)
                        <a href="{{ route('reports.monthly.pdf', ['month' => $month, 'year' => $year]) }}"
                            target="_blank"
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 font-bold text-sm h-10 flex items-center transition">
                            Cetak PDF
                        </a>
                    @endif
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50 dark:bg-gray-900 border-b dark:border-gray-700 text-gray-500 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4 text-right">Total Transaksi</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse ($sales as $sale)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $sale->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs font-bold text-indigo-600">
                                        {{ $sale->invoice_number }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 dark:text-white text-sm">
                                        Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center items-center space-x-2">
                                            <button type="button"
                                                @click="showDetail('{{ $sale->invoice_number }}', [
        @foreach ($sale->details as $detail)
        {
            name: '{{ $detail->product ? $detail->product->name : 'Produk Telah Dihapus' }}',
            qty: {{ $detail->quantity }},
            price: {{ $detail->price }}
        }, @endforeach
    ])"
                                                class="p-2 bg-blue-100 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition"
                                                title="Preview Detail">

                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>

                                            </button>
                                            <form action="{{ route('reports.sales.destroy', $sale->id) }}"
                                                method="POST" id="delete-form-{{ $sale->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $sale->id }})"
                                                    class="p-2 bg-red-100 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-4 text-center text-gray-400">Data Kosong</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-show="openModal"
            class="fixed inset-0 z-[999] overflow-y-auto flex items-center justify-center bg-black bg-opacity-75 p-4"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" style="display: none;">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden"
                @click.away="openModal = false">
                <div
                    class="p-5 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900">
                    <h3 class="text-lg font-bold dark:text-white">Detail Invoice: <span x-text="invoiceTitle"
                            class="text-indigo-600"></span></h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="p-6">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-sm">Nama Barang</th>
                                <th class="px-4 py-2 text-center text-sm">Qty</th>
                                <th class="px-4 py-2 text-right text-sm">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="item in modalItems" :key="item.id">
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-3 dark:text-gray-300" x-text="item.name"></td>
                                    <td class="px-4 py-3 text-center dark:text-gray-300" x-text="item.qty"></td>
                                    <td class="px-4 py-3 text-right dark:text-gray-300 font-bold"
                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(item.price * item.qty)">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-900 text-right">
                    <button @click="openModal = false"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 font-bold text-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: "Stok akan kembali otomatis!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
</x-app-layout>
