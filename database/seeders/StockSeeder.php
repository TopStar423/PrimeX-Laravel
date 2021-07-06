<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Util\StockImport;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stock_csv_path = storage_path('app/' . 'primex-stock-test.csv');
        $stocks = (new StockImport)->toArray($stock_csv_path);
        $data = array_map(function($stock) {
            return [
                'product_id' => is_numeric($stock['product_code']) ? $stock['product_code'] : 0,
                'on_hand' => $stock['on_hand'],
                'production_date' => date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $stock['production_date'])))
            ];
        }, $stocks[0]);

        DB::table('stocks')->insert($data);
    }
}
