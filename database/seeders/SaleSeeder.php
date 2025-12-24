<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sale;
use Carbon\Carbon;

class SaleSeeder extends Seeder
{
    public function run()
    {
        $dates = [
            Carbon::now()->subDays(3),
            Carbon::now()->subDays(2),
            Carbon::now()->subDay(),
            Carbon::now(),
        ];

        foreach ($dates as $date) {
           Sale::create([
    'product_id' => 1,
    'quantity' => 2,
    'total_price' => 5223,
    'sale_date' => now(),
]);

        }
    }
}
