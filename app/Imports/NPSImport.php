<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NPSImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->shift(); // Remove header row if present (assuming first row is a header)

        foreach ($rows as $row) {
            $card_no = $row[0] ?? null;
            if (!$card_no) {
                continue; // Skip if no card number
            }

            $email                     = $row[1] ?? null;
            $last_name                  = $row[2] ?? null;
            $phone_no                   = $row[3] ?? null;
            $last_transaction_date      = $this->parseDate($row[4]) ?? null;
            $last_visited_store        = $row[5] ?? null;

            if($email){
            // Query 1: Update only card_no, email, name, phone, last transaction & visited store
                DB::table('bnb')->updateOrInsert(
                    ['card_no' => $card_no, 'email'=>$email], // Ensure unique card_no
                    [
                        'last_name'             => $last_name,
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
        if (!$value) {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1900, 1, 1)->addDays($value - 2)->toDateString();
        }

        return Carbon::parse(trim($value))->toDateString();
    }
}
