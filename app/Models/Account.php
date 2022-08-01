<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'user_id',
        'account_number',
        'balance'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class,'account_id');
    }
    use HasFactory;
}
