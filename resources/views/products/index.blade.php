<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Produk Grosir') }}
            </h2>
            <button onclick="openModal('modalTambah')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-bold shadow-sm transition">
                + Tambah Produk Baru
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Success --}}
            @if (session('success'))
                <div id="success-alert"
                    class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md shadow-sm transition-opacity duration-500">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">No</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Nama Barang</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Kategori</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Harga Jual</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-center">Stok (Pcs)
                                </th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700">
                            @forelse ($products as $key => $product)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                    <td class="px-6 py-4 dark:text-white">{{ $key + 1 }}</td>
                                    <td class="px-6 py-4 font-bold dark:text-white">{{ $product->name }}</td>
                                    <td class="px-6 py-4 dark:text-gray-300">
                                        {{ $product->category->name ?? 'Tanpa Kategori' }}</td>
                                    <td class="px-6 py-4 dark:text-white text-right font-mono">Rp
                                        {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-2 py-1 rounded-full text-xs font-bold {{ $product->stock <= 10 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center space-x-3">
                                            <button onclick="openModalEdit({{ $product }})"
                                                class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button onclick="confirmDelete('{{ $product->id }}')"
                                                class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                            <form id="delete-form-{{ $product->id }}"
                                                action="{{ route('products.destroy', $product->id) }}" method="POST"
                                                class="hidden">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">Belum ada
                                        produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-2xl p-6 shadow-xl overflow-y-auto max-h-[90vh]">
            <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2">📦 Tambah Produk Baru</h3>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            <label class="block text-sm font-medium dark:text-gray-300">Satuan Jual</label>
                            <select name="unit_id" id="tambah_unit_id" required onchange="hitungStok('tambah')"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                @foreach ($units as $u)
                                    <option value="{{ $u->id }}" data-konversi="{{ $u->base_quantity ?? 1 }}">
                                        {{ $u->name }}
                                        {{ $u->parent_id ? '(Isi: ' . round($u->base_quantity) . ' ' . $u->parent->name . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium">Harga Modal</label>
                                <input type="number" name="purchase_price" required
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Harga Jual</label>
                                <input type="number" name="selling_price" required
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700">
                            </div>
                        </div>

                        <div class="p-3 bg-blue-50 dark:bg-gray-700 rounded-md border border-blue-100">
                            <label class="block text-[10px] font-bold text-blue-600 uppercase">Setting Harga
                                Grosir</label>
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <input type="number" name="wholesale_min" placeholder="Min. Qty"
                                    class="text-xs rounded-md border-gray-300">
                                <input type="number" name="wholesale_price" placeholder="Harga Grosir"
                                    class="text-xs rounded-md border-gray-300">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Jumlah Stok</label>
                                <input type="number" id="tambah_jumlah_fisik" oninput="hitungStok('tambah')"
                                    placeholder="Misal: 30" class="w-full rounded-md border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-blue-600">Total (Pcs)</label>
                                <input type="number" name="stock" id="tambah_total_stok" readonly
                                    class="w-full rounded-md bg-blue-100 font-bold border-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeModal('modalTambah')"
                        class="px-4 py-2 text-gray-500">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold">Simpan
                        Produk</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-2xl p-6 shadow-xl">
            <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2">✏️ Edit Produk</h3>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium">Nama Barang</label>
                            <input type="text" name="name" id="edit_name" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Kategori</label>
                            <select name="category_id" id="edit_category_id" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-red-500 font-bold">⚠️ Perhatian!</label>
                            <p class="text-[10px] text-gray-500 italic">Untuk Edit Stok, langsung masukkan angka
                                Pcs/Terkecil di bawah.</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium">Modal</label>
                                <input type="number" name="purchase_price" id="edit_purchase_price" required
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Jual</label>
                                <input type="number" name="selling_price" id="edit_selling_price" required
                                    class="w-full rounded-md border-gray-300 dark:bg-gray-700">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">Stok Saat Ini (Pcs)</label>
                            <input type="number" name="stock" id="edit_stock" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 font-bold text-blue-600">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                    <button type="button" onclick="closeModal('modalEdit')"
                        class="px-4 py-2 text-gray-500">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold">Update
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

        function openModalEdit(product) {
            // Set Action URL
            document.getElementById('formEdit').action = '/products/' + product.id;

            // Set Values
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_category_id').value = product.category_id;
            document.getElementById('edit_purchase_price').value = product.purchase_price;
            document.getElementById('edit_selling_price').value = product.selling_price;
            document.getElementById('edit_stock').value = product.stock;

            openModal('modalEdit');
        }

        function hitungStok(prefix) {
            const jumlahFisik = document.getElementById(prefix + '_jumlah_fisik').value || 0;
            const selectUnit = document.getElementById(prefix + '_unit_id');
            const selectedOption = selectUnit.options[selectUnit.selectedIndex];
            const konversi = selectedOption.getAttribute('data-konversi') || 1;

            const total = parseFloat(jumlahFisik) * parseFloat(konversi);
            document.getElementById(prefix + '_total_stok').value = total;
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Produk?',
                text: "Data ini tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }

        // Auto hide alert
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            }
        });
    </script>
</x-app-layout>
