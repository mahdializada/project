<?php

namespace App\Exports;

use App\Models\SubSystem;
use App\Models\Subsystemnt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubSystemSheetExport implements FromCollection, WithTitle, WithMapping
{

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return SubSystem::orderBy('name', 'asc')->get();
    }

    public function title(): string
    {
        return 'subsystems';
    }

    public function map($row): array
    {
        return [
            $row->name
        ];
    }
}
