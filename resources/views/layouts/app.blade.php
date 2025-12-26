<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - Toko Dinda</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex overflow-hidden">

        <aside class="w-64 bg-gray-800 dark:bg-gray-950 text-white hidden md:flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-gray-700">
                Toko Dinda
            </div>
            <nav class="flex-grow p-4 space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('products.index') }}"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('products.*') ? 'bg-gray-700' : '' }}">
                    Produk / Stok
                </a>
                <a href="{{ route('categories.index') }}"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('categories.*') ? 'bg-gray-700' : '' }}">
                    Kategori Produk
                </a>
                <a href="{{ route('brands.index') }}"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('brands.*') ? 'bg-gray-700' : '' }}">
                    Manajemen Brand
                </a>
                <a href="{{ route('units.index') }}"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('units.*') ? 'bg-gray-700' : '' }}">
                    Konversi Satuan
                </a>
                <a href="{{ route('kasir.index') }}"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('sales.*') ? 'bg-gray-700' : '' }}">
                    Transaksi Kasir
                </a>
                <div class="pt-4 pb-2 text-xs font-semibold text-gray-500 uppercase">Laporan</div>
                <a href="{{ route('reports.daily') }}"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    Laporan Harian
                </a>
                <a href="{{ route('reports.monthly') }}"
                    class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">
                    Laporan Bulanan
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700 text-sm text-gray-400">
                v1.0 - Toko Dinda
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-y-auto">

            <header
                class="bg-white dark:bg-gray-800 shadow-sm border-b dark:border-gray-700 px-6 py-4 flex justify-between items-center">
                <div class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                    {{ $header ?? 'Panel Admin' }}
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-gray-600 dark:text-gray-400">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Logout</button>
                    </form>
                </div>
            </header>

            <main class="p-6">
                <script>
                    // Pastikan fungsi ini bersih dari karakter aneh
                    function confirmDelete(id) {
                        Swal.fire({
                            title: 'Apakah kamu yakin?',
                            text: "Data produk ini akan dihapus permanen!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Mencari form berdasarkan ID dan mengirimkannya
                                const form = document.getElementById('delete-form-' + id);
                                if (form) {
                                    form.submit();
                                }
                            }
                        });
                    }

                    // Fungsi modal lainnya tetap diletakkan di bawah sini
                    function openModal(id) {
                        const modal = document.getElementById(id);
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }

                    function closeModal(id) {
                        const modal = document.getElementById(id);
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }

                    function openEditModal(product) {
                        document.getElementById('formEdit').action = '/products/' + product.id;
                        document.getElementById('edit_name').value = product.name;
                        document.getElementById('edit_category_id').value = product.category_id;
                        document.getElementById('edit_purchase_price').value = product.purchase_price;
                        document.getElementById('edit_selling_price').value = product.selling_price;
                        document.getElementById('edit_stock').value = product.stock;
                        document.getElementById('edit_unit').value = product.unit;
                        openModal('modalEdit');
                    }
                </script>

                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
