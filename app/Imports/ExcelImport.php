<?php

namespace App\Imports;

use App\Models\Bnb;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;  

class ExcelImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // Skip the header row

            Bnb::create([
                'card_no' => $row[0],
                'email' => $row[1],
                'last_name' => $row[2],
                'phone_no' => $row[3],
                'brand' => $row[4],
                'mfm_segment' => $row[5],
                'tr_segment' => $row[6],
                'nyss_segment' => $row[7],
                'last_transaction_date' => $this->parseDate($row[8]),
                'last_visited_store' => $row[9],
                'remaining_points' => $row[10] ?? 0,
                'points_last_updated' => $this->parseDate($row[11]) ?? now(),
            ]);
        }
    }
            private function parseDate($value)
            {
                if (!$value) {
                    return null;
                }

                try {
                    // Check if the date is in "DD/MM/YYYY" or "MM/DD/YYYY" format
                    return Carbon::createFromFormat('d/m/Y', $value) // Try DD/MM/YYYY
                        ?? Carbon::createFromFormat('m/d/Y', $value) // Try MM/DD/YYYY
                        ?? Carbon::parse($value); // Try default parsing
                } catch (\Exception $e) {
                    return null; // Return null if it's not a valid date
                }
            }
        }


