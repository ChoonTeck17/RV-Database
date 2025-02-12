<?php

namespace App\Imports;

use App\Models\Bnb;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;  

class ExcelImport implements ToCollection
{
    // public function collection(Collection $rows)
    // {
    //     $rows->shift();

    //     foreach ($rows as $row) {
            
    //         DB::table('bnb')->updateOrInsert(
    //             ['card_no' => $row[0]], // Condition: Match by card number
    //             [
    //                 'email' => $row[1],
    //                 'last_name' => $row[2],
    //                 'phone_no' => $row[3],
    //                 'brand' => $row[4],
    //                 'mfm_segment' => $row[5],
    //                 'tr_segment' => $row[6],
    //                 'nyss_segment' => $row[7],
    //                 'last_transaction_date' => $this->parseDate($row[8]),
    //                 'last_visited_store' => $row[9],
    //                 'remaining_points' => isset($row[10]) && is_numeric($row[10]) ? (int) $row[10] : 0,
    //                 'points_last_updated' => $this->parseDate($row[11]) ?? now(),
    //                 'updated_at' => now()
    //             ]
                
    //         );
    //     }
    // }

    public function collection(Collection $rows)
{
    $rows->shift(); // Remove header row if present

    foreach ($rows as $row) {
        $existingData = DB::table('bnb')->where('card_no', $row[0])->first();

        $newData = [
            'email' => $row[1] ?? null,
            'last_name' => $row[2] ?? null,
            'phone_no' => $row[3] ?? null,
            'brand' => $row[4] ?? null,
            'mfm_segment' => $row[5] ?? null,
            'tr_segment' => $row[6] ?? null,
            'nyss_segment' => $row[7] ?? null,
            'last_transaction_date' => isset($row[8]) ? $this->parseDate($row[8]) : null,
            'last_visited_store' => $row[9] ?? null,
            'remaining_points' => isset($row[10]) && is_numeric($row[10]) ? (int) $row[10] : 0,
            'points_last_updated' => isset($row[11]) ? $this->parseDate($row[11]) : now(),
            'updated_at' => now(),
        ];

        if ($existingData) {
            $existingArray = (array) $existingData;
            $changes = [];

            foreach ($newData as $key => $value) {
                if (isset($existingArray[$key]) && $existingArray[$key] != $value) {
                    $changes[$key] = [
                        'old' => $existingArray[$key],
                        'new' => $value
                    ];
                }
            }

            if (!empty($changes)) {
                Log::info("Updated Record for Card No: {$row[0]}", $changes);
            }
        }

        // Perform update or insert
        DB::table('bnb')->updateOrInsert(['card_no' => $row[0]], $newData);
    }
}

        private function parseDate($value)
        {
            if (!$value) {
                return null;
            }

            try {
                return Carbon::createFromFormat('d/m/Y', trim($value))->format('Y-m-d');
            } catch (\Exception $e) {
                Log::error("Invalid date format: " . $value);
                return null; // Return null if it's not a valid date
            }
        }

        }


