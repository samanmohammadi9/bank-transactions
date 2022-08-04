<?php

namespace App\Http\Classes;

use http\Env;
use Symfony\Component\Yaml\Yaml;

class KavehSMSSender extends NotifSender
{

    public function send_notification($origin , $destination , $amount)
    {
        $curl = curl_init();
        $sms_file=Yaml::parse(file_get_contents('../sms.yml'));
        $origin_text=$sms_file['origin']['text1'].$amount.$sms_file['origin']['text2'];

        $curl_url = 'https://api.kavenegar.com/v1/'.
            Env("KAVEH_API_KEY").'/sms/send.json?receptor='.$origin.'&message='.$origin_text.'&sender=10008663';
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic OlpQRkVTRzVMOTc=',
                'Content-Type: application/json'
            ),
        ));

        $destination_text=$sms_file['destination']['text1'].$amount.$sms_file['destination']['text2'];
        $curl_url = 'https://api.kavenegar.com/v1/'.
            Env("KAVEH_API_KEY").'/sms/send.json?receptor='.$destination.'&message='.$destination_text.'&sender=10008663';
        curl_setopt_array($curl, array(
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
