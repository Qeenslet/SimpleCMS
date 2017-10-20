<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 11.01.2017
 * Time: 15:43
 */
require_once('Model.php');
class Dash_Model extends Model
{

    public function getSection($id)
    {
        $sql = 'SELECT id,
                       name,
                       mark,
                       html,
                       insides
                 FROM sections WHERE id = :id';
        return $this->fetchRow($sql, array('id' => $id));
    }


    /**
     * Инсерт новых данных
     *
     * @param $save
     * @param $table
     * @return int|string
     */
    public function insertData($save, $table)
    {

        try
        {
            $SQL = 'INSERT INTO `' . $table . '` ' . $this->prepareInsert($save);
            //echo '<pre>'; print_r($save); print_r($SQL); die;
            $this->db->prepare($SQL)->execute($save);
            return 0;
        }
        catch (Exception $e)
        {
            return $e->getMessage();
        }

    }


    /**
     * Обновление данных
     * @param $update
     * @param $table
     * @param $id
     * @return int|string
     */
    public function updateData($update, $table, $id)
    {
        try
        {
            $sqlPart = $this->prepareUpdate($update);
            $update['id'] = $id;
            $sql = 'UPDATE ' . $table . $sqlPart . ' WHERE id = :id';
            $this->db->prepare($sql)->execute($update);
            return 0;
        }
        catch (Exception $e)
        {
            return $e->getMessage();
        }

    }


    /**
     * Данные о пользователе
     * @param $id
     * @return mixed
     */
    public function getOneUser($id)
    {
        $sql = 'SELECT id,
                       u_name,
                       u_pass,
                       u_mail
                 FROM users WHERE id = :id';
        return $this->fetchRow($sql, array('id' => $id));
    }


    public function getAllLocations()
    {
        $sql = "SELECT * FROM locations";
        return $this->fetchAll($sql, null);
    }


    public function getLimitedLocations($start, $end)
    {
        $sql = "SELECT * FROM locations WHERE date >= :dt1 AND date <= :dt2 AND country IS NOT NULL";
        return $this->fetchAll($sql, ['dt1' => $start, 'dt2' => $end]);
    }
}