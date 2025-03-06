<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RAWImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Access columns by header names
            $card_no = $row['cardno'] ?? null;

            if (!$card_no) {
                continue; // Skip if no card number
            }
            $email = $row['email'] ?? null;
            $last_name = $row['name'] ?? null;
            $phone_no = $row['mobile_number'] ?? null;
            $remaining_points = $row['remaining_point'] ?? 0; // Default to 0 if missing
            $points_last_updated =$row['points_last_updated_date'] ?? null;
            $points_last_updated = $this->parseDate($row['points_last_updated_date'] ?? null); // Parse date 
            $points_last_updated_month = $this->parseMonth($row['statement_month'] ?? null);
            
            if ($email) {
                DB::table('bnb')->updateOrInsert(
                    ['card_no' => $card_no],
                    [
                        'last_name' => $last_name,
                        'email' => $email,
                        'phone_no' => $phone_no,
                        'remaining_points' => $remaining_points,
                        'points_last_updated' => $points_last_updated,
                        'points_last_updated_month' => $points_last_updated_month,
                        'updated_at' => now(),
                    ]
                );
            }

            // Build update data dynamically to avoid overwriting with null
            $updateData = [
                'updated_at' => now(),
            ];

            // // Only include fields present in the row and not null
            // if ($row->has('last_name') && !is_null($last_name)) {
            //     $updateData['last_name'] = $last_name;
            // }
            // if ($row->has('phone_no') && !is_null($phone_no)) {
            //     $updateData['phone_no'] = $phone_no;
            // }
            // if ($row->has('remaining_points') && !is_null($remaining_points)) {
            //     $updateData['remaining_points'] = $remaining_points;
            // }
            // if ($row->has('points_last_updated') && !is_null($points_last_updated)) {
            //     $updateData['points_last_updated'] = $points_last_updated;
            // }
            // if ($row->has('points_last_updated_month') && !is_null($points_last_updated_month)) {
            //     $updateData['points_last_updated_month'] = $points_last_updated_month;
            // }

            
            // Update or insert only if email exists and points meet the threshold
            if ($email && $remaining_points >= 150) {
                DB::table('bnb')->updateOrInsert(
                    ['card_no' => $card_no, 'email' => $email],
                    $updateData
                );
            }
        }
    }

    private function parseDate($value)
    {
        if (is_null($value) || trim($value) === '') {
            return null;
        }

        // Handle Excel serial dates
        if (is_numeric($value)) {
            return Carbon::create(1900, 1, 1)->addDays($value - 2)->toDateString();
        }

        // Try parsing DD/MM/YYYY (e.g., 03/03/2025)
        try {
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', trim($value))) {
                return Carbon::createFromFormat('d/m/Y', trim($value))->toDateString();
            }
        } catch (\Exception $e) {
            // Continue to next format if this fails
        }

        // Try parsing YYYY-MM-DD (e.g., 2025-12-31) or other standard formats
        try {
            return Carbon::parse(trim($value))->toDateString();
        } catch (\Exception $e) {
            return null; // Return null if all parsing fails
        }
    }

    private function parseMonth($value)
    {
        if (is_null($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse(trim($value))->format('F'); // Output: "March", "December", etc.
        } catch (\Exception $e) {
            return null;
        }
    }
}