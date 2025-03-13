<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NPSImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Removed $rows->shift() since WithHeadingRow handles headers
        // Uncomment to debug all rows: dd($rows->toArray());

        foreach ($rows as $row) {
            $card_no = $row['card_number'] ?? null;
            if (!$card_no) {
                continue; // Skip if no card number
            }

            $email = $row['email'] ?? null;
            $last_name = $row['customer_name'] ?? null;
            $phone_no = $row['phone_number'] ?? null;
            $last_transaction_date = $this->parseDate($row['date_time']) ?? null;
            $last_visited_store = $row['store'] ?? null;

            // Uncomment to debug each row: dd($row->toArray());

            if ($email) {
                DB::table('bnb')->updateOrInsert(
                    ['card_no' => $card_no],
                    [
                        'last_name' => $last_name,
                        'email' => $email,
                        'phone_no' => $phone_no,
                        'last_transaction_date' => $last_transaction_date,
                        'last_visited_store' => $last_visited_store,
                        'source' => 'nps', // Tag as NPS data
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    private function parseDate($value)
    {
        if (is_null($value) || trim($value) == '') {
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
}