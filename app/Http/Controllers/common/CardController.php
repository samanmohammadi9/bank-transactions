<?php

namespace App\Http\Controllers\common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function check_card_valid($card)
    {
        $strlen = strlen($card);
        if($strlen!=16)
            return false;
       if(!in_array($card[0],[2,4,5,6,9]))
            return false;

        for($i=0; $i<$strlen; $i++)
        {
            $res[$i] = $card[$i];
            if(($strlen%2)==($i%2))
            {
                $res[$i] *= 2;
                if($res[$i]>9)
                    $res[$i] -= 9;
            }
        }
        return array_sum($res)%10==0?true:false;
    }
}
