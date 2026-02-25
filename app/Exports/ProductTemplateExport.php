<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ProductTemplateExport implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
          return collect([]);
    }

    public function headings(): array
    {
        return [
            'name',
            'category_id',
            'sku',
            'barcode',
            'price',
            'cost',
            'stock',
        ];
    }
}
