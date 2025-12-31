<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Daftar Brand') }}
            </h2>
        </div>
    </x-slot>
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
                <form action="{{ route('brands.index') }}" method="GET" class="w-full md:w-1/3">
                    <div class="relative">
                        <input type="text" id="search-input" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama Brand..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:bg-gray-700 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        + Tambah Brand Baru
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden border dark:border-gray-700">
                <form id="formBulkDelete" action="{{ route('brands.bulkDelete') }}" method="POST">
                    @csrf
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                            <tr>
                                <th class="px-6 py-3 w-10"><input type="checkbox" id="selectAllBrand"
                                        class="rounded border-gray-300"></th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">No</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Nama Brand</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Nama Sales</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">No Telepon</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Kategori Brand</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500">Status</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase text-gray-500 text-center">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-gray-700" id="brand-table-body">
                            @forelse ($brands as $key => $brand)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                    <td class="px-6 py-4"><input type="checkbox" name="ids[]"
                                            value="{{ $brand->id }}"
                                            class="product-checkbox rounded border-gray-300"></td>
                                    <td class="px-6 py-4 dark:text-white">{{ $brands->firstItem() + $key }}</td>
                                    <td class="px-6 py-4 font-bold dark:text-white">{{ $brand->name }}</td>
                                    <td class="px-6 py-4 dark:text-gray-300">{{ $brand->sales_name }}
                                    </td>
                                    <td class="px-6 py-4 dark:text-gray-300">{{ $brand->sales_phone }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="block font-semibold text-blue-600">
                                            {{ $brand->category->name ?? 'Tanpa Kategori Utama' }}
                                        </span>

                                        <hr class="my-1 border-gray-200">

                                        <div class="flex flex-wrap gap-1">
                                            @if ($brand->products && $brand->products->count() > 0)
                                                @foreach ($brand->products->pluck('category.name')->unique() as $catName)
                                                    <span
                                                        class="bg-gray-100 text-gray-700 text-[10px] px-2 py-0.5 rounded border">
                                                        {{ $catName }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-gray-400 italic text-[10px]">Belum ada produk
                                                    terdaftar</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($brand->status == 'active')
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Active
                                            </span>
                                        @else
                                            <span
                                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center space-x-3">
                                            <button type="button" onclick='openEditBrand(@json($brand))'
                                                class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </button>
                                            <button type="button" onclick="confirmDelete('{{ $brand->id }}')"
                                                class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                            <form id="delete-form-{{ $brand->id }}"
                                                action="{{ route('brands.destroy', $brand->id) }}" method="POST"
                                                class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-gray-500 italic">Belum ada
                                        brand</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
                <div class="mt-4 px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t dark:border-gray-600">
                    {{ $brands->links() }}
                </div>
            </div>
        </div>
    </div>
    </div>


    {{-- MODAL TAMBAH --}}
    <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-4xl p-6 shadow-xl overflow-y-auto max-h-[95vh]">
            <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2 text-blue-600">📦 Tambah Brand Baru</h3>

            <form action="{{ route('brands.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- SISI KIRI: Identitas --}}
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Nama Brand</label>
                            <input type="text" name="name" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Nama Sales</label>
                            {{-- Perbaikan: ganti name dari 'name' menjadi 'sales_name' --}}
                            <input type="text" name="sales_name"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">No Telepon
                                (WhatsApp)</label>
                            {{-- Perbaikan: ganti name menjadi 'sales_phone' agar sesuai database --}}
                            <input type="text" name="sales_phone"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white"
                                placeholder="628123...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Kategori Utama</label>
                            <select name="category_id" id="category_id" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white mt-1">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($main_categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Status</label>
                            <select name="status" id="status" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white mt-1">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-8">
                    <button type="button" onclick="closeModal('modalTambah')"
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                    <button type="submit"
                        class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-bold shadow-lg transition">Simpan
                        Brand</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT BRAND --}}
    <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-4xl p-6 shadow-xl overflow-y-auto max-h-[95vh]">
            <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2 text-amber-600">📝 Edit Data Brand</h3>

            {{-- Form action akan diisi otomatis oleh JavaScript --}}
            <form id="formEdit" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Nama Brand</label>
                            <input type="text" name="name" id="edit_name" required
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Nama Sales</label>
                            <input type="text" name="sales_name" id="edit_sales_name"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">No Telepon (WhatsApp)</label>
                            <input type="number" name="sales_phone" id="edit_sales_phone"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Kategori Utama</label>
                            <select name="category_id" id="edit_category_id" required class="w-full ...">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($main_categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium dark:text-gray-300">Status</label>
                            <select name="status" id="edit_status"
                                class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t mt-8">
                    <button type="button" onclick="closeModal('modalEdit')"
                        class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium">Batal</button>
                    <button type="submit"
                        class="px-8 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-md font-bold shadow-lg transition">Update
                        Brand</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // 1. Fungsi Umum untuk Membuka/Menutup Modal
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
        }

        // 2. Fungsi untuk Mengisi Data ke Modal Edit
        function openEditBrand(brand) {
            const form = document.getElementById('formEdit');
            form.action = `/brands/${brand.id}`;

            document.getElementById('edit_name').value = brand.name;
            document.getElementById('edit_sales_name').value = brand.sales_name || '';
            document.getElementById('edit_sales_phone').value = brand.sales_phone || '';

            // Pastikan ID dropdown di modal edit adalah 'edit_category_id'
            if (document.getElementById('edit_category_id')) {
                document.getElementById('edit_category_id').value = brand.category_id || '';
            }

            document.getElementById('edit_status').value = brand.status || 'active';

            openModal('modalEdit');
        }

        // 3. Logika Checkbox (Select All) dan Delete
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAllBrand');
            if (selectAll) {
                selectAll.onclick = function() {
                    let checkboxes = document.querySelectorAll('#brand-table-body input[type="checkbox"]');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                };
            }

            // Auto-hide alert sukses setelah 3 detik
            const alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 3000);
            }
        });

        function confirmBulkDelete() {
            const checked = document.querySelectorAll('#brand-table-body input[name="ids[]"]:checked');
            if (checked.length === 0) {
                alert('Silakan pilih brand yang ingin dihapus.');
                return;
            }
            if (confirm('Hapus semua brand yang dipilih?')) {
                document.getElementById('formBulkDelete').submit();
            }
        }

        function confirmDelete(id) {
            if (confirm('Yakin ingin menghapus brand ini?')) {
                const form = document.getElementById('delete-form-' + id);
                if (form) form.submit();
            }
        }
    </script>
</x-app-layout>
