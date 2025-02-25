<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            $cardNo = $row[0] ?? null;
            if (!$cardNo) {
                continue; // Skip if no card number
            }

            $brandValue   = $row[4] ?? null;
            $segmentValue = $row[5] ?? null; 

            // Update or Insert NPS Data into `bnb`
            $npsData = [
                'email'                 => $row[1] ?? null,
                'last_name'             => $row[2] ?? null,
                'phone_no'              => $row[3] ?? null,
                'last_transaction_date' => $this->parseDate($row[4] ?? null),
                'last_visited_store'    => $row[5] ?? null,
                'updated_at'            => now(),
            ];
            DB::table('bnb')->updateOrInsert(['card_no' => $cardNo], $npsData);

            // Retrieve existing segment data
            $existingData = DB::table('bnb')->where('card_no', $cardNo)->first();

            // Initialize the update array for segments
            $rfmData = [
                'brand'      => $brandValue,
                'segment'    => $segmentValue,
                'updated_at' => now(),
            ];

            // RFM Data Update (Updating the correct segment column)
            // if ($this->updateMFM) {
            //     $rfmData['mfm_segment'] = $segmentValue;
            // } else {
            //     $rfmData['mfm_segment'] = $existingData->mfm_segment ?? null;
            // }

            // if ($this->updateTR) {
            //     $rfmData['tr_segment'] = $segmentValue;
            // } else {
            //     $rfmData['tr_segment'] = $existingData->tr_segment ?? null;
            // }

            // if ($this->updateNYSS) {
            //     $rfmData['nyss_segment'] = $segmentValue;
            // } else {
            //     $rfmData['nyss_segment'] = $existingData->nyss_segment ?? null;
            // }

            // Perform update or insert for `bnb`
            DB::table('bnb')->updateOrInsert(['card_no' => $cardNo], $rfmData);

            // Update or Insert Data into `tada_raw_member`
            $tadaData = [
                'last_name'         => $row[2] ?? null, // Assuming last_name is member_name
                'phone_no'        => $row[3] ?? null,
                'email'               => $row[1] ?? null,
                'remaining_points'     => $row[12] ?? 0, // Assuming correct index
                'points_last_updated' => $this->parseDate($row[13] ?? null), // Assuming correct index
                'updated_at'          => now(),
            ];
            DB::table('bnb')->updateOrInsert(['card_no' => $cardNo], $tadaData);
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

        $formats = ['d/m/Y', 'Y-m-d', 'm/d/Y', 'd-M-Y'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, trim($value))->toDateString();
            } catch (\Exception $e) {
                continue;
            }
        }

        try {
            return Carbon::parse(trim($value))->toDateString();
        } catch (\Exception $e) {
            Log::error("Date parsing failed for value: " . json_encode($value));
            return null;
        }
    }
}
