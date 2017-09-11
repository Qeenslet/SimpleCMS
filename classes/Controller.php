<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 20.12.2016
 * Time: 17:35
 */

class Controller
{
    private $request;

    public function __construct()
    {
        $sets = parse_ini_file('settings.ini');
        $this->folder = !empty($sets['subfolder']) ? $sets['subfolder'] : null;
        $this->request = substr($_SERVER['REQUEST_URI'], 1);
    }

    public function controlURL()
    {
        $result = explode('/', $this->request);
        if (!empty($result[0]))
        {
            if ($result[0] == $this->folder) unset($result[0]);
        }
        if (count($result) > 0 && count($result) < 3)
        {
            $params = array();
            foreach($result as $one)
            {
                $params[] = $one;
            }
            if (!empty($params[0]))
            {
                $_GET['section'] = $params[0];
            }
            if (!empty($params[1]))
            {
                $_GET['article'] = $params[1];
            }
        }
    }
}