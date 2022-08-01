<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $card_numbers = [
            '6037997462651796',
            '6104337768448506',
            '5892107044075003',
            '5022291900045731',
            '5894631523343325',
            '5892107044026337',
            '6362147010005732',
            '5029087000550593',
            '6104337725721243',
            '6104337823399843',
            '6037991950254546',
            '6104337768852021'
        ];
        $national_codes=[
            '2190147761',
            '2190112321'
        ];
        $phone_numbers=[
            '09364739598',
            '09195025921'
        ];
        $cards_used=0;
        for($u=0;$u<2;$u++){
            $user1=User::create([
                'name' => fake()->name,
                'national_code' => $national_codes[$u],
                'phone' => $phone_numbers[$u]
            ]);
            for($a=0;$a<3;$a++){
                $acc=Account::create([
                    'user_id' => $user1->id,
                    'account_number' => fake()->creditCardNumber(),
                    'balance' => 0
                ]);
                $total_balance = 0;
                for($c=0;$c<2;$c++){
                    $card = Card::create([
                        'account_id' => $acc->id,
                        'card_number' => $card_numbers[$cards_used],
                        'balance' => rand(10,100000)*1000,
                        'pin' => '1234'
                    ]);
                    $total_balance=$total_balance + $card->balance;
                    $cards_used++;
                }
                Account::find($acc->id)->update(['balance' => $total_balance]);
            }
        }


    }
}
