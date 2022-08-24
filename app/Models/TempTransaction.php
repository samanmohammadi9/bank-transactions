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

    public function store($origin , $destination , $amount)
    {
        $this->query()->create([
            'origin' => $origin,
            'destination' => $destination,
            'amount' => $amount
        ]);
    }
}
