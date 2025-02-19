<?php

namespace App\Imports;

use App\Models\Bnb;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;  

// class ExcelImport implements ToCollection
// {
//     protected $updateMFM;
//     protected $updateTR;
//     protected $updateNYSS;
//     protected $NpsOnly; // New flag to determine which fields to update


//     // Constructor to accept selected segments
//     public function __construct($updateMFM = false, $updateTR = false, $updateNYSS = false, $NpsOnly = false)
//     {
//         $this->updateMFM = $updateMFM;
//         $this->updateTR = $updateTR;
//         $this->updateNYSS = $updateNYSS;
//         $this->NpsOnly = $NpsOnly; // Store the flag

//     }

//     public function collection(Collection $rows)
// {
//     $rows->shift(); // Remove header row if present

//         foreach ($rows as $row) {
//             $cardNo = $row[0] ?? null;
//             if (!$cardNo) {
//                 continue; // Skip empty card numbers
//             }

//             $existingData = DB::table('bnb')->where('card_no', $cardNo)->first();

//             if ($this->NpsOnly) {
//                 // Only update selected fields
//                 $newData = [
//                     'email' => $row[1] ?? null,
//                     'last_name' => $row[2] ?? null,
//                     'last_transaction_date' => $this->parseDate($row[7] ?? null),
//                     'last_visited_store' => $row[8] ?? null,
//                     'updated_at' => now(),
//                 ];
//             } else{
//             $newData = [
//                 'email' => $row[1] ?? null,
//                 'last_name' => $row[2] ?? null,
//                 'phone_no' => $row[3] ?? null,
//                 'brand' => $row[4] ?? null,
//                 'last_transaction_date' => $this->parseDate($row[7] ?? null),
//                 'last_visited_store' => $row[8] ?? null,
//                 'remaining_points' => is_numeric($row[9] ?? null) ? (int) $row[9] : 0,
//                 'points_last_updated' => $this->parseDate($row[10] ?? null) ?: ($existingData->points_last_updated ?? null),
//                 'updated_at' => now(),
//             ];

//             // Extract segment name from "Segments" column
//             $segmentName = isset($row[5]) ? trim($row[5]) : null;

//             // **Update only selected segments** (set null if unchecked)
//             $newData['mfm_segment'] = $this->updateMFM ? ('MFM ' . $segmentName) : ($existingData->mfm_segment ?? null);
//             $newData['tr_segment'] = $this->updateTR ? ('TR ' . $segmentName) : ($existingData->tr_segment ?? null);
//             $newData['nyss_segment'] = $this->updateNYSS ? ('NYSS ' . $segmentName) : ($existingData->nyss_segment ?? null);

//         }
//             // **Log changes (if any)**
//             if ($existingData) {
//                 $changes = [];
//                 foreach ($newData as $key => $value) {
//                     if ($existingData->$key != $value) {
//                         $changes[$key] = ['old' => $existingData->$key, 'new' => $value];
//                     }
//                 }

//                 if (!empty($changes)) {
//                     Log::info("Updated Record for Card No: $cardNo", $changes);
//                 }
//             }

//             // **Perform update or insert**
//             DB::table('bnb')->updateOrInsert(['card_no' => $cardNo], $newData);
//         }
//     }

class ExcelImport implements ToCollection
{
    private $updateMFM;
    private $updateTR;
    private $updateNYSS;

    public function __construct($updateMFM, $updateTR, $updateNYSS)
    {
        $this->updateMFM = $updateMFM;
        $this->updateTR = $updateTR;
        $this->updateNYSS = $updateNYSS;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // Remove header row if present (assuming first row is a header)
    
        foreach ($rows as $row) {
            // dd($row);
            $cardNo = $row[0] ?? null;
            if (!$cardNo) {
                continue; // Skip if no card number
            }
    
            $brandValue = $row[4] ?? null;    // ✅ Correct column for brands
            $segmentValue = $row[5] ?? null;  // ✅ Fix: Assign segments to column 6 instead
    
            //  NPS Data Update
            $npsData = [
                'email' => $row[1] ?? null,
                'last_name' => $row[2] ?? null,
                'phone_no' => $row[3] ?? null,
                'last_transaction_date' => $this->parseDate($row[4] ?? null),
                'last_visited_store' => $row[5] ?? null,
                'updated_at' => now(),
            ];
            DB::table('bnb')->updateOrInsert(['card_no' => $cardNo], $npsData);
    
            //  Retrieve existing segment data
            $existingData = DB::table('bnb')->where('card_no', $cardNo)->first();
    
            //  RFM Data Update (Updating the correct segment column)

    
            if ($this->updateMFM) {
                $rfmData['mfm_segment'] = $segmentValue;
            } else {
                $rfmData['mfm_segment'] = $existingData->mfm_segment ?? null;
            }
    
            if ($this->updateTR) {
                $rfmData['tr_segment'] = $segmentValue;
            } else {
                $rfmData['tr_segment'] = $existingData->tr_segment ?? null;
            }
    
            if ($this->updateNYSS) {
                $rfmData['nyss_segment'] = $segmentValue;
            } else {
                $rfmData['nyss_segment'] = $existingData->nyss_segment ?? null;
            }

            $rfmData = [
                'brand' => $brandValue,
                'updated_at' => now(),
            ];
    
            DB::table('bnb')->where('card_no', $cardNo)->update($rfmData);
        }
    }
    


    private function parseDate($value)
    {
        if (!$value) {
            return null;
        }
    
        // If the value is numeric, it's an Excel serial date
        if (is_numeric($value)) {
            return Carbon::create(1900, 1, 1)->addDays($value - 2)->toDateString();
        }
    
        // Try different date formats
        $formats = ['d/m/Y', 'Y-m-d', 'm/d/Y', 'd-M-Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value))->toDateString();
            } catch (\Exception $e) {
                continue;
            }
        }
    
        // Last fallback (automatic parsing)
        try {
            return Carbon::parse(trim($value))->toDateString();
        } catch (\Exception $e) {
            Log::error("Date parsing failed for value: " . json_encode($value));
            return null;
        }
    }
    
}
?>
