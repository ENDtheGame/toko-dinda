<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kelola Kategori Barang') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow h-fit">
                    <h3 class="font-bold mb-4 dark:text-white border-b pb-2 text-lg">Tambah Kategori</h3>
                    <form action="{{ route('categories.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Nama Kategori</label>
                            <input type="text" name="name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Misal: Mie Instan" required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium dark:text-gray-300 mb-1">Induk Kategori
                                (Opsional)</label>
                            <select name="parent_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">-- Kategori Utama --</option>
                                @foreach ($categories->where('parent_id', null) as $cat)
                                    <option value="{{ $cat->id }}">📂 {{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">*Pilih induk jika ini adalah sub-kategori.</p>
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 font-bold transition shadow-md">
                            Simpan Kategori
                        </button>
                    </form>
                </div>

                <div class="md:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs uppercase font-bold">
                                <th class="px-4 py-3 border-b dark:border-gray-600">Nama Kategori</th>
                                <th class="px-4 py-3 border-b dark:border-gray-600 text-center">Aksi</th>
                            </tr>
                        </thead>

                        @forelse ($parentCategories as $parent)
                            {{-- Setiap Induk punya 1 tbody sendiri agar x-data tidak tertukar --}}
                            <tbody x-data="{ open: false }" class="border-b dark:border-gray-700">
                                <tr
                                    class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 transition cursor-pointer">
                                    <td class="px-4 py-4 font-bold dark:text-white" @click="open = !open">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 transition-transform duration-200 text-gray-500"
                                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                            <span>📂 {{ $parent->name }}</span>
                                            <span
                                                class="ml-2 px-2 py-0.5 text-xs bg-gray-200 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-400">
                                                {{ $parent->children->count() }} Anak
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex justify-center space-x-3">
                                            <button type="button"
                                                onclick="openEditModal('{{ $parent->id }}', '{{ $parent->name }}', '{{ $parent->parent_id }}')"
                                                class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Edit</button>

                                            <form action="{{ route('categories.destroy', $parent->id) }}" method="POST"
                                                class="inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
                                                    class="text-red-500 hover:text-red-700 font-semibold text-sm">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                @foreach ($parent->children as $child)
                                    <tr x-show="open" x-cloak x-transition
                                        class="bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-800">
                                        <td class="px-4 py-3 pl-12 text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <span class="mr-2 text-gray-300">↳</span>
                                                {{ $child->name }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex justify-center space-x-3 text-xs">
                                                <button type="button"
                                                    onclick="openEditModal('{{ $child->id }}', '{{ $child->name }}', '{{ $child->parent_id }}')"
                                                    class="text-blue-400 hover:text-blue-600 font-medium">Edit</button>

                                                <form action="{{ route('categories.destroy', $child->id) }}"
                                                    method="POST" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDelete(this)"
                                                        class="text-red-400 hover:text-red-600 font-medium">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @empty
                            <tbody>
                                <tr>
                                    <td colspan="2" class="px-4 py-8 text-center text-gray-500 italic">Belum ada
                                        kategori.</td>
                                </tr>
                            </tbody>
                        @endforelse
                    </table>

                    <div class="mt-4">
                        {{ $parentCategories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"
                onclick="closeEditModal()"></div>

            <div
                class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6">
                <h3 class="text-lg font-bold dark:text-white mb-4 border-b pb-2">Edit Kategori</h3>
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium dark:text-gray-300 mb-1">Nama Kategori</label>
                        <input type="text" name="name" id="edit_name"
                            class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium dark:text-gray-300 mb-1">Induk Kategori</label>
                        <select name="parent_id" id="edit_parent_id"
                            class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Kategori Utama --</option>
                            @foreach ($categories->where('parent_id', null) as $cat)
                                <option value="{{ $cat->id }}">📂 {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">Batal</button>
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md font-bold hover:bg-blue-700 transition">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <script>
        // Fungsi Modal
        function openEditModal(id, name, parentId) {
            const modal = document.getElementById('editModal');
            const form = document.getElementById('editForm');
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_parent_id').value = parentId || "";
            form.action = `/categories/${id}`;
            modal.classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Notifikasi Sukses
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
            });
        @endif

        // Konfirmasi Hapus
        function confirmDelete(button) {
            const form = button.closest('form');
            Swal.fire({
                title: 'Hapus Kategori?',
                text: "Sub-kategori di dalamnya juga akan terpengaruh!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
