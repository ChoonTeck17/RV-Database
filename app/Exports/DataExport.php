<?php
namespace App\Exports;

use App\Models\Bnb;
use Maatwebsite\Excel\Concerns\FromCollection;

class DataExport implements FromCollection
{
    public function collection()
    {
        return Bnb::all();
    }
}

?>