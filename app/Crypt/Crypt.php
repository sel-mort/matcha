<?php

namespace App\Crypt;

class Crypt
{
	public static function crypt_str($str)
	{
        $salt = '$2a$07$usesomadasdsadsadsadasdasdasdsadesillystringfors';
		return crypt($str, $salt);
    }
}