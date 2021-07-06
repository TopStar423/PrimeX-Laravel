<?php
namespace App\Util;

use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return Product|null
     */
    public function model(array $row)
    {
        return [
            'id' => $row['code'],
            'code' => $row['code'],
            'name' => $row['name'],
            'description' => $row['description']
        ];
    }
}
