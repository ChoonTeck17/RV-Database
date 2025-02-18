<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExcelImport implements ToCollection
{
    protected $updateMFM;
    protected $updateTR;
    protected $updateNYSS;
    protected $updateTADA;
    protected $updateRFM;
    protected $updateNPS;

    public function __construct($updateMFM = false, $updateTR = false, $updateNYSS = false, $updateTADA = false, $updateRFM = false, $updateNPS = false)
    {
        $this->updateMFM = $updateMFM;
        $this->updateTR = $updateTR;
        $this->updateNYSS = $updateNYSS;
        $this->updateTADA = $updateTADA;
        $this->updateRFM = $updateRFM;
        $this->updateNPS = $updateNPS;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // Remove header row

        foreach ($rows as $row) {
            $cardNo = $row[0] ?? null; // Card number is the primary key
            if (empty($newData['email']) || empty($newData['last_name'])) {
                Log::warning("Skipping Card No: $cardNo due to missing email or last name", $newData);
                continue;
            }

            $existingData = DB::table('bnb')->where('card_no', $cardNo)->first();
            if ($existingData) {
                Log::info("Existing data found for Card No: $cardNo", (array) $existingData);
            } else {
                Log::info("No existing data found for Card No: $cardNo, inserting new record.");
            }
            
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

            // Ensure all required fields are provided
            if (isset($newData['email']) && isset($newData['last_name'])) {
                DB::table('bnb')->updateOrInsert(
                    ['card_no' => $cardNo], // Primary key
                    [
                        'email' => $email,
                        'last_name' => $lastName,
                        'last_transaction_date' => $lastTransactionDate,
                        'last_visited_store' => $lastVisitedStore,
                        'updated_at' => now(),
                    ]
                );                Log::info("Inserted/Updated record for Card No: $cardNo", $newData);
            } else {
                Log::warning("Skipping record for Card No: $cardNo due to missing required fields", $newData);
            }
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
        if (!$value) return null;
    
        try {
            return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d'); // Convert to MySQL format
        } catch (\Exception $e) {
            try {
                return Carbon::parse($value)->format('Y-m-d'); // Fallback for different formats
            } catch (\Exception $e) {
                Log::error("Invalid date format for value: $value");
                return null;
            }
        }
    }
    
}