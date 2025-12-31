<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Produk Grosir') }}
            </h2>
        </div>
    </x-slot>
    @php
        $lowStockProducts = \App\Models\Product::whereRaw('stock <= min_stock')->get();
    @endphp
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Success --}}
            @if (session('success'))
                <div id="success-alert"
                    class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                {{-- Toolbar: Search & Buttons --}}
                <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <form action="{{ route('products.index') }}" method="GET" class="w-full md:w-1/3">
                        <div class="relative">
                            <input type="text" id="search-input" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama atau kategori..."
                                class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:bg-gray-700 dark:text-white">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </form>

                    <div class="flex gap-2">
                        <button type="button" onclick="confirmBulkDelete()"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                            🗑️ Hapus Terpilih
                        </button>
                        <button onclick="openModal('modalTambah')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                            + Tambah Produk Baru
                        </button>
                        <button type="button" onclick="openModal('modalRestok')"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                            <i class="fas fa-plus-circle"></i> Restok Barang
                        </button>
                    </div>
                </div>
                @php
                    $lowStockProducts = \App\Models\Product::where('stock', '<=', 5)->get();
                @endphp

                @if ($lowStockProducts->count() > 0)
                    <div class="mb-6 p-4 bg-amber-50 border-l-4 border-amber-400 text-amber-800 rounded-r-md shadow-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-bold">Peringatan Stok Rendah:</span>
                        </div>
                        <ul class="mt-2 ml-7 list-disc list-inside text-sm">
                            @foreach ($lowStockProducts as $low)
                                <li>{{ $low->name }} (Sisa: <span
                                        class="font-bold text-red-600">{{ $low->stock }}</span>)</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                {{-- Table Section --}}
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border dark:border-gray-700">
                    <form id="formBulkDelete" action="{{ route('products.bulkDelete') }}" method="POST">
                        @csrf
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                                <tr>
                                    <th class="px-6 py-3 w-10"><input type="checkbox" id="selectAllProduct"
                                            class="rounded border-gray-300"></th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">No</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Nama Barang</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Kategori</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Brand</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Harga Jual</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-center">Stok
                                        Keseluruhan</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-center">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700" id="product-table-body">
                                @forelse ($products as $key => $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <td class="px-6 py-4"><input type="checkbox" name="ids[]"
                                                value="{{ $product->id }}"
                                                class="product-checkbox rounded border-gray-300"></td>
                                        <td class="px-6 py-4 dark:text-white">{{ $products->firstItem() + $key }}</td>
                                        <td class="px-6 py-4 font-bold dark:text-white">{{ $product->name }}</td>
                                        <td class="px-6 py-4 dark:text-gray-300">{{ $product->category->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 dark:text-gray-300">{{ $product->brand->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 dark:text-white text-right">
                                            @php
                                                // Ambil nama satuan terkecil (Induk/Dasar)
                                                // Jika satuan produk punya parent (misal: Karung -> induknya Kg), ambil nama induknya.
                                                // Jika tidak punya parent, ambil nama satuan itu sendiri.
                                                $satuanTerkecil =
                                                    $product->unit && $product->unit->parent
                                                        ? $product->unit->parent->name
                                                        : ($product->unit
                                                            ? $product->unit->name
                                                            : 'Pcs');

                                                $namaSatuanBesar = $product->unit ? $product->unit->name : 'Pcs';
                                            @endphp

                                            <div class="text-sm font-bold">
                                                Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                                <span
                                                    class="text-[10px] font-normal text-gray-500">/{{ $satuanTerkecil }}</span>
                                            </div>

                                            {{-- Keterangan Harga Satuan Besar (Misal: Karung/Dus) --}}
                                            @if ($product->unit && $product->unit->base_quantity > 1)
                                                <div class="text-[10px] text-blue-500 italic">
                                                    (Rp
                                                    {{ number_format($product->selling_price * $product->unit->base_quantity, 0, ',', '.') }}
                                                    /{{ $namaSatuanBesar }})
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $stokUtama = $product->stock;
                                                $konversi = $product->unit ? $product->unit->base_quantity : 1;
                                                $namaSatuanBesar = $product->unit ? $product->unit->name : '';
                                                $namaSatuanDasar =
                                                    $product->unit && $product->unit->parent
                                                        ? $product->unit->parent->name
                                                        : ($product->unit
                                                            ? $product->unit->name
                                                            : 'Pcs');

                                                // Hitung jumlah satuan besar (misal: Karung)
                                                $jumlahSatuanBesar = $konversi > 1 ? floor($stokUtama / $konversi) : 0;
                                                // Hitung sisa ecerannya (misal: sisa Kg)
                                                $sisaEceran = $konversi > 1 ? $stokUtama % $konversi : 0;
                                            @endphp

                                            <div class="flex flex-col items-center">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ number_format($stokUtama, 0, ',', '.') }}
                                                    {{ $namaSatuanDasar }}
                                                </span>

                                                @if ($konversi > 1 && $jumlahSatuanBesar > 0)
                                                    <span
                                                        class="text-[11px] text-blue-600 font-medium bg-blue-50 px-2 py-0.5 rounded-full mt-1 border border-blue-100">
                                                        {{ $jumlahSatuanBesar }} {{ $namaSatuanBesar }}
                                                        @if ($sisaEceran > 0)
                                                            + {{ $sisaEceran }} {{ $namaSatuanDasar }}
                                                        @endif
                                                    </span>
                                                @endif

                                                @if ($product->stock <= $product->min_stock)
                                                    <span
                                                        class="text-[10px] bg-red-100 text-red-700 px-1.5 py-0.5 rounded mt-1 font-bold uppercase animate-pulse">
                                                        ⚠️ Low Stock
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-3">
                                                <button type="button"
                                                    onclick='openModalEdit(@json($product))'
                                                    class="text-blue-600 hover:text-blue-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>
                                                <button type="button" onclick="confirmDelete('{{ $product->id }}')"
                                                    class="text-red-600 hover:text-red-900">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-10 text-center text-gray-500 italic">Belum
                                            ada
                                            produk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </form>
                    <div class="mt-4 px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t dark:border-gray-600">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-4xl p-6 shadow-xl overflow-y-auto max-h-[95vh]">
            <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2 text-blue-600">📦 Tambah Produk Baru</h3>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- SISI KIRI: Identitas --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Nama Barang</label>
                            <input type="text" name="name" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Kategori</label>
                            <select name="category_id" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Brand / Merk</label>
                            <select name="brand_id" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Pilih Brand --</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Batas Stok Minimal</label>
                            <input type="number" name="min_stock" value="10"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                            <p class="text-xs text-gray-500 mt-1">*Sistem akan memberi peringatan jika stok mencapai
                                angka ini.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Satuan Beli/Jual</label>
                            <select name="unit_id" id="tambah_unit_id" required onchange="hitungStok('tambah')"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                @foreach ($units as $u)
                                    <option value="{{ $u->id }}"
                                        data-konversi="{{ $u->base_quantity ?? 1 }}">
                                        {{ $u->name }}
                                        {{ $u->parent_id ? '(Isi: ' . round($u->base_quantity) . ' ' . $u->parent->name . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- SISI KANAN: Harga & Stok --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Harga Modal (Dus)</label>
                                <input type="number" name="purchase_price" id="tambah_purchase_price" required
                                    oninput="hitungHargaSatuan('tambah')"
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"
                                    placeholder="Contoh: 140000">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Harga Jual (Dus)</label>
                                <input type="number" name="selling_price" id="tambah_selling_price" required
                                    oninput="hitungHargaSatuan('tambah')"
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"
                                    placeholder="Contoh: 150000">
                            </div>
                        </div>

                        <p id="tambah_info_harga_pcs"
                            class="text-xs text-blue-600 font-semibold bg-blue-50 p-2 rounded border border-blue-100 italic">
                            Analogi: Rp 0 /Pcs
                        </p>

                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-md border border-gray-200">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Setting Harga
                                Grosir</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] text-gray-500">Min. Qty (Pcs)</label>
                                    <input type="number" name="wholesale_min" placeholder="Misal: 40"
                                        class="w-full text-sm rounded-md border-gray-300">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500">Harga Grosir (per Pcs)</label>
                                    <input type="number" name="wholesale_price" placeholder="Misal: 3625"
                                        class="w-full text-sm rounded-md border-gray-300">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Jumlah Fisik (Dus)</label>
                                <input type="number" id="tambah_jumlah_fisik" oninput="hitungStok('tambah')"
                                    placeholder="Misal: 150" class="w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-600">Total Stok (Pcs)</label>
                                <input type="number" name="stock" id="tambah_total_stok" readonly
                                    class="w-full rounded-md bg-blue-100 font-bold border-none text-blue-700">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-8">
                    <button type="button" onclick="closeModal('modalTambah')"
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                    <button type="submit"
                        class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-bold shadow-lg transition">Simpan
                        Produk</button>
                </div>
            </form>
        </div>
    </div>
    {{-- MODAL RESTOK --}}
    <div id="modalRestok" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-2xl p-6 shadow-xl overflow-y-auto max-h-[95vh]">
            <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2 text-green-600 flex items-center">
                <span class="mr-2">🚛</span> Restok Barang: <span id="namaBarangTerpilih"
                    class="ml-2 text-gray-800 dark:text-white">Pilih Barang...</span>
            </h3>

            <form action="{{ route('stock-entry.store') }}" method="POST">
                @csrf
                {{-- Input Hidden Utama untuk Controller --}}
                <input type="hidden" name="product_id" id="restok_product_id">
                <input type="hidden" name="quantity_added" id="hidden_final_stock">

                <div class="space-y-4">
                    {{-- 1. PILIH PRODUK (GRID) --}}
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih
                            Barang:</label>
                        <div class="flex flex-col gap-2 mb-3">
                            <input type="text" id="searchItemRestok" onkeyup="filterRestokCards()"
                                placeholder="Cari nama barang..."
                                class="w-full rounded-md border-gray-300 text-sm focus:ring-blue-500 dark:bg-gray-700 dark:text-white">

                            <div class="flex gap-1 overflow-x-auto pb-1">
                                <button type="button" onclick="filterStatus('all')"
                                    class="px-3 py-1 text-[10px] bg-gray-800 text-white rounded-full font-bold shadow-sm">Semua</button>
                                <button type="button" onclick="filterStatus('KRITIS')"
                                    class="px-3 py-1 text-[10px] bg-red-500 text-white rounded-full font-bold shadow-sm">Kritis</button>
                                <button type="button" onclick="filterStatus('MENIPIS')"
                                    class="px-3 py-1 text-[10px] bg-amber-500 text-white rounded-full font-bold shadow-sm">Menipis</button>
                                <button type="button" onclick="filterStatus('AMAN')"
                                    class="px-3 py-1 text-[10px] bg-green-500 text-white rounded-full font-bold shadow-sm">Aman</button>
                            </div>
                        </div>

                        <div id="restokGrid"
                            class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 bg-gray-100 dark:bg-gray-900 rounded-lg border">
                            @foreach ($products as $product)
                                @php
                                    $status = 'AMAN';
                                    $cardStyle = 'bg-green-50 border-green-200';
                                    $badgeColor = 'bg-green-600';

                                    if ($product->stock <= $product->min_stock) {
                                        $status = 'KRITIS';
                                        $cardStyle = 'bg-red-50 border-red-300';
                                        $badgeColor = 'bg-red-600';
                                    } elseif ($product->stock <= $product->min_stock + 10) {
                                        $status = 'MENIPIS';
                                        $cardStyle = 'bg-amber-50 border-amber-200';
                                        $badgeColor = 'bg-amber-500';
                                    }
                                @endphp
                                <div class="product-card p-2 rounded-md border-2 cursor-pointer transition hover:shadow-md {{ $cardStyle }}"
                                    onclick="siapkanRestok({{ json_encode($product->load('unit.parent')) }})"
                                    data-name="{{ strtolower($product->name) }}" data-status="{{ $status }}">
                                    <div class="flex justify-between items-center mb-1">
                                        <span
                                            class="text-[8px] font-black px-1 rounded text-white {{ $badgeColor }}">{{ $status }}</span>
                                        <span class="text-[10px] font-bold text-gray-600">{{ $product->stock }}</span>
                                    </div>
                                    <h4 class="text-[11px] font-bold text-gray-800 truncate">{{ $product->name }}</h4>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. INFO STOK SAAT INI --}}
                    <div
                        class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase">Stok di
                                Gudang Sekarang:</span>
                            <span id="info_stok_lama"
                                class="text-sm font-bold text-blue-700 dark:text-blue-300">-</span>
                        </div>
                    </div>

                    {{-- 3. INPUT JUMLAH MASUK --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Jumlah Masuk
                                (Nota)</label>
                            <input type="number" id="restok_qty_input" oninput="hitungOtomatisRestok()"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 dark:bg-gray-700 dark:text-white"
                                placeholder="0" step="any">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 dark:text-gray-300">Satuan di Nota</label>
                            <select id="restok_unit_select" onchange="hitungOtomatisRestok()"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 dark:bg-gray-700 dark:text-white"></select>
                        </div>
                    </div>

                    {{-- 4. BOX HASIL KALKULASI --}}
                    <div id="box_info_kalkulasi"
                        class="hidden p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-800 dark:text-green-400 font-medium">Total Akan
                                Ditambah:</span>
                            <span id="display_hasil_akhir"
                                class="text-xl font-black text-green-700 dark:text-green-300">0</span>
                        </div>
                    </div>

                    {{-- 5. HARGA & SUPPLIER --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Harga Beli Baru (Total
                                Nota)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-400">Rp</span>
                                <input type="number" name="actual_purchase_price"
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white pl-10"
                                    placeholder="0">
                            </div>
                            <p class="text-[10px] text-gray-500 mt-1 italic">*Kosongkan jika harga tidak berubah</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Brand / Merk</label>
                            <select name="brand_id" id="restok_brand_select"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:ring-green-500">
                                <option value="">-- Pilih Brand --</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- FOOTER BUTTON --}}
                <div class="flex justify-end space-x-3 pt-6 border-t mt-8">
                    <button type="button" onclick="closeModal('modalRestok')"
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium dark:text-gray-400">Batal</button>
                    <button type="submit"
                        class="px-8 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md font-bold shadow-lg transition">
                        Konfirmasi Restok
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-4xl p-6 shadow-xl overflow-y-auto max-h-[95vh]">
            <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2 text-orange-600">✏️ Edit Produk</h3>

            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- SISI KIRI: Identitas --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Nama Barang</label>
                            <input type="text" name="name" id="edit_name" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Kategori</label>
                            <select name="category_id" id="edit_category_id" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Brand / Merk</label>
                            <select name="brand_id" id="edit_brand_id" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Batas Stok Minimal</label>
                            <input type="number" name="min_stock" id="edit_min_stock"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                            <p class="text-xs text-gray-500 mt-1">*Sistem akan memberi peringatan jika stok mencapai
                                angka ini.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 text-gray-400 italic">Satuan
                                (Hanya Info)</label>
                            <input type="text" id="edit_unit_name" readonly
                                class="w-full rounded-md border-gray-200 bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                        </div>
                    </div>

                    {{-- SISI KANAN: Harga & Stok --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Harga Modal (Satuan)</label>
                                <input type="number" name="purchase_price" id="edit_purchase_price" required
                                    oninput="hitungHargaSatuan('edit')"
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"
                                    step="any">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Harga Jual (Satuan)</label>
                                <input type="number" name="selling_price" id="edit_selling_price" required
                                    oninput="hitungHargaSatuan('edit')"
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"
                                    step="any">
                            </div>
                        </div>

                        <p id="edit_info_harga_pcs"
                            class="text-xs text-blue-600 font-semibold bg-blue-50 p-2 rounded border border-blue-100 italic">
                            Analogi: Rp 0 /Pcs
                        </p>

                        <div class="p-4 bg-orange-50 dark:bg-gray-700 rounded-md border border-orange-100">
                            <label class="block text-xs font-bold text-orange-600 uppercase mb-2">Setting Harga
                                Grosir</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[10px] text-gray-500">Min. Qty (Pcs)</label>
                                    <input type="number" name="wholesale_min" id="edit_wholesale_min"
                                        class="w-full text-sm rounded-md border-gray-300">
                                </div>
                                <div>
                                    <label class="text-[10px] text-gray-500">Harga Grosir (per Pcs)</label>
                                    <input type="number" name="wholesale_price" id="edit_wholesale_price"
                                        class="w-full text-sm rounded-md border-gray-300">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-blue-600 font-bold">Total Stok Saat Ini
                                (Pcs)</label>
                            <input type="number" name="stock" id="edit_stock" required
                                class="w-full rounded-md bg-blue-50 border-blue-200 font-bold text-blue-700">
                            <p class="text-[10px] text-gray-400 mt-1">*Ubah angka ini jika ingin koreksi stok fisik
                                secara langsung.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-8">
                    <button type="button" onclick="closeModal('modalEdit')"
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                    <button type="submit"
                        class="px-8 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-md font-bold shadow-lg transition">Update
                        Produk</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Variabel global
        let dataProdukAktif = null;

        // --- FUNGSI UTAMA MODAL ---
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
            // Reset filter brand jika yang ditutup modal restok
            if (id === 'modalRestok') {
                const brandSelect = document.getElementById('restok_brand_select');
                if (brandSelect) {
                    Array.from(brandSelect.options).forEach(option => option.style.display = "block");
                }
            }
        }

        // --- FUNGSI EDIT PRODUK (Perbaikan Utama) ---
        function openModalEdit(product) {
            // 1. Set Action Form ke route update
            const form = document.getElementById('formEdit');
            form.action = `/products/${product.id}`;

            // 2. Isi data teks & angka (Gunakan ID dengan prefix edit_)
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_purchase_price').value = parseFloat(product.purchase_price);
            document.getElementById('edit_selling_price').value = parseFloat(product.selling_price);
            document.getElementById('edit_min_stock').value = product.min_stock;
            document.getElementById('edit_stock').value = product.stock;

            // 3. Dropdown Kategori & Brand
            document.getElementById('edit_category_id').value = product.category_id;
            document.getElementById('edit_brand_id').value = product.brand_id;

            // 4. Harga Grosir (Handle jika null)
            document.getElementById('edit_wholesale_min').value = product.wholesale_min || '';
            document.getElementById('edit_wholesale_price').value = product.wholesale_price || '';

            // 5. Satuan (Info Only)
            if (document.getElementById('edit_unit_name')) {
                document.getElementById('edit_unit_name').value = product.unit ? product.unit.name : 'Pcs';
            }

            // Simpan konversi untuk hitung info harga
            const konversi = product.unit ? parseFloat(product.unit.base_quantity) : 1;
            const hiddenKonversi = document.getElementById('edit_unit_konversi');
            if (hiddenKonversi) hiddenKonversi.value = konversi;

            // 6. Jalankan kalkulasi info harga sekilas
            hitungHargaSatuan('edit');

            // 7. Buka Modal
            openModal('modalEdit');
        }

        // --- FUNGSI HITUNG HARGA SATUAN (Analogi /Pcs) ---
        function hitungHargaSatuan(prefix) {
            const hargaInput = document.getElementById(`${prefix}_selling_price`);
            const infoDisplay = document.getElementById(`${prefix}_info_harga_pcs`);

            let konversi = 1;
            let unitName = 'Unit';
            let baseUnitName = 'Pcs';

            if (prefix === 'tambah') {
                const selectUnit = document.getElementById('tambah_unit_id');
                const selectedOption = selectUnit.options[selectUnit.selectedIndex];
                konversi = parseFloat(selectedOption?.dataset.konversi) || 1;
                unitName = selectedOption?.text.split('(')[0].trim() || 'Unit';
                if (selectedOption?.text.includes('Isi:')) {
                    baseUnitName = selectedOption.text.split(' ').pop().replace(')', '');
                }
            } else if (prefix === 'edit') {
                const hiddenKonv = document.getElementById('edit_unit_konversi');
                konversi = parseFloat(hiddenKonv?.value) || 1;
                unitName = document.getElementById('edit_unit_name')?.value || 'Unit';
                baseUnitName = unitName.toLowerCase().includes('karung') ? 'Kg' : 'Pcs';
            }

            if (hargaInput && hargaInput.value && konversi > 1) {
                const harga = parseFloat(hargaInput.value);
                const hasilBagi = Math.round(harga / konversi);
                if (infoDisplay) {
                    infoDisplay.innerHTML =
                        `💡 <b>Info:</b> Rp ${harga.toLocaleString('id-ID')} /${unitName} = <b>Rp ${hasilBagi.toLocaleString('id-ID')} /${baseUnitName}</b>`;
                    infoDisplay.classList.remove('hidden');
                }
            } else {
                if (infoDisplay) infoDisplay.classList.add('hidden');
            }
        }

        // --- FUNGSI STOK & RESTOK ---
        function hitungStok(prefix) {
            const inputFisik = document.getElementById(prefix + '_jumlah_fisik');
            const selectUnit = document.getElementById(prefix + '_unit_id');
            const inputTotal = document.getElementById(prefix + '_total_stok');

            if (!inputFisik || !selectUnit || !inputTotal) return;

            const selectedOption = selectUnit.options[selectUnit.selectedIndex];
            const konversi = parseFloat(selectedOption.getAttribute('data-konversi')) || 1;
            const total = (parseFloat(inputFisik.value) || 0) * konversi;

            inputTotal.value = total;
            hitungHargaSatuan(prefix);
        }

        function siapkanRestok(product) {
            dataProdukAktif = product;
            document.getElementById('namaBarangTerpilih').innerText = product.name;
            document.getElementById('restok_product_id').value = product.id;

            // Filter Brand Otomatis
            const brandSelect = document.getElementById('restok_brand_select');
            if (brandSelect && product.brand_id) {
                brandSelect.value = product.brand_id;
            }

            // Setup Satuan Restok
            const unitSelect = document.getElementById('restok_unit_select');
            unitSelect.innerHTML = '';
            const unitName = product.unit ? product.unit.name : 'Pcs';
            const isiKonversi = product.unit ? parseFloat(product.unit.base_quantity) : 1;
            const unitDasar = (product.unit && product.unit.parent) ? product.unit.parent.name : 'Pcs';

            if (isiKonversi > 1) {
                unitSelect.add(new Option(`${unitName} (Isi ${isiKonversi} ${unitDasar})`, isiKonversi));
                unitSelect.add(new Option(unitDasar, 1));
            } else {
                unitSelect.add(new Option(unitName, 1));
            }

            document.getElementById('info_stok_lama').innerText = `${product.stock} ${unitDasar}`;
            document.getElementById('restok_qty_input').value = '';
            openModal('modalRestok');
        }

        function hitungOtomatisRestok() {
            const qty = parseFloat(document.getElementById('restok_qty_input').value) || 0;
            const pengali = parseFloat(document.getElementById('restok_unit_select').value) || 1;
            const total = qty * pengali;

            const display = document.getElementById('display_hasil_akhir');
            if (display) display.innerText = `${total} Unit`;

            const hidden = document.getElementById('hidden_final_stock');
            if (hidden) hidden.value = total;
        }

        // --- EVENT LISTENERS (Hapus Duplicate & Gabungkan) ---
        document.addEventListener('DOMContentLoaded', function() {
            // Select All Checkbox
            const selectAll = document.getElementById('selectAllProduct');
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
                });
            }

            // Auto-hide Alert
            setTimeout(() => {
                const alert = document.getElementById('success-alert');
                if (alert) alert.style.display = 'none';
            }, 3000);
        });

        // --- FUNGSI DELETE (SweetAlert) ---
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Data tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/products/${id}`;
                    form.innerHTML =
                        `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
