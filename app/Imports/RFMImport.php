<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RFMImport implements ToCollection, WithHeadingRow
{
    protected $segments;

    public function __construct(array $segments = [])
    {
        $this->segments = $segments; // e.g., ['mfm', 'tr']
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $card_no = $row['card_no'] ?? null;
            if (!$card_no) {
                continue; // Skip if no card number
            }

            $email = $row['email'] ?? null;
            if (!$email) {
                continue; // Skip if no email
            }

            $updateData = [
                'last_name' => $row['name'] ?? null,
                'email' => $email,
                'phone_no' => $row['phone'] ?? null,
                'brand' => $row['brand'] ?? null,
                'source' => 'rfm', 
                'birthday' => $this->parseDate($row['birthday'] ?? null),
                'updated_at' => now(),
            ];

            // Assume the Excel file has a 'segment' column with values like "Champions"
            $segment = $row['segment'] ?? null;

            // Modify segment values based on checked boxes and store in respective columns
            if ($segment) {
                if (in_array('mfm', $this->segments)) {
                    $updateData['mfm_segment'] = "MFM $segment"; // e.g., "MFM Champions"
                }
                if (in_array('tr', $this->segments)) {
                    $updateData['tr_segment'] = "TR $segment"; // e.g., "TR Champions"
                }
                if (in_array('nyss', $this->segments)) {
                    $updateData['nyss_segment'] = "NYSS $segment"; // e.g., "NYSS Champions"
                }
            }

            DB::table('bnb')->updateOrInsert(
                ['card_no' => $card_no],
                $updateData
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

        try {
            return Carbon::parse(trim($value))->toDateString();
        } catch (\Exception $e) {
            return null;
        }
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