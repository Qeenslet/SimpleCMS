<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 30.01.2017
 * Time: 14:18
 */
require_once('Sypher.php');
require_once('Sypher.php');
class Guardian extends Project
{
    private $marker;
    private $standartAttrs;

    public function __construct()
    {
        parent::__construct();
        $this->standartAttrs['{PATH_TO_ADMIN}'] = DOC_PATH . 'dash/';
        if ($this->checkSession()) $this->marker = 200;
        else $this->marker = 403;
    }


    private function checkSession()
    {
        if (!empty($_SESSION['_smart_control']))
        {
            if ($_SESSION['_smart_control'] == true)
            {
                $_SESSION['_smart_control'] = true;
                return true;
            }
        }
        return false;
    }


    /**
    * Возвращает маркер проверки
    * @return int
    */
    public function getMarker()
    {
        return $this->marker;
    }


    public function getLogin($message = '')
    {
        $_SESSION['_entries'] = 1;
        $params['{MESSAGE}'] = $message;
        $params = array_merge($params, $this->standartAttrs);
        echo Pagemaker::render('dash/'. 'password.html', $params);
    }


    public function checkAdmisson($post)
    {
        if (!empty($_SESSION['_entries']))
        {
            $count = $_SESSION['_entries'];
        }
        else
        {
            $this->getLogin('Ай, молодец! Давай еще разок!');
            exit;
        }
        $count++;
        if ($count > $this->getSetting('login_attempts'))
        {
            $this->getLogin('Странно.... по-моему, чего-то явно не хватает');
            exit;
        }
        unset($_SESSION['_entries']);
        $_SESSION['_entries'] = $count;

        if ($this->checkCredencials($post))
        {
            $_SESSION['_smart_control'] = true;
            $_SESSION['_uname'] = $post['uname'];
            header('location: index.php?section=dash');
        }
        else
        {
            $this->getLogin('Хм.... Че-т не сходится...');
        }
    }


    private function checkCredencials($post)
    {
        if (!empty($post['uname']))
        {
            $data = $this->model->getUserByName($post['uname']);
            if (empty($data)) return false;

            return Sypher::verify($data['u_pass'], $post['password']);
        }
        return false;
    }

}