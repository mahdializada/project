<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class UserSheetExport implements FromCollection, WithMapping, WithTitle
{
    protected $init_status = '';


    public function __construct($init_status)
    {
        $this->init_status = $init_status;
    }


    public function collection(): \Illuminate\Support\Collection
    {
        return User::where('status', $this->init_status)->orderBy('firstname', 'asc')->get();
    }

    public function map($row): array
    {
        return [
            $row->firstname.' '.$row->lastname
        ];
    }

    public function title(): string
    {
        return 'users';
    }
}
