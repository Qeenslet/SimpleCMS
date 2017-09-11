<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 16.01.2017
 * Time: 17:10
 */
require_once('Project.php');
require_once('Section_Model.php');

class Section extends Project
{

    private $standart;
    private $key;
    private $data;
    protected $model;


    public function __construct($key)
    {
        parent::__construct();
        $this->key = strtolower($key);
        $this->model = new Section_Model();
        $this->data = $this->model->getSection($this->key);
    }

    public function getData()
    {
        return $this->data;
    }


    public function displaySection()
    {
        $template = $this->getThemePass() . 'section_template.html';
        $content = $this->data['insides'];
        $pageName =  $this->data['name'];
        $menu = $this->getSiteMenu();
        echo Pagemaker::render($template, ['{CONTENT}' => $content, '{MENU}' => $menu, '{SECTION_NAME}' => $pageName, '{SITENAME}' => $this->getSetting('sitename'), '{HTML_TYPE}' => $this->data['html']]);
    }
    //http://betasite.esy.es/wp-content/uploads/2016/06/1-744x1024.jpg

}