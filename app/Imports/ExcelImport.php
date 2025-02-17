<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExcelImport implements ToCollection
{
    protected $updateTADA;

    public function __construct($updateTADA = false)
    {
        $this->updateTADA = $updateTADA;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // Remove header row

        foreach ($rows as $row) {
            $cardNo = $row[0] ?? null; // Card number is the primary key
            if (!$cardNo) {
                continue; // Skip empty card numbers
            }

            $existingData = DB::table('bnb')->where('card_no', $cardNo)->first();

            // Initialize the update array
            $newData = ['updated_at' => now()];

            // **TADA Segment** - Ensure specific fields are updated
            if ($this->updateTADA) {
                $email = $row[1] ?? null;  // Ensure correct index for email
                $lastName = $row[2] ?? null;
                $lastTransactionDate = $this->parseDate($row[3] ?? null);
                $lastVisitedStore = $row[4] ?? null;

                $newData = array_merge($newData, [
                    'email' => $email,
                    'last_name' => $lastName,
                    'last_transaction_date' => $lastTransactionDate,
                    'last_visited_store' => $lastVisitedStore,
                ]);

                // Log changes for debugging
                Log::info("Updating TADA fields for Card No: $cardNo", [
                    'email' => $email,
                    'last_name' => $lastName,
                    'last_transaction_date' => $lastTransactionDate,
                    'last_visited_store' => $lastVisitedStore,
                ]);

                $this->updateTADAFile($cardNo, $newData); // Update TADA dataset as well
            }

            // Perform insert or update on `bnb` table
            DB::table('bnb')->updateOrInsert(['card_no' => $cardNo], $newData);
        }
    }

    // Update TADA dataset
    protected function updateTADAFile($cardNo, $newData)
    {
        DB::table('tada_raw_files')->updateOrInsert(['card_no' => $cardNo], array_merge($newData, ['updated_at' => now()]));

        Log::info("Updated TADA Data for Card No: $cardNo", $newData);
    }

    // Helper method to parse dates
    private function parseDate($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value); // Fallback for different formats
            } catch (\Exception $e) {
                return null;
            }
        }
    }
}
