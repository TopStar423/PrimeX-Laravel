<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Util\ProductImport;

class ImportProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
