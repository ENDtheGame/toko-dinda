<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kasir Grosir') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div class="md:col-span-2 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold mb-4 dark:text-white">Keranjang Belanja</h3>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b dark:border-gray-700 text-gray-500">
                                <th class="py-2">Barang</th>
                                <th class="py-2">Harga</th>
                                <th class="py-2 w-20">Qty</th>
                                <th class="py-2">Subtotal</th>
                                <th class="py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cart-table">
                        </tbody>
                    </table>

                    <div class="mt-6 border-t pt-4">
                        <div class="flex justify-between text-xl font-bold dark:text-white mb-4">
                            <span>TOTAL:</span>
                            <span id="grand-total">Rp 0</span>
                        </div>

                        <button onclick="processTransaction()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg transition">
                            PROSES TRANSAKSI
                        </button>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="mb-4 flex flex-wrap gap-2">
                        <a href="{{ route('transaction.index') }}"
                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full transition-colors {{ !request('category_id') ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}">
                            Semua
                        </a>

                        @foreach ($categories as $cat)
                            <div class="relative group">
                                <a href="{{ route('transaction.index', ['category_id' => $cat->id]) }}"
                                    class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full transition-colors {{ request('category_id') == $cat->id ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $cat->name }}
                                    @if ($cat->children->count() > 0)
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    @endif
                                </a>

                                @if ($cat->children->count() > 0)
                                    <div
                                        class="hidden group-hover:block absolute left-0 z-20 mt-1 min-w-[120px] bg-white dark:bg-gray-800 shadow-lg rounded-md border border-gray-200 dark:border-gray-600 p-1">
                                        @foreach ($cat->children as $child)
                                            <a href="{{ route('transaction.index', ['category_id' => $child->id]) }}"
                                                class="block px-3 py-1.5 text-[10px] uppercase tracking-wider text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-gray-700 rounded transition">
                                                ↳ {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <h3 class="text-lg font-bold mb-4 dark:text-white">Cari Barang</h3>
                    <input type="text" id="search-product" placeholder="Ketik nama barang..."
                        class="w-full mb-4 rounded-md border-gray-300 dark:bg-gray-700 dark:text-white focus:ring-blue-500">

                    <div class="space-y-2 overflow-y-auto max-h-96" id="product-list">
                        @foreach ($products as $product)
                            <button onclick="addToCart({{ $product }})"
                                class="w-full text-left p-3 border dark:border-gray-700 rounded-md hover:bg-blue-50 dark:hover:bg-gray-700 transition group">
                                <div class="font-bold dark:text-white">{{ $product->name }}</div>
                                <div class="flex justify-between text-sm text-gray-500">
                                    <span>Stok: {{ $product->stock }}</span>
                                    <span class="text-blue-600 font-bold group-hover:scale-110 transition">
                                        Rp {{ number_format($product->selling_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        let cart = [];

        function addToCart(product) {
            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                if (existing.qty < product.stock) {
                    existing.qty++;
                } else {
                    Swal.fire('Stok Habis!', 'Jumlah melebihi stok tersedia.', 'error');
                }
            } else {
                cart.push({
                    ...product,
                    qty: 1
                });
            }
            renderCart();
        }

        function renderCart() {
            const container = document.getElementById('cart-table');
            let total = 0;
            container.innerHTML = '';

            cart.forEach((item, index) => {
                const subtotal = item.selling_price * item.qty;
                total += subtotal;
                container.innerHTML += `
                    <tr class="border-b dark:border-gray-700 dark:text-gray-300">
                        <td class="py-4 font-bold">${item.name}</td>
                        <td class="py-4">Rp ${item.selling_price.toLocaleString()}</td>
                        <td class="py-4">
                            <input type="number" value="${item.qty}" min="1" onchange="updateQty(${index}, this.value)"
                                class="w-16 rounded border-gray-300 dark:bg-gray-700 text-black dark:text-white">
                        </td>
                        <td class="py-4 font-bold">Rp ${subtotal.toLocaleString()}</td>
                        <td class="py-4 text-center">
                            <button onclick="removeItem(${index})" class="text-red-500 font-bold">X</button>
                        </td>
                    </tr>
                `;
            });
            document.getElementById('grand-total').innerText = 'Rp ' + total.toLocaleString();
        }

        function updateQty(index, val) {
            cart[index].qty = parseInt(val);
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        // FUNGSI PROSES TRANSAKSI (Sudah Diperbaiki)
        async function processTransaction() {
            if (cart.length === 0) return Swal.fire('Oops!', 'Keranjang masih kosong', 'error');

            const total = cart.reduce((sum, item) => sum + (item.selling_price * item.qty), 0);

            const {
                value: payAmount
            } = await Swal.fire({
                title: 'Total Belanja',
                text: 'Total: Rp ' + total.toLocaleString(),
                input: 'number',
                inputLabel: 'Masukkan uang bayar',
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value || value < total) {
                        return 'Uang bayar kurang dari total belanja!';
                    }
                }
            });

            if (payAmount) {
                // Tampilkan loading sebentar
                Swal.showLoading();

                fetch("{{ route('kasir.store') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            total_price: total,
                            pay_amount: payAmount,
                            items: cart
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Transaksi Berhasil!',
                                text: 'Kembalian: Rp ' + (payAmount - total).toLocaleString(),
                            }).then(() => {
                                location.reload(); // Refresh untuk update stok
                            });
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        console.error(err);
                    });
            }
        }

        // Live Search
        document.getElementById('search-product').addEventListener('keyup', function() {
            const val = this.value.toLowerCase();
            const buttons = document.querySelectorAll('#product-list button');
            buttons.forEach(btn => {
                const text = btn.innerText.toLowerCase();
                btn.style.display = text.includes(val) ? 'block' : 'none';
            });
        });
    </script>
</x-app-layout>
