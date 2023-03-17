<?php

namespace App\Exports;

use App\Models\Action;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActionSheetExport implements FromCollection, withTitle, withMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Action::all();
    }

    public function title(): string
    {
        return 'actions';
    }

    public function map($row): array
    {
        return [
            $row->name
        ];
    }
}
