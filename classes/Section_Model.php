<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 16.01.2017
 * Time: 17:12
 */
require_once('Model.php');

class Section_Model extends Model
{

    public function getSection($key)
    {
        $sql = 'SELECT id,
                       name,
                       mark,
                       html,
                       insides
                 FROM sections WHERE mark = ?';
        return $this->fetchRow($sql, array($key));
    }
}