<?php

namespace App\Exports;

use App\Models\System;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SystemSheetExport implements FromCollection, withMapping, withTitle
{


    public function title(): string
    {
        return 'systems';
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): \Illuminate\Support\Collection
    {
        return System::all();
    }

    public function map($system): array
    {
        return [
            $system->name
        ];
    }
}
