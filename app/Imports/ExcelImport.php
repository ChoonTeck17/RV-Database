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
    protected $updateMFM;
    protected $updateTR;
    protected $updateNYSS;

    // Constructor to accept selected segments
    public function __construct($updateMFM = false, $updateTR = false, $updateNYSS = false)
    {
        $this->updateMFM = $updateMFM;
        $this->updateTR = $updateTR;
        $this->updateNYSS = $updateNYSS;
    }

    public function collection(Collection $rows)
{
    $rows->shift(); // Remove header row if present

        foreach ($rows as $row) {
            $cardNo = $row[0] ?? null;
            if (!$cardNo) {
                continue; // Skip empty card numbers
            }

            $existingData = DB::table('bnb')->where('card_no', $cardNo)->first();

            $newData = [
                'email' => $row[1] ?? null,
                'last_name' => $row[2] ?? null,
                'phone_no' => $row[3] ?? null,
                'brand' => $row[4] ?? null,
                'last_transaction_date' => $this->parseDate($row[7] ?? null),
                'last_visited_store' => $row[8] ?? null,
                'remaining_points' => is_numeric($row[9] ?? null) ? (int) $row[9] : 0,
                'points_last_updated' => $this->parseDate($row[10] ?? null) ?: ($existingData->points_last_updated ?? null),
                'updated_at' => now(),
            ];

            // Extract segment name from "Segments" column
            $segmentName = isset($row[5]) ? trim($row[5]) : null;

            // **Update only selected segments** (set null if unchecked)
            $newData['mfm_segment'] = $this->updateMFM ? ('MFM ' . $segmentName) : ($existingData->mfm_segment ?? null);
            $newData['tr_segment'] = $this->updateTR ? ('TR ' . $segmentName) : ($existingData->tr_segment ?? null);
            $newData['nyss_segment'] = $this->updateNYSS ? ('NYSS ' . $segmentName) : ($existingData->nyss_segment ?? null);

            // **Log changes (if any)**
            if ($existingData) {
                $changes = [];
                foreach ($newData as $key => $value) {
                    if ($existingData->$key != $value) {
                        $changes[$key] = ['old' => $existingData->$key, 'new' => $value];
                    }
                }

                if (!empty($changes)) {
                    Log::info("Updated Record for Card No: $cardNo", $changes);
                }
            }

            // **Perform update or insert**
            DB::table('bnb')->updateOrInsert(['card_no' => $cardNo], $newData);
        }
    }

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
?>
