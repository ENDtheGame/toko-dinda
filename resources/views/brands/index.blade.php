<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Brand') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg shadow-sm">{{ session('error') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-4">
                    <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                        <h3 class="text-lg font-bold mb-4 dark:text-white">Tambah Brand</h3>
                        <form action="{{ route('brands.store') }}" method="POST">
                            @csrf
                            <input type="text" name="name"
                                class="w-full rounded-md border-gray-300 mb-4 dark:bg-gray-700 dark:text-white"
                                placeholder="Nama Brand..." required>
                            <button type="submit"
                                class="w-full bg-blue-600 text-white font-bold py-2 rounded-lg hover:bg-blue-700 transition">Simpan</button>
                        </form>
                    </div>
                </div>

                <div class="md:col-span-8">
                    <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden p-6">
                        <form action="{{ route('brands.bulkDelete') }}" method="POST" id="formBulk">
                            @csrf
                            <div class="mb-4 flex justify-between items-center">
                                <h3 class="text-lg font-bold dark:text-white">Daftar Brand</h3>
                                <button type="submit" onclick="return confirm('Hapus brand terpilih?')"
                                    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-bold">
                                    Hapus Terpilih
                                </button>
                            </div>

                            <table class="w-full text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3"><input type="checkbox" id="checkAll"></th>
                                        <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500">Nama Brand</th>
                                        <th class="px-4 py-3 text-xs font-bold uppercase text-gray-500 text-right">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y dark:divide-gray-700">
                                    @foreach ($brands as $brand)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                            <td class="px-4 py-3"><input type="checkbox" name="ids[]"
                                                    value="{{ $brand->id }}" class="brand-checkbox"></td>
                                            <td class="px-4 py-3 dark:text-white font-medium">{{ $brand->name }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <div class="flex justify-end space-x-2">
                                                    {{-- Tombol Edit --}}
                                                    <button type="button" onclick="openEditBrand({{ $brand }})"
                                                        class="text-blue-600 hover:text-blue-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                    </button>
                                                    {{-- Tombol Delete Single --}}
                                                    <button type="button"
                                                        onclick="confirmDeleteBrand({{ $brand->id }})"
                                                        class="text-red-600 hover:text-red-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"></path>
                                                        </svg>
                                                    </button>
                                                    <form id="del-brand-{{ $brand->id }}"
                                                        action="{{ route('brands.destroy', $brand->id) }}"
                                                        method="POST" class="hidden">@csrf @method('DELETE')</form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Brand --}}
    <div id="modalEditBrand" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md p-6 shadow-xl">
            <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2">✏️ Edit Brand</h3>
            <form id="formEditBrand" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium dark:text-gray-300">Nama Brand</label>
                    <input type="text" name="name" id="edit_brand_name" required
                        class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:text-white mt-1">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('modalEditBrand')"
                        class="px-4 py-2 text-gray-500">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Check All Script
        document.getElementById('checkAll').onclick = function() {
            let checkboxes = document.getElementsByClassName('brand-checkbox');
            for (let checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }

        function openEditBrand(brand) {
            document.getElementById('formEditBrand').action = '/brands/' + brand.id;
            document.getElementById('edit_brand_name').value = brand.name;
            document.getElementById('modalEditBrand').classList.remove('hidden');
            document.getElementById('modalEditBrand').classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        function confirmDeleteBrand(id) {
            if (confirm('Yakin ingin menghapus brand ini?')) {
                document.getElementById('del-brand-' + id).submit();
            }
        }
    </script>
</x-app-layout>
