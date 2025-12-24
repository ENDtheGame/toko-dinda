<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Konversi Satuan Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow h-fit">
                    <h3 class="font-bold mb-4 dark:text-white border-b pb-2">Tambah Satuan</h3>
                    <form action="{{ route('units.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Nama Satuan</label>
                                <input type="text" name="name" placeholder="Contoh: Dus" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700">Satuan Dasar (Induk)</label>
                                <select name="parent_id" id="parent_id" onchange="updateLabel()"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                                    <option value="">-- Ini Satuan Terkecil (Paling Dasar) --</option>
                                    @foreach ($units as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="konversi_container">
                                <label class="block text-sm font-bold text-gray-700" id="label_konversi">
                                    Nilai Konversi
                                </label>
                                <div id="helper_text"
                                    class="hidden mt-2 p-2 bg-yellow-50 border-l-4 border-yellow-400 text-xs text-yellow-700">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Berarti: Jika Anda menjual 1 <strong id="txt_satuan_ini">...</strong>, maka stok
                                    akan berkurang sebanyak <strong id="txt_jumlah">0</strong> <strong
                                        id="txt_satuan_dasar">...</strong>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500">1 <span id="span_satuan_baru">[Satuan]</span>
                                        berisi</span>
                                    <input type="number" name="base_quantity" placeholder="0"
                                        class="w-20 rounded-md border-gray-300 shadow-sm">
                                    <span class="text-sm text-gray-500" id="span_satuan_dasar">[Satuan Dasar]</span>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-1">*Contoh: 1 Dus berisi 40 Pcs</p>
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 font-bold transition">Simpan
                            Satuan</button>
                    </form>
                </div>

                <div class="md:col-span-2 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 border-b">SATUAN</th>
                                <th class="px-4 py-2 border-b">DETAIL KONVERSI</th>
                                <th class="px-4 py-2 border-b text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
    {{-- Kita tampilkan dulu semua satuan yang punya 'anak' atau konversi --}}
    @foreach($units->whereNotNull('parent_id') as $unit)
        <tr class="bg-white hover:bg-gray-50">
            <td class="px-4 py-3">
                <div class="flex items-center">
                    <span class="text-xl mr-2">📦</span>
                    <div>
                        <div class="font-bold text-gray-900">{{ $unit->name }}</div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-wider">Satuan Besar</div>
                    </div>
                </div>
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center space-x-2">
                    <span class="text-gray-400 text-xs">Isi:</span>
                    <span class="font-semibold text-blue-600">{{ number_format($unit->base_quantity) }}</span>
                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs border border-blue-100">
                        {{ $unit->parent->name }}
                    </span>
                </div>
                <p class="text-[10px] text-gray-400 mt-1 italic">
                    *Jika dijual 1 {{ $unit->name }}, stok berkurang {{ number_format($unit->base_quantity) }} {{ $unit->parent->name }}
                </p>
            </td>
            <td class="px-4 py-3 text-center">
                <form action="{{ route('units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('Hapus satuan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach

    {{-- Tampilkan satuan dasar yang belum punya konversi di bagian bawah --}}
    <tr class="bg-gray-50">
        <td colspan="3" class="px-4 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
            Daftar Satuan Dasar (Terkecil)
        </td>
    </tr>
    @foreach($units->whereNull('parent_id') as $baseUnit)
        <tr class="opacity-70">
            <td class="px-4 py-2 text-sm text-gray-600">
                📄 {{ $baseUnit->name }}
            </td>
            <td class="px-4 py-2 text-xs text-gray-400 italic">
                Tidak ada konversi (Satuan Paling Kecil)
            </td>
            <td class="px-4 py-2 text-center">
                <form action="{{ route('units.destroy', $baseUnit->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-gray-400 hover:text-red-500 text-xs">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateLabel() {
            const nameInput = document.getElementsByName('name')[0].value || '[Satuan Besar]';
            const parentSelect = document.getElementById('parent_id');
            const helper = document.getElementById('helper_text');
            const qty = document.getElementsByName('base_quantity')[0].value || '0';

            if (parentSelect.value !== "") {
                helper.classList.remove('hidden');
                const parentText = parentSelect.options[parentSelect.selectedIndex].text;

                document.getElementById('txt_satuan_ini').innerText = nameInput;
                document.getElementById('txt_jumlah').innerText = qty;
                document.getElementById('txt_satuan_dasar').innerText = parentText;
            } else {
                helper.classList.add('hidden');
            }
        }

        // Tambahkan listener ke input quantity juga
        document.getElementsByName('base_quantity')[0].addEventListener('input', updateLabel);

        // Jalankan fungsi saat user mengetik nama satuan
        document.getElementsByName('name')[0].addEventListener('input', updateLabel);

        function confirmDelete(button) {
            const form = button.closest('form');
            Swal.fire({
                title: 'Hapus Satuan?',
                text: "Pastikan satuan ini tidak sedang digunakan oleh produk!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
            }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        }
    </script>
</x-app-layout>
