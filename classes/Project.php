<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 17.01.2017
 * Time: 16:43
 */
require_once('Model.php');
require_once('Pagemaker.php');

class Project
{

    protected $model;
    private $settings;

    public function __construct()
    {
        $this->model = new Model();
        $this->parseSettings();
    }

    protected function getSiteMenu()
    {
        $data = $this->model->getAllSections();
        $params = array();
        $subfolder = $this->getSetting('subfolder');
        if (!empty($subfolder)) $subfolder = '/' . $subfolder;
        else  $subfolder = '';
        $mainPage = [];
        $mainPage['{SECTION_HREF}'] =  $this->getSetting('url_path') ? $this->getSetting('url_path') : '/';
        $mainPage['{SECTION}'] = 'Главная';
        $params['hrefs'][] = $mainPage;
        foreach($data as $one)
        {
            $tmp = array();
            $tmp['{SECTION}'] = $one['name'];
            $tmp['{SECTION_HREF}'] = $subfolder . '/' . $one['mark'];
            $params['hrefs'][] = $tmp;
        }

        return Pagemaker::render($this->getThemePass() . $this->getSetting('menu_template'), $params);
    }


    protected function getThemePass()
    {
        return $this->getSetting('theme_path');
    }


    public function mainPage()
    {
        $params['{SITE_TITLE}'] = $this->getSetting('sitename');
        $params['{SITEMENU}'] = $this->getSiteMenu();
        $params['{NEWSFEED}'] = $this->getNews();
        //echo '<pre>'; print_r($this->getNews()); die;
        echo Pagemaker::render($this->getThemePass(). $this->getSetting('main_template'), $params);
    }


    protected function getNews()
    {
        $news = $this->model->getAllNews();
        $params = array();
        $n = 1;
        $subfolder = $this->getSetting('subfolder');
        if (!empty($subfolder)) $subfolder = '/' . $subfolder;
        foreach($news as $one)
        {
            if ($n > 5) break;
            $tmp = [];
            $tmp['{NUM}'] = $n;
            $tmp['{IMG}'] = $one['image'];
            $tmp['{TITLE}'] = $one['header'];
            $tmp['{CONTENT}'] = $this->shortenVersion($one['html']);
            $tmp['{ID}'] = $one['id'];
            $tmp['{HREF}'] = $subfolder . '/news/' . $one['mark'];
            $params['news'][] = $tmp;
            $n++;
        }
        //echo '<pre>'; print_r($params); die;
        return Pagemaker::render($this->getThemePass() . $this->getSetting('news_template'), $params);
    }



    protected function shortenVersion($string)
    {
        $string = strip_tags($string);
        return mb_substr($string, 0, 200, 'UTF-8');
    }


    protected function parseSettings()
    {
        $this->settings = parse_ini_file('settings.ini');
    }

    protected function getSetting($name)
    {
        if (!empty($this->settings[$name])) return $this->settings[$name];
        return null;
    }



    public function displayNews($mark)
    {
        $template = $this->getThemePass() . 'section_template.html';
        $data = $this->model->getOneNewsByMark($mark);
        if (!empty($data))
        {
            $menu = $this->getSiteMenu();
            $pageName = $data['header'];
            $content = $this->prepareNews($data);
            echo Pagemaker::render($template, ['{CONTENT}' => $content, '{MENU}' => $menu, '{SECTION_NAME}' => $pageName, '{SITENAME}' => $this->getSetting('sitename'), '{HTML_TYPE}' => 'fonnews']);
        }
    }

    private function prepareNews(Array $data)
    {
        $template = $this->getThemePass() . 'news_template.html';
        $content['{IMG}'] = !empty($data['image']) ? '<img alt="image" src="' . $data['image'] . '" style="max-width: 600px">' : '';
        $content['{HTML}'] = $data['html'];
        return Pagemaker::render($template, $content);
    }

}