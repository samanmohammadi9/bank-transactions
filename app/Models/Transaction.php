<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'origin',
        'destination',
        'amount',
        'status'
    ];

    public function origin_card()
    {
        return $this->belongsTo(Card::class,'origin');
    }

    public function destination_card()
    {
        return $this->belongsTo(Card::class,'destination');
    }
    use HasFactory;
}
