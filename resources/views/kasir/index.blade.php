<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kasir Grosir') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

                <div class="md:col-span-5 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 h-fit sticky top-6">
                    <h3 class="text-lg font-bold mb-4 dark:text-white border-b pb-2">🛒 Keranjang Belanja</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs uppercase text-gray-500 border-b dark:border-gray-700">
                                    <th class="py-2">Barang</th>
                                    <th class="py-2 text-center">Qty</th>
                                    <th class="py-2 text-right">Subtotal</th>
                                    <th class="py-2 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="cart-table" class="text-sm"></tbody>
                        </table>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <div class="flex justify-between text-xl font-bold dark:text-white mb-4">
                            <span>TOTAL:</span>
                            <span id="grand-total" class="text-blue-600">Rp 0</span>
                        </div>
                        <button onclick="processTransaction()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition-all active:scale-95">
                            PROSES TRANSAKSI (F10)
                        </button>
                    </div>
                </div>

                <div class="md:col-span-7 space-y-4">
                    <div class="bg-white dark:bg-gray-800 p-4 shadow-sm sm:rounded-lg">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('transaction.index') }}"
                                class="px-4 py-2 text-xs font-medium rounded-full transition-colors {{ !request('category_id') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                Semua
                            </a>
                            @foreach ($categories as $cat)
                                <a href="{{ route('transaction.index', ['category_id' => $cat->id]) }}"
                                    class="px-4 py-2 text-xs font-medium rounded-full transition-colors {{ request('category_id') == $cat->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 p-6 shadow-sm sm:rounded-lg">
                        <div class="relative mb-6">
                            <input type="text" id="search-product" placeholder="Cari nama produk..."
                                class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-300 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 overflow-y-auto max-h-[600px] pr-2"
                            id="product-list">
                            @foreach ($products as $product)
                                <button onclick="addToCart({{ json_encode($product) }})"
                                    class="product-card text-left p-3 border dark:border-gray-700 rounded-xl hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-gray-700 transition-all relative group {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                    @if ($product->stock <= 0)
                                        <div
                                            class="absolute inset-0 flex items-center justify-center z-10 bg-white/50 rounded-xl">
                                            <span
                                                class="bg-red-600 text-white text-[10px] px-2 py-1 rounded shadow-lg uppercase font-bold">Habis</span>
                                        </div>
                                    @endif

                                    <div
                                        class="text-[9px] text-blue-500 font-bold uppercase tracking-tighter leading-none mb-1">
                                        {{ $product->brand->name ?? 'No Brand' }}
                                    </div>

                                    <div class="font-bold text-gray-800 dark:text-white text-sm line-clamp-2 mb-2 h-10">
                                        {{ $product->name }}</div>
                                    <div class="flex flex-col gap-1">
                                        <span class="text-blue-600 font-extrabold text-sm">Rp
                                            {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                        <span class="text-[10px] text-gray-400">Stok: {{ $product->stock }}</span>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    #receipt-area {
        display: none;
    }

    @media print {
        body * {
            visibility: hidden !important;
        }

        #receipt-area,
        #receipt-area * {
            visibility: visible !important;
            display: block !important;
        }

        #receipt-area {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            color: #000;
        }
    }

    .flex-between {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    .text-center {
        text-align: center;
    }

    .border-top {
        border-top: 1px dashed #000;
        margin-top: 5px;
        padding-top: 5px;
    }
</style>

<div id="receipt-area">
    <div class="text-center">
        <h3 style="margin:0; font-size: 14pt; text-transform: uppercase; font-weight: bold;">Toko Dinda</h3>
        <p style="margin:0; font-size: 9pt;">Jl. Raya Cigedug</p>
        <p style="margin:0;">---------------------------</p>
    </div>
    <div style="font-size: 9pt; margin-bottom: 5px;">
        <div class="flex-between"><span>Tgl: <span id="r-date"></span></span></div>
        <div>No: <span id="r-inv"></span></div>
    </div>
    <div class="border-top" id="r-items"></div>
    <div class="border-top" style="font-size: 10pt;">
        <div class="flex-between" style="font-weight: bold;"><span>TOTAL:</span><span id="r-total"></span></div>
        <div class="flex-between"><span>BAYAR:</span><span id="r-pay"></span></div>
        <div class="flex-between"><span>KEMBALI:</span><span id="r-change"></span></div>
    </div>
    <div class="text-center" style="margin-top: 15px; font-size: 8pt;">
        <p>*** TERIMA KASIH ***</p>
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
                Swal.fire('Stok Habis!', '', 'error');
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
                <tr class="border-b dark:border-gray-700">
                    <td class="py-2 font-bold text-xs text-white">
                        <div class="text-[8px] text-blue-400 uppercase">${item.brand ? item.brand.name : ''}</div>
                        ${item.name}
                    </td>
                    <td class="py-2 text-center">
                        <input type="number" value="${item.qty}" min="1" onchange="updateQty(${index}, this.value)" class="w-12 p-1 text-xs text-black">
                    </td>
                    <td class="py-2 text-right text-white">Rp ${subtotal.toLocaleString()}</td>
                    <td class="py-2 text-center"><button onclick="removeItem(${index})" class="text-red-500 font-bold">×</button></td>
                </tr>`;
        });
        document.getElementById('grand-total').innerText = 'Rp ' + total.toLocaleString();
    }

    // UPDATE PADA BAGIAN STRUK DI SCRIPT
    async function processTransaction() {
        if (cart.length === 0) return Swal.fire('Oops!', 'Keranjang kosong', 'error');
        const total = cart.reduce((sum, item) => sum + (item.selling_price * item.qty), 0);

        const {
            value: payAmount
        } = await Swal.fire({
            title: 'Pembayaran',
            text: 'Total: Rp ' + total.toLocaleString(),
            input: 'number',
            inputLabel: 'Uang Bayar',
            showCancelButton: true,
            inputValidator: (value) => {
                if (!value || value < total) return 'Uang kurang!';
            }
        });

        if (payAmount) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch("{{ route('kasir.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        total_price: total,
                        pay_amount: payAmount,
                        items: cart
                    })
                });

                const data = await response.json();

                if (data.success) {
                    document.getElementById('r-inv').innerText = data.invoice_number || 'INV-000';
                    document.getElementById('r-date').innerText = new Date().toLocaleString('id-ID');
                    document.getElementById('r-total').innerText = 'Rp ' + total.toLocaleString();
                    document.getElementById('r-pay').innerText = 'Rp ' + parseInt(payAmount).toLocaleString();
                    document.getElementById('r-change').innerText = 'Rp ' + (payAmount - total).toLocaleString();

                    let itemHtml = '';
                    cart.forEach(item => {
                        // TAMBAHAN BRAND DI BARIS STRUK
                        const brandName = item.brand ? `[${item.brand.name}] ` : '';
                        itemHtml += `<div style="margin-bottom:4px;">
                                        <div style="text-transform:uppercase; font-size:8pt;">${brandName}${item.name}</div>
                                        <div class="flex-between">
                                            <span>${item.qty} x ${item.selling_price.toLocaleString()}</span>
                                            <span>${(item.qty * item.selling_price).toLocaleString()}</span>
                                        </div>
                                     </div>`;
                    });
                    document.getElementById('r-items').innerHTML = itemHtml;

                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil',
                        text: 'Kembalian: Rp ' + (payAmount - total).toLocaleString(),
                        showCancelButton: true,
                        confirmButtonText: 'Cetak Struk',
                        cancelButtonText: 'Selesai',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.print();
                            window.location.reload();
                        } else {
                            window.location.reload();
                        }
                    });
                }
            } catch (err) {
                Swal.fire('Error!', 'Gagal menyambung ke server', 'error');
            }
        }
    }

    function updateQty(index, val) {
        cart[index].qty = parseInt(val);
        renderCart();
    }

    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    document.getElementById('search-product').addEventListener('keyup', function() {
        const val = this.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = card.innerText.toLowerCase().includes(val) ? 'block' : 'none';
        });
    });
</script>
