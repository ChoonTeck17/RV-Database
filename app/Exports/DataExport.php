<?php

namespace App\Exports;

use App\Models\Bnb;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DataExport implements FromCollection, WithHeadings
{
    protected $exportType;

    public function __construct($exportType = 'all')
    {
        $this->exportType = $exportType;
    }

    public function collection()
    {
        $columns = match ($this->exportType) {
            'raw' => [
                'card_no',
                'email',
                'last_name',
                'phone_no',
                'remaining_points',
                'points_last_updated',
                'points_last_updated_month',
            ],
            'nps' => [
                'card_no',
                'email',
                'last_name',
                'phone_no',
                'last_transaction_date',
                'last_visited_store',
            ],
            'rfm' => [
                'card_no',
                'email',
                'last_name',
                'phone_no',
                'brand',
                'mfm_segment',
                'tr_segment',
                'nyss_segment',
                'birthday',
            ],
            default => [
                'card_no',
                'email',
                'last_name',
                'phone_no',
                'last_transaction_date',
                'last_visited_store',
                'remaining_points',
                'points_last_updated',
                'points_last_updated_month',
            ],
        };

        $query = Bnb::select($columns);
        if ($this->exportType !== 'all') {
            $query->where('source', $this->exportType);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return match ($this->exportType) {
            'raw' => [
                'Card No',
                'Email',
                'Last Name',
                'Phone No',
                'Remaining Points',
                'Points Last Updated',
                'Points Last Updated Month',
            ],
            'nps' => [
                'Card No',
                'Email',
                'Last Name',
                'Phone No',
                'Last Transaction Date',
                'Last Visited Store',
            ],
            'rfm' => [
                'Card No',
                'Email',
                'Last Name',
                'Phone No',
                'Brand',
                'MFM Segment',
                'TR Segment',
                'NYSS Segment',
                'Birthday',
            ],
            default => [
                'Card No',
                'Email',
                'Last Name',
                'Phone No',
                'Last Transaction Date',
                'Last Visited Store',
                'Remaining Points',
                'Points Last Updated',
                'Points Last Updated Month',
            ],
        };
    }
}