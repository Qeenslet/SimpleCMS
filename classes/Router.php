<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 10.01.2017
 * Time: 15:42
 */

require_once('Project.php');
require_once('Dashboard.php');
require_once('Section.php');
require_once('Guardian.php');
require_once ('Locator.php');

class Router
{
    private static $instance = null;

    private function __construct(){}
    private function __clone(){}

    public static function getRouter()
    {
        if (empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function checkRout()
    {
        try
        {
            if (!empty($_GET['section']))
            {
                if ($_GET['section'] == 'news' && !empty($_GET['article']))
                {
                    $project = new Project();
                    $project->displayNews(strval($_GET['article']));
                    exit;
                }
                else if ($_GET['section'] == 'dash')
                {
                    //echo '<pre>'; print_r($_SESSION); echo '</pre>';
                    $guard = new Guardian();
                    if ($guard->getMarker() != 200)
                    {
                        if (!empty($_POST))
                        {
                            $guard->checkAdmisson($_POST);
                        }
                        else
                        {
                            $guard->getLogin();
                        }
                        exit;
                    }
                    else
                    {
                        $admin = new Dashboard();
                        $admin->displayAdmin();
                        exit;
                    }

                }
                else if ($_GET['section'] == 'service' && !empty($_POST)) //служебный роут для получения видео с ютуба и фоток с инстаграмма
                {
                    //TODO - подключить классы Youtuber и Instagrammer, получить ои них данные и вернуть в json
                }
                else
                {
                    $section = new Section($_GET['section']);
                    $section->displaySection();
                    exit;

                }
            }
            $locator = new Locator();
            $locator->locate();
            $project = new Project();
            $project->mainPage();
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
            exit;
        }
    }
}