<?php

namespace App\Mail;

class Mail
{
    public static function send($email, $title, $content)
    {
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'From: Matcha <mail@matcha.com>' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        mail($email, $title, $content, $headers);
    }
}
