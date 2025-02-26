<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExcelImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->shift(); // Remove header row if present (assuming first row is a header)

        foreach ($rows as $row) {
            $cardNo = $row[0] ?? null;
            if (!$cardNo) {
                continue; // Skip if no card number
            }

            $email                     = $row[1] ?? null;
            $lastName                  = $row[2] ?? null;
            $phoneNo                   = $row[3] ?? null;
            $remainingPoints           = $row[4] ?? 0;  // FIXED: Now correctly assigned
            $pointsLastUpdated         = $this->parseDate($row[5] ?? null); 
            $pointsLastUpdatedMonth    = $this->parseMonth($row[6] ?? null); // FIXED: "January" now goes here

            // Query 1: Update only card_no, email, name, phone, last transaction & visited store
            DB::table('bnb')->updateOrInsert(
                ['card_no' => $cardNo], // Ensure unique card_no
                [
                    'email'                 => $email,
                    'last_name'             => $lastName,
                    'phone_no'              => $phoneNo,
                    'updated_at'            => now(),
                ]
            );

            // Query 2: Update only card_no, email, name, phone, points-related data
            DB::table('bnb')->updateOrInsert(
                ['card_no' => $cardNo], // Ensure unique card_no
                [
                    'remaining_points'          => $remainingPoints,
                    'points_last_updated'       => $pointsLastUpdated,
                    'points_last_updated_month' => $pointsLastUpdatedMonth,
                    'updated_at'                => now(),
                ]
            );
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
