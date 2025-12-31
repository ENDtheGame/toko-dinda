<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin - Toko Dinda</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .sidebar-link-active {
            background: rgba(255, 255, 255, 0.1);
            border-left: 4px solid #6366f1;
            /* Indigo 500 */
            color: #ffffff !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 10px;
        }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    <div class="min-h-screen flex overflow-hidden">

        <aside class="w-72 bg-slate-900 text-slate-300 hidden md:flex flex-col shadow-2xl transition-all duration-300">
            <div class="p-8 text-xl font-extrabold text-white tracking-tight flex items-center space-x-3">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-store text-sm"></i>
                </div>
                <span>Toko Dinda</span>
            </div>

            <nav class="flex-grow px-4 pb-4 space-y-1 custom-scrollbar overflow-y-auto">
                <div class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Menu Utama</div>

                <a href="{{ route('dashboard') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                    <i class="fas fa-home w-6 opacity-75 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('products.index') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group {{ request()->routeIs('products.*') ? 'sidebar-link-active' : '' }}">
                    <i class="fas fa-box w-6 opacity-75 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Produk / Stok</span>
                </a>

                <a href="{{ route('categories.index') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group {{ request()->routeIs('categories.*') ? 'sidebar-link-active' : '' }}">
                    <i class="fas fa-tags w-6 opacity-75 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Kategori Produk</span>
                </a>

                <a href="{{ route('brands.index') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group {{ request()->routeIs('brands.*') ? 'sidebar-link-active' : '' }}">
                    <i class="fas fa-copyright w-6 opacity-75 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Manajemen Brand</span>
                </a>

                <div class="pt-6 px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Operasional</div>

                <a href="{{ route('units.index') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group {{ request()->routeIs('units.*') ? 'sidebar-link-active' : '' }}">
                    <i class="fas fa-balance-scale w-6 opacity-75 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Konversi Satuan</span>
                </a>

                <a href="{{ route('stock-histories.index') }}"
                    class="flex items-center py-3 px-4 rounded-xl transition-all duration-200 hover:bg-white/5 hover:text-white group {{ request()->routeIs('stock-histories.*') ? 'sidebar-link-active' : '' }}">
                    <i class="fas fa-history w-6 opacity-75 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium">Riwayat Stok</span>
                </a>

                <a href="{{ route('kasir.index') }}"
                    class="flex items-center py-3 px-4 rounded-xl bg-indigo-600/10 text-indigo-400 border border-indigo-600/20 mt-4 transition-all duration-200 hover:bg-indigo-600 hover:text-white group {{ request()->routeIs('sales.*') ? 'bg-indigo-600 text-white' : '' }}">
                    <i class="fas fa-cash-register w-6 group-hover:scale-110 transition-transform"></i>
                    <span class="font-bold tracking-wide">TRANSAKSI KASIR</span>
                </a>

                <div class="pt-6 px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Laporan</div>

                <a href="{{ route('reports.daily') }}"
                    class="flex items-center py-3 px-4 rounded-xl hover:bg-white/5 transition-all">
                    <i class="fas fa-chart-line w-6 opacity-75"></i> <span>Harian</span>
                </a>
                <a href="{{ route('reports.monthly') }}"
                    class="flex items-center py-3 px-4 rounded-xl hover:bg-white/5 transition-all">
                    <i class="fas fa-calendar-alt w-6 opacity-75"></i> <span>Bulanan</span>
                </a>
            </nav>

            <div class="p-6 bg-white/5 border-t border-white/5 flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-xs text-slate-500">Versi</span>
                    <span class="text-sm font-bold text-slate-300">1.0 - Toko Dinda</span>
                </div>
                <i class="fas fa-shield-halved text-slate-600"></i>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-y-auto">

            <header
                class="bg-white/80 backdrop-blur-md dark:bg-gray-800/80 sticky top-0 z-10 border-b border-gray-100 dark:border-gray-700 px-8 py-4 flex justify-between items-center shadow-sm">
                <div class="flex flex-col">
                    <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                        {{ $header ?? 'Panel Admin' }}
                    </h1>
                    <p class="text-xs text-gray-500 font-medium">Selamat bekerja, {{ Auth::user()->name }}!</p>
                </div>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3 pr-4 border-r border-gray-200 dark:border-gray-700">
                        <div
                            class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold text-sm">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <span
                            class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex items-center text-sm font-bold text-red-500 hover:text-red-700 transition-colors">
                            <i class="fas fa-power-off mr-2"></i> Keluar
                        </button>
                    </form>
                </div>
            </header>

            <main class="p-8">
                <script>
                    function confirmDelete(id) {
                        Swal.fire({
                            title: 'Hapus Data?',
                            text: "Tindakan ini tidak dapat dibatalkan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444', // Red 500
                            cancelButtonColor: '#6b7280', // Gray 500
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal',
                            borderRadius: '15px'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('delete-form-' + id)?.submit();
                            }
                        });
                    }
                    // ... fungsi modal lainnya ...
                </script>

                <div class="animate-in fade-in slide-in-from-bottom-4 duration-500">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>
