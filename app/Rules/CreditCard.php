<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\InvokableRule;

class CreditCard implements InvokableRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        if( strlen($value)!=16 || !in_array($value[0],[2,4,5,6,9]) || !$this->checkCardIsValid($value) ){
            $fail('The Card Number Is not Valid');
        }
    }

    protected function checkCardIsValid($card) : bool
    {
        $creditCardLength = strlen($card);
        for($i=0; $i<$creditCardLength; $i++)
        {
            $validCreditCard[$i] = $card[$i];
            if(($creditCardLength%2)==($i%2))
            {
                $validCreditCard[$i] *= 2;
                if($validCreditCard[$i]>9)
                    $validCreditCard[$i] -= 9;
            }
        }
        if (array_sum($validCreditCard)%10!=0){
            return false;
        }
        else
            return true;
    }
}
