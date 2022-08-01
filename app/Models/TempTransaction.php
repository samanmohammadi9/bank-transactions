<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempTransaction extends Model
{
    protected $fillable=[
        'origin',
        'destination',
        'amount',
    ];
    use HasFactory;
}
