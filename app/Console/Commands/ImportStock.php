<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Util\StockImport;

class ImportStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:stock';

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
