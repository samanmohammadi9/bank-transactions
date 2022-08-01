<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'card_number',
        'account_id',
        'pin',
        'balance'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id');
    }

    //for paying money
    public function deposit()
    {
        return $this->hasMany(Transaction::class,'origin');
    }

    //for recieving money
    public function receipt()
    {
        return $this->hasMany(Transaction::class,'destination');
    }
    use HasFactory;
}
