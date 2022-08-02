<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\common\CardController;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Card;
use App\Models\TempTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class TransactionController extends Controller
{
    public function reserve_transaction(Request $request)
    {
        $card_errors=$this->validate_cards($request->origin,$request->destination);
        if(!empty($card_errors)){
            return json_encode([
                'status' => 'failed',
                'data' => $card_errors
            ],true);
        }
        $card_errors=$this->find_cards($request->origin,$request->destination);
        if(!empty($card_errors)){
            return json_encode([
                'status' => 'failed',
                'data' => $card_errors
            ],true);
        }

        if($request->amount <10000 || $request->amount > 500000000){
            return json_encode([
                'status' => 'failed',
                'data' => ['Invalid Amount']
            ]);
        }

        $temp_transaction=TempTransaction::create([
            'origin' => $request->origin,
            'destination' => $request->destination,
            'amount' => $request->amount
        ]);
        return json_encode([
            'status' => 'success',
            'data' => [
                $temp_transaction
            ]
        ]);
    }

    public function confirm_transaction(Request $request)
    {
        $temp_transaction=TempTransaction::find($request->id);
        if($temp_transaction->status!=0){
            return json_encode([
                'status' => 'failed',
                'data' => [
                    'Request Not Available Or Expired'
                ]
            ]);
        }
        $origin=Card::where('card_number',$temp_transaction->origin)->first();
        $destination = Card::where('card_number',$temp_transaction->destination)->first();
        $amount=$temp_transaction->amount;
        if($request->pin != $origin->pin){
            $temp_transaction->update([
                'status' => 2
            ]);
            return json_encode([
                'status' => 'failed',
                'data' => [
                    'The Card Pin Is Incorrect'
                ]
            ]);
        }
        elseif($origin->balance<($amount)+5000){
            $temp_transaction->update([
                'status' => 2
            ]);
            return json_encode([
                'status' => 'failed',
                'data' => [
                    'Not Enough Balance In Card'
                ]
            ]);
        }
        else{
            $transaction=Transaction::create([
                'origin' => $origin->card_number,
                'destination' => $destination->card_number,
                'amount' => $amount
            ]);
            $origin->update([
                'balance' => $origin->balance - ($amount + 5000)
            ]);
            $origin->account()->update([
                'balance' => $origin->account()->balance - $amount+5000
            ]);
            $destination->update([
                'balance' => $destination->balance + $amount
            ]);
            $destination->account()->update([
                'balance' => $destination->account()->balance + $amount
            ]);
            $temp_transaction->update([
                'status' => 1
            ]);
            return json_encode([
                'status' => 'success',
                'data' => $transaction
            ]);
        }



    }

    public function validate_cards($origin,$destination)
    {
        $card_controller= new CardController();
        $errors=[];
        if(!$card_controller->check_card_valid($origin))
            array_push($errors,"Invalid Origin Card Number");
        if(!$card_controller->check_card_valid($destination))
            array_push($errors,"Invalid Destination Card Number");
        return $errors;
    }

    public function find_cards($origin,$destination)
    {
        $errors=[];
        $origin=Card::where('card_number',$origin)->first();
        $destination=Card::where('card_number',$destination)->first();
        if($origin==null){
            array_push($errors,'Origin Card Doesnt Exsist');
        }
        if($destination==null){
            array_push($errors,'destination Card Doesnt Exsist');
        }
        if(empty($errors) && $origin == $destination){
            array_push($errors,'Origin And Destination are same value');
        }
        return $errors;
    }
}
