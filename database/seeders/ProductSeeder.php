<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Util\ProductImport;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products_csv_path = storage_path('app/' . 'primex-products-test.csv');
        $products = Excel::toArray(new ProductImport, $products_csv_path, null, \Maatwebsite\Excel\Excel::CSV);
        $data = array_map(function($product) {
            return [
                'id' => $product['code'],
                'code' => $product['code'],
                'name' => $product['name'],
                'description' => $product['description']
            ];
        }, $products[0]);

        DB::table('products')->insert($data);
    }
}
