<?php

namespace App\Exports;

use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class RoleSheetExport implements FromCollection, withMapping, withTitle
{
    private $status = '';

    public function __construct($init_status)
    {
        $this->status = $init_status;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(): \Illuminate\Support\Collection
    {
        return Role::whereStatus($this->status)->orderBy('name', 'asc')->get();;
    }

    public function title(): string
    {
        return 'roles';
    }

    public function map($row): array
    {
        return [
            $row->name
        ];
    }
}
