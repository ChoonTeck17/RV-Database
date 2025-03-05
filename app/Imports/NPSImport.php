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
        $rows->shift(); // Remove header row if present (assuming first row is a header)

        foreach ($rows as $row) {

            // Access columns by header names
            $card_no = $row['card_number'] ?? null;
            if (!$card_no) {
                // dd($row);

                continue; // Skip if no card number
            }

            // $email = $row['email'] ?? null;
            // if (is_null($email)) {
            //     continue; // Skip if no email
            // }

            // Extract only the columns you care about
            $email= $row['email'] ?? null;
            // $last_name = $row['last_name'] ?? null;
            $last_name                  = $row['customer_name'] ?? null;
            $phone_no                   = $row['phone_number'] ?? null;
            $last_transaction_date      = $this->parseDate($row['date_time']) ?? null;
            $last_visited_store        = $row['store'] ?? null;
            

            // Build update data dynamically to avoid overwriting with null
            if($email){
                // Query 1: Update only card_no, email, name, phone, last transaction & visited store
                    DB::table('bnb')->updateOrInsert(
                        ['card_no' => $card_no], // Ensure unique card_no
                        [
                            'last_name'             => $last_name,
                            'email' => $email, // Add this line
                            'phone_no'              => $phone_no,
                            'last_transaction_date' => $last_transaction_date,
                            'last_visited_store'    => $last_visited_store,
                            'updated_at'            => now(),
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