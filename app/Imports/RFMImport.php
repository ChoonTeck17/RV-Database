<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RFMImport implements ToCollection
{
    // private $segmentType;
    // public function __construct($segmentType){
    //     $this->segmentType = $segmentType;
    // }

    public function collection(Collection $rows)
    {
        $rows->shift(); // Remove header row if present (assuming first row is a header)

        foreach ($rows as $row) {
            $card_no = $row[0] ?? null;
            if (!$card_no) {
                continue; // Skip if no card number
            }

            $email                  = $row[1] ?? null;
            $last_name              = $row[2] ?? null;
            $phone_no               = $row[3] ?? null;
            $brand                  = $row[4] ?? null;
            $segment                = $row[4] . ' '. $row[5] ?? null;
            
            $column_type = $brand == 'mfm' ? 'mfm_segment' : ($brand == 'tr' ? 'tr_segment' : ($brand == 'nyss' ? 'nyss_segment' : null));
            
            
            if($column_type && $email){
                DB::table('bnb')->updateOrInsert(
                    ['card_no' => $card_no, 'email'=> $email], // Ensure unique card_no
                    
                    [
                        'last_name'             => $last_name,
                        'phone_no'              => $phone_no,
                        'brand'      => DB::raw(
                            "IF(brand IS NULL OR brand = '', '$brand', " .
                                    "IF(FIND_IN_SET('$brand', brand), brand, CONCAT(brand, ',', '$brand')))"
                        ),
                        $column_type            => $segment,
                        'updated_at'            => now(),
                    ]
                );
            }
        }
    }

    private function parseDate($value)
    {
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1900, 1, 1)->addDays($value - 2)->toDateString();
        }

        return Carbon::parse(trim($value))->toDateString();
    }

    private function parseMonth($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse(trim($value))->format('F'); // Output: "January", "February", etc.
        } catch (\Exception $e) {
            return null;
        }
    }
}
