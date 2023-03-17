<?php

namespace App\Exports;

use App\Models\Industry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class IndustrySheetExport implements FromCollection, WithTitle, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Industry::orderBy('name', 'asc')->get();
    }

    public function title(): string
    {
        return 'industries';
    }

    public function map($row): array
    {
        return [
            $row->name
        ];
    }
}
