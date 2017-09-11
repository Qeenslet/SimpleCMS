<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 11.01.2017
 * Time: 12:28
 */
require_once('Templater2.php');

class Pagemaker
{

    public static function render($path, Array $args)
    {
        $tpl = new Templater2($path);
        foreach ($args as $key => $val)
        {
            if (is_array($val))
            {
                self::reassiner($tpl, $key, $val);
            }
            else
                $tpl->assign($key, $val);
        }
        return $tpl->parse();
    }


    private static function reassiner(Templater2 $tpl, $key, Array $args)
    {
        $total = count($args);
        $n = 1;
        foreach ($args as $collection)
        {
            foreach ($collection as $holder => $val)
            {
                $tpl->$key->assign($holder, $val);
            }
            if ($n != $total)
            {
                $tpl->$key->reassign();
            }
            $n++;
        }
    }
}