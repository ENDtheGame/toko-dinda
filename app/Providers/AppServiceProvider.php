<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // Tambahkan ini

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useTailwind(); // Pastikan Laravel pakai Tailwind untuk paging
    }
}
