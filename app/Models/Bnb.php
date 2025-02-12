<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bnb extends Model
{
    use HasFactory;

    protected $table = 'bnb'; // Ensure it matches the database table name

    protected $fillable = [
        'card_no', 'email', 'last_name', 'phone_no', 'brand',
        'mfm_segment', 'tr_segment', 'nyss_segment',
        'last_transaction_date', 'last_visited_store', 'remaining_points', 'points_last_updated'
    ];
}
