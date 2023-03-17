<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class DepartmentSheetExport implements FromCollection, WithMapping, WithTitle
{

    private $departments = [];

    public function __construct($departments)
    {
        $this->departments = $departments;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->departments;
    }

    public function title(): string
    {
        return 'departments';
    }

    public function map($row): array
    {
        return [
            $row->name
        ];
    }
}
