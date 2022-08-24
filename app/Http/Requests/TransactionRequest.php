<?php

namespace App\Http\Requests;

use App\Rules\CreditCard;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */

    public function prepareForValidation() : void
    {
        $this->merge([
            'origin' => $this->convert_numbers($this->origin),
            'destination' => $this->convert_numbers($this->destination),
            'amount' => $this->convert_numbers($this->amount),
        ]);
    }

    /**
     * @param $string
     * @return array|string
     */
    protected function convert_numbers($string) : array|string
    {
        $persianNumbers = config('numbers.persian');
        $arabicNumbers = config('numbers.arabic');

        $num = range(0, 9);
        $convertedPersianNums = str_replace($persianNumbers, $num, $string);
        $englishNumbersOnly = str_replace($arabicNumbers, $num, $convertedPersianNums);
        return $englishNumbersOnly;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() : array
    {
        return [
            'origin' => ['string',new CreditCard()],
            'destination' => ['string',new CreditCard()],
            'amount' => ['numeric','min:10000','max:500000000']
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
