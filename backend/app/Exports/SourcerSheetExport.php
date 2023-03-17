<?php

namespace App\Exports;

use App\Models\Sourcer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SourcerSheetExport implements FromCollection, withMapping, withTitle
{
    public function title(): string
    {
        return 'sourcers';
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return Sourcer::all();
    }

    public function map($sourcer): array
    {
        return [
            $sourcer->name
        ];
    }
}
