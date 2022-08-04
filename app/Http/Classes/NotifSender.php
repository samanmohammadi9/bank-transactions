<?php

namespace App\Http\Classes;

abstract class NotifSender
{
    abstract public function send_notification($origin , $destination , $amount);
}
