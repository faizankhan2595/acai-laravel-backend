<?php

namespace App\Exports;

use App\User;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
ini_set('memory_limit', '1024M');
class UsersExport implements FromQuery, WithMapping, WithColumnFormatting, WithHeadings, ShouldAutoSize,ShouldQueue
{
    use Exportable;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function query()
    {
        return User::query();
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->mobile_number,
            Date::dateTimeToExcel(Carbon::parse($user->dob)),
            Date::dateTimeToExcel(Carbon::parse($user->created_at)),
            $user->membership(),
            ($user->lastCredittransaction()) ? Date::dateTimeToExcel(Carbon::parse($user->lastCredittransaction()->created_at)) : 'N/A',
            $user->balance() . ' Points',
            ($user->gold_expiring_date) ? Date::dateTimeToExcel(Carbon::parse($user->gold_expiring_date)) : 'N/A',
            ($user->account_status == 1) ? 'Active' : 'Inactive',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => NumberFormat::FORMAT_DATE_DDMMYYYY,
        ];
    }
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Mobile Number',
            'Date Of Birth',
            'Join Date',
            'Membership Tier',
            'Last Transaction Date',
            'Balance Points',
            'Gold Membership Next Expiry',
            'Account Status',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:W1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }
}
