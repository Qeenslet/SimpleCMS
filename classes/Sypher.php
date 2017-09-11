<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 27.01.2017
 * Time: 16:30
 */
class Sypher
{

    public static function encode($string)
    {
        $tmp = 'V@ghtl7&56fhZ3e';
        $salt = md5($tmp);
        return crypt($string, $salt);
    }


    public static function verify($saved_pass, $input_pass)
    {
        $entered = self::encode($input_pass);
        return $entered === $saved_pass;
    }



    public static function createFirstUser()
    {
        $path = '1234';
        $name = 'developer';
        $email = 'egorgulidow@mail.ru';
        $dt = new DateTime();
        $date = $dt->format('Y-m-d');
        $result = [];
        $result['u_name'] = $name;
        $result['date_added'] = $date;
        $result['u_mail'] = $email;
        $result['u_pass'] = self::encode($path);
        return $result;
    }
}