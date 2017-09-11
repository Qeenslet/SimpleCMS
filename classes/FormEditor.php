<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 16.01.2017
 * Time: 17:49
 */
class FormEditor
{
    private $data;
    private $html;

    public function __construct(Array $data, $formTitle = null)
    {
        $this->data = $data;
        $this->pageName($formTitle);
        $this->tableStart();
    }


    public function addField($name, $key, $type)
    {
        $this->newField($name, $key, $type);
    }

    private function pageName($formTitle)
    {
        if (!empty($formTitle))
        {
            $this->html = '<h3>' . $formTitle . '</h3>';
        }
        else
        {
            $this->html = '';
        }
    }


    private function tableStart()
    {
        $this->html .= '<form method="post">';
    }



    private function newField($name, $key, $type)
    {
        $value = !empty($this->data[$key]) ? $this->data[$key] : '';
        $this->html .= '<div class="form-group">';
        $this->html .= '<label for="edit_' . $key . '_form">' . $name . '</lable>';
        if ($type == 'text')
        {
            $this->html .= '<input type="text" class="form-control" id="edit_' . $key . '_form" name="' . $key . '" value="' . $value . '">';
        }
        else if ($type == 'textarea')
        {
            $this->html .= '<textarea class="form-control" id="edit_' . $key . '_form" rows="10" cols="150" name="' . $key . '">' . $value . '</textarea>';
        }
        else if ($type == 'password')
        {
            $value = 'placeholder';
            $this->html .= '<input type="password" class="form-control" id="edit_' . $key . '_form" name="' . $key . '" value="' . $value . '">';
        }
        else if ($type == 'email')
        {
            $this->html .= '<input type="email" class="form-control" id="edit_' . $key . '_form" name="' . $key . '" value="' . $value . '">';
        }
        $this->html .= '</div>';
    }

    public function setParams($table, $id)
    {
        $this->html .= '<input type="hidden" name="table" value="' . $table . '">';
        $this->html .= '<input type="hidden" name="id" value="' . $id . '">';
    }



    public function getForm()
    {
        $this->finalize();
        return $this->html;
    }


    private function finalize()
    {
        $this->html .= '<button type="submit" class="btn btn-primary">Сохранить</button>&nbsp; ';
        $this->html .= '<input type="button" class="btn btn-default" onclick="window.history.back()" value="Назад">';
        $this->html .= '</form>';
    }
}