<?php

namespace App\Http\Classes;

use http\Env;

class KavehSMSSender extends NotifSender
{

    public function send_notification($origin , $destination , $amount)
    {
        $curl = curl_init();
        $origin_text="تست برداشت مبلغ ".$amount;
        $origin_text="تست واریز مبلغ ".$amount;
        $curl_url = 'https://api.kavenegar.com/v1/'.
            Env("KAVEH_API_KEY").'/sms/send.json?receptor='.$origin.'&message='.$origin_text.'&sender=10008663';
        $rsp=curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic OlpQRkVTRzVMOTc=',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

    }
}
