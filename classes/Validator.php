<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 17.01.2017
 * Time: 15:21
 */
class Validator
{

    public static function clearScripts($array)
    {
        $result = [];
        foreach ($array as $key => $one)
        {
            $tmp = str_replace('script', 'scrept', $one);
            $result[$key] = $tmp;
        }
        return $result;
    }


    public static function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            return true;
        }
        return false;
    }


    public static function checkNewPass($pass)
    {
        $combined = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z@#$%]{8,12}$/';
        if (preg_match($combined, $pass))
        {
            return true;
        }
        return false;
    }


    public static function checkOldPass($pass)
    {
        if ($pass == 'placeholder') return false;
        return true;
    }
}