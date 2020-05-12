<?php

namespace App\Validator;

class Validator
{
    public static function check_array($data)
    {
        $res = is_array($data) || $data instanceof Traversable ? true : false;
        return !$res;
    }

    public static function check_email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function check_username($username)
    {
        $regex = '/^[\w-]+$/';
        if (preg_match($regex, $username)) {
            return (1);
        } else {
            return (0);
        }
    }

    public static function check_password($password)
    {
        $regex = '/^(?=\S{5,64})(?=[^a-z]*[a-z]+)(?=[^A-Z]*[A-Z]+)(?=\S*[0-9]+)(?=\S*\W+)\S*$/';
        if (preg_match($regex, $password)) {
            return (1);
        } else {
            return (0);
        }
    }

    public static function check_lenght($value, $minlen, $maxlen)
    {
        return strlen($value) >= $minlen && strlen($value) < $maxlen;
    }

    public static function check_not_empty($value)
    {
        return !empty($value);
    }

    public static function check_alpha($value)
    {
        $regex = '/^[a-zA-Z]+$/';
        if (preg_match($regex, $value)) {
            return (1);
        } else {
            return (0);
        }
    }

    public static function check_int($value)
    {
        $regex = '/^\d+$/';
        if (preg_match($regex, $value)) {
            return (1);
        } else {
            return (0);
        }
    }

    public static function check_age($value)
    {
        if (((int) $value >= 12) && ((int) $value < 150)) {
            return (1);
        } else {
            return (0);
        }
    }

    public static function check_latitude($value)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) && $value <= 90 && $value >= -90) {
            return (1);
        } else {
            return (0);
        }
    }

    public static function check_longitude($value)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) && $value <= 180 && $value >= -180) {
            return (1);
        } else {
            return (0);
        }
    }

    public static function check_file($file)
    {
        $filename = $file['name'];
        $filetmpname = $file['tmp_name'];
        $filesize = $file['size'];
        $maxsize = 5000000;
        $minsize = 5000;
        $fileerror = $file['error'];
        $filetype = $file['type'];
        $ext = explode('.', $filename);
        $fileextension = strtolower(end($ext));
        $allowedextension = array('jpg', 'jpeg', 'png');

        if (in_array($fileextension, $allowedextension)) {
            if ($fileerror === 0) {
                if ($filesize <= $maxsize && $filesize >= $minsize) { //size allowed (5ko - 5mo)
                    if (@isset(getimagesize($filetmpname)[0]) 
                    && @imagecreatefromstring(file_get_contents($filetmpname))) {
                        return (1);
                    } else {
                        // file type error
                        return (2);
                    }
                } else {
                    // file size error
                    return (3);
                }
            } else {
                // file error
                return (4);
            }
        } else {
            // file extension error
            return (0);
        }
    }
}
