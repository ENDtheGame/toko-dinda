<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kasir / Transaksi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('sales.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-bold mb-4 dark:text-white">Daftar Belanja</h3>

                            <table class="w-full text-left" id="cart-table">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 text-sm text-gray-500">
                                        <th class="pb-2">Produk</th>
                                        <th class="pb-2 w-24">Harga</th>
                                        <th class="pb-2 w-20 text-center">Qty</th>
                                        <th class="pb-2 w-32 text-right">Subtotal</th>
                                        <th class="pb-2 w-10"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-body" class="divide-y dark:divide-gray-700">
                                    </tbody>
                            </table>

                            <button type="button" onclick="addRow()" class="mt-4 text-sm font-bold text-blue-600 hover:text-blue-800">
                                + Tambah Baris (F2)
                            </button>
                        </div>
                    </div>

                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 sticky top-6">
                            <h3 class="text-lg font-bold mb-4 dark:text-white">Ringkasan Bayar</h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm text-gray-500">Total Belanja</label>
                                    <div class="text-3xl font-black text-blue-600" id="grand-total-display">Rp 0</div>
                                    <input type="hidden" name="total_price" id="grand-total-input">
                                </div>

                                <hr class="dark:border-gray-700">

                                <div>
                                    <label class="block text-sm font-medium dark:text-gray-300">Uang Bayar (Tunai)</label>
                                    <input type="number" id="cash-amount" class="w-full text-xl font-bold rounded-md border-gray-300 dark:bg-gray-700 dark:text-white" placeholder="0">
                                </div>

                                <div>
                                    <label class="text-sm text-gray-500">Kembalian</label>
                                    <div class="text-xl font-bold text-green-500" id="change-display">Rp 0</div>
                                </div>

                                <button type="submit" class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-black text-lg shadow-lg transition">
                                    PROSES TRANSAKSI (F10)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const products = @json($products);

        function addRow() {
            const tbody = document.getElementById('cart-body');
            const rowId = Date.now();
            const row = `
                <tr id="row-${rowId}" class="group">
                    <td class="py-3">
                        <select name="items[${rowId}][product_id]" onchange="updatePrice(this, ${rowId})" required class="w-full rounded-md border-gray-300 text-sm dark:bg-gray-700 dark:text-white">
                            <option value="">Pilih Barang...</option>
                            ${products.map(p => `<option value="${p.id}" data-price="${p.selling_price}">${p.name} (Stok: ${p.stock})</option>`).join('')}
                        </select>
                    </td>
                    <td class="py-3 px-2 text-sm text-gray-500" id="price-${rowId}">Rp 0</td>
                    <td class="py-3 text-center">
                        <input type="number" name="items[${rowId}][quantity]" value="1" min="1" oninput="calculate()" class="w-16 rounded-md border-gray-300 p-1 text-center dark:bg-gray-700 dark:text-white">
                    </td>
                    <td class="py-3 text-right font-bold dark:text-white subtotal" id="subtotal text-${rowId}">Rp 0</td>
                    <input type="hidden" class="subtotal-val" id="subtotal-val-${rowId}" value="0">
                    <td class="py-3 text-center">
                        <button type="button" onclick="document.getElementById('row-${rowId}').remove(); calculate()" class="text-red-500 hover:text-red-700 text-lg">×</button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        }

        function updatePrice(select, rowId) {
            const price = select.options[select.selectedIndex].getAttribute('data-price') || 0;
            document.getElementById(`price-${rowId}`).innerText = 'Rp ' + parseInt(price).toLocaleString('id-ID');
            calculate();
        }

        function calculate() {
            let total = 0;
            const rows = document.querySelectorAll('#cart-body tr');

            rows.forEach(row => {
                const price = row.querySelector('select').options[row.querySelector('select').selectedIndex].getAttribute('data-price') || 0;
                const qty = row.querySelector('input[type="number"]').value || 0;
                const subtotal = price * qty;

                row.querySelector('.subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
                row.querySelector('.subtotal-val').value = subtotal;
                total += subtotal;
            });

            document.getElementById('grand-total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('grand-total-input').value = total;
            updateChange();
        }

        function updateChange() {
            const total = parseInt(document.getElementById('grand-total-input').value) || 0;
            const cash = parseInt(document.getElementById('cash-amount').value) || 0;
            const change = cash - total;
            document.getElementById('change-display').innerText = 'Rp ' + (change > 0 ? change.toLocaleString('id-ID') : 0);
        }

        document.getElementById('cash-amount').addEventListener('input', updateChange);

        // Shortcut Keyboard
        window.addEventListener('keydown', (e) => {
            if (e.key === 'F2') addRow();
            if (e.key === 'F10') document.querySelector('form').submit();
        });

        // Tambah baris pertama saat load
        window.onload = addRow;
    </script>
</x-app-layout>
