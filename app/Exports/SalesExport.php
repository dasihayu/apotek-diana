<?php

namespace App\Exports;

use App\Models\Sales;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Sales::with('product.purchase')->get();
    }

    public function headings(): array
    {
        return [
            'Medicine Name',
            'Quantity',
            'Total Price',
            'Date',
        ];
    }

    public function map($sales): array
    {
        return [
            $sales->product->purchase->name, // Mengambil nama produk dari relasi
            $sales->quantity,
            $sales->total_price,
            $sales->created_at->format('d M, Y'),
        ];
    }
}
