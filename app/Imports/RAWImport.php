<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RAWImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->shift(); // Remove header row if present (assuming first row is a header)

        foreach ($rows as $row) {
            $card_no = $row[0] ?? null;
            if (!$card_no) {
                continue; // Skip if no card number
            }

            $email                     = $row[1] ?? null;
            $last_name                  = $row[2] ?? null;
            $phone_no                   = $row[3] ?? null;
            $remaining_points          = $row[4] ?? 0;  // FIXED: Now correctly assigned
            $points_last_updated         = $this->parseDate($row[5] ?? null); 
            $points_last_updated_month    = $this->parseMonth($row[6] ?? null); // FIXED: "January" now goes here
            
            if($email && $remaining_points >= 150){
                DB::table('bnb')->updateOrInsert(
                    ['card_no' => $card_no, 'email' => $email], // Ensure unique card_no
                    [
                        'last_name'             => $last_name,
                        'phone_no'              => $phone_no,
                        'remaining_points'          => $remaining_points,
                        'points_last_updated'       => $points_last_updated,
                        'points_last_updated_month' => $points_last_updated_month,
                        'updated_at'                => now(),
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
