<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightHistoryData extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'api_url',
        'amount',
    ];
}
