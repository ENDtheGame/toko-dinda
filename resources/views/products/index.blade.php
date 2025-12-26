<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Produk Grosir') }}
            </h2>
        </div>
    </x-slot>

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
                            <input type="text" name="search" value="{{ request('search') }}"
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
                    </div>
                </div>

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
                                        (Pcs)</th>
                                    <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-center">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y dark:divide-gray-700">
                                @forelse ($products as $key => $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                        <td class="px-6 py-4"><input type="checkbox" name="ids[]"
                                                value="{{ $product->id }}"
                                                class="product-checkbox rounded border-gray-300"></td>
                                        <td class="px-6 py-4 dark:text-white">{{ $products->firstItem() + $key }}</td>
                                        <td class="px-6 py-4 font-bold dark:text-white">{{ $product->name }}</td>
                                        <td class="px-6 py-4 dark:text-gray-300">{{ $product->category->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 dark:text-gray-300">{{ $product->brand->name ?? '-' }}</td>
                                        <td class="px-6 py-4 dark:text-white text-right">
                                            <div class="text-sm font-bold">Rp
                                                {{ number_format($product->selling_price, 0, ',', '.') }} <span
                                                    class="text-[10px] font-normal">/Pcs</span></div>
                                            @if ($product->unit && $product->unit->base_quantity > 1)
                                                <div class="text-[10px] text-blue-500 italic">(Rp
                                                    {{ number_format($product->selling_price * $product->unit->base_quantity, 0, ',', '.') }}
                                                    /{{ $product->unit->name }})</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-bold {{ $product->stock <= 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                                {{ $product->stock }}
                                            </span>
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
                                        <td colspan="8" class="px-6 py-10 text-center text-gray-500 italic">Belum ada
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
                        {{-- Di Edit, kita kunci satuannya atau tampilkan sebagai info saja agar tidak merusak hitungan stok yang sudah ada --}}
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300 text-gray-400 italic">Satuan
                                (Hanya Info)</label>
                            <input type="text" id="edit_unit_name" readonly
                                class="w-full rounded-md border-gray-200 bg-gray-50 text-gray-500 text-sm">
                            <input type="hidden" id="edit_unit_konversi">
                        </div>
                    </div>

                    {{-- SISI KANAN: Harga & Stok --}}
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Harga Modal (Pcs)</label>
                                <input type="number" name="purchase_price" id="edit_purchase_price" required
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Harga Jual (Pcs)</label>
                                <input type="number" name="selling_price" id="edit_selling_price" required
                                    oninput="hitungHargaSatuan('edit')"
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                            </div>
                        </div>

                        <p id="edit_info_harga_pcs"
                            class="text-xs text-blue-600 font-semibold bg-blue-50 p-2 rounded border border-blue-100 italic hidden">
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
                            <label class="block text-sm font-medium text-blue-600">Total Stok Saat Ini (Pcs)</label>
                            <input type="number" name="stock" id="edit_stock" required
                                class="w-full rounded-md bg-blue-50 border-blue-200 font-bold text-blue-700">
                            <p class="text-[10px] text-gray-400 mt-1">*Ubah angka ini jika ingin koreksi stok langsung
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-8">
                    <button type="button" onclick="closeModal('modalEdit')"
                        class="px-4 py-2 text-gray-500 font-medium">Batal</button>
                    <button type="submit"
                        class="px-8 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-md font-bold shadow-lg transition">Update
                        Produk</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        // FUNGSI UTAMA HITUNG HARGA (Satu fungsi untuk Tambah & Edit)
        function hitungHargaSatuan(prefix) {
            const hargaInput = document.getElementById(`${prefix}_selling_price`).value;
            const infoDisplay = document.getElementById(`${prefix}_info_harga_pcs`);

            let konversi = 1;
            let unitName = 'Unit';

            if (prefix === 'tambah') {
                // Ambil dari Select Option di Modal Tambah
                const selectUnit = document.getElementById('tambah_unit_id');
                const selectedOption = selectUnit.options[selectUnit.selectedIndex];
                konversi = parseFloat(selectedOption?.dataset.konversi) || 1;
                unitName = selectedOption?.text.split('(')[0].trim() || 'Unit';
            } else {
                // Ambil dari Hidden Input di Modal Edit
                konversi = parseFloat(document.getElementById('edit_unit_konversi').value) || 1;
                unitName = document.getElementById('edit_unit_name').value || 'Unit';
            }

            if (hargaInput && konversi > 1) {
                const harga = parseFloat(hargaInput);

                if (prefix === 'tambah') {
                    // MODAL TAMBAH: Input Harga DUS -> Info Harga PCS
                    const hasilPcs = Math.round(harga / konversi);
                    infoDisplay.innerHTML =
                        `💡 <b>Info:</b> Rp ${harga.toLocaleString('id-ID')} /${unitName} = <b>Rp ${hasilPcs.toLocaleString('id-ID')} /Pcs</b>`;
                } else {
                    // MODAL EDIT: Input Harga PCS -> Info Harga DUS
                    const hasilDus = Math.round(harga * konversi);
                    infoDisplay.innerHTML =
                        `💡 <b>Info:</b> Jual Rp ${harga.toLocaleString('id-ID')} /Pcs setara <b>Rp ${hasilDus.toLocaleString('id-ID')} /${unitName}</b>`;
                }
                infoDisplay.classList.remove('hidden');
            } else {
                infoDisplay.classList.add('hidden');
            }
        }

        // FUNGSI HITUNG STOK (Hanya untuk Modal Tambah)
        function hitungStok(prefix) {
            const jumlahFisik = document.getElementById(prefix + '_jumlah_fisik').value || 0;
            const selectUnit = document.getElementById(prefix + '_unit_id');
            const selectedOption = selectUnit.options[selectUnit.selectedIndex];
            const konversi = parseFloat(selectedOption.getAttribute('data-konversi')) || 1;

            const total = parseFloat(jumlahFisik) * konversi;
            document.getElementById(prefix + '_total_stok').value = total;

            // Jalankan hitung harga juga saat satuan diganti
            hitungHargaSatuan(prefix);
        }

        // MODAL EDIT OPENER
        function openModalEdit(product) {
            document.getElementById('formEdit').action = '/products/' + product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_category_id').value = product.category_id;
            document.getElementById('edit_brand_id').value = product.brand_id || '';
            document.getElementById('edit_purchase_price').value = Math.round(product.purchase_price);
            document.getElementById('edit_selling_price').value = Math.round(product.selling_price);
            document.getElementById('edit_wholesale_min').value = product.wholesale_min || '';
            document.getElementById('edit_wholesale_price').value = product.wholesale_price || '';
            document.getElementById('edit_stock').value = product.stock;

            if (product.unit) {
                document.getElementById('edit_unit_name').value = product.unit.name;
                document.getElementById('edit_unit_konversi').value = product.unit.base_quantity;

                // Langsung panggil hitung harga agar info muncul otomatis
                setTimeout(() => {
                    hitungHargaSatuan('edit');
                }, 50);
            }

            openModal('modalEdit');
        }

        // SWEETALERT & BULK DELETE LOGIC
        document.getElementById('selectAllProduct').onclick = function() {
            document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
        }

        function confirmBulkDelete() {
            let count = document.querySelectorAll('.product-checkbox:checked').length;
            if (count === 0) return Swal.fire('Info', 'Pilih barang dulu!', 'info');
            Swal.fire({
                title: 'Hapus ' + count + ' produk?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('formBulkDelete').submit();
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Produk?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.createElement('form');
                    form.action = '/products/' + id;
                    form.method = 'POST';
                    form.innerHTML = `@csrf @method('DELETE')`;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        // Menghilangkan Alert Success Secara Otomatis dalam 3 Detik
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    // Tambahkan efek transisi halus (fade out)
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = "0";

                    // Hapus elemen dari layar setelah transisi selesai
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }, 3000); // 3000ms = 3 detik
            }
        });
    </script>
</x-app-layout>
