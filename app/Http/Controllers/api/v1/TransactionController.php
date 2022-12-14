<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\common\CardController;
use App\Http\Controllers\common\NumbersController;
use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Card;
use App\Models\TempTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    protected TempTransaction $tempTransaction;
    public function __construct()
    {
        $this->tempTransaction = new TempTransaction();
    }

    public function reserve_transaction(TransactionRequest $request)
    {
        $transactionData=$request->validated();
        //$this->tempTransaction->store( $transactionData['origin'] , $transactionData['destination'] , $transactionData['amount'] );

    }

    public function confirm_transaction(Request $request)
    {
        $temp_transaction=TempTransaction::find($request->temp_id);
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
            DB::insert('insert into fees (amount, transaction_id) values (?, ?)',
                [5000, $transaction->id]);

            $sms_driver = "App\\Http\\Classes\\".Env('SMS_DRIVER')."SMSSender";
            $notification_sender = new $sms_driver();

            $notification_sender->send_notification(
                $origin->account()->user()->phone,
                $destination->account()->user()->phone,
                $amount
            );
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
