<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 11.01.2017
 * Time: 15:39
 */
require_once('DB.php');
class Model
{
    protected $db;

    public function __construct()
    {
        $connection = DB::establishConnction();
        $this->db = $connection->getDB();
    }

    public function getAllSections()
    {
        $sql = 'SELECT id, name, mark FROM `sections`';
        return $this->fetchAll($sql, null);
    }


    public function getAllNews()
    {
        $sql = "SELECT id, mark, header, date_added, html, image FROM `news` ORDER BY date_added DESC";
        return $this->fetchAll($sql, null);
    }


    public function getAllUsers()
    {
        $sql = "SELECT id, u_name, u_mail, date_added FROM `users` ORDER BY date_added DESC";
        return $this->fetchAll($sql, null);
    }


    protected function fetchAll($sql, $params)
    {

        if (!empty($params))
        {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            $st = $this->db->query($sql);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }
    }


    protected function fetchRow($sql, $params)
    {
        if (!empty($params))
        {
            $stmt = $this->db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        else
        {
            $st = $this->db->query($sql);
            return $st->fetch(PDO::FETCH_ASSOC);
        }
    }


    protected function prepareInsert($array)
    {
        $part1 = [];
        $part2 = [];
        foreach ($array as $key => $value)
        {
            $part1[] = $key;
            $part2[] = ':' . $key;
        }
        $p1 = implode(', ', $part1);
        $p2 = implode(', ', $part2);
        return ('(' . $p1 . ') VALUES (' . $p2 . ')');
    }

    protected function prepareUpdate($array)
    {
        $string = ' SET ';
        $tmp = [];
        foreach ($array as $key => $value)
        {
            $tmp[] = $key . ' = :' . $key;
        }
        $string .= implode(', ', $tmp);
        return $string . ' ';
    }


    public function getOneNews($id)
    {
        $sql = 'SELECT id,
                       header,
                       mark,
                       html,
                       date_added,
                       image
                 FROM news WHERE id = :id';
        return $this->fetchRow($sql, array('id' => $id));
    }


    public function getOneNewsByMark($mark)
    {
        $sql = 'SELECT id,
                       header,
                       mark,
                       html,
                       date_added,
                       image
                 FROM news WHERE mark = :mark';
        return $this->fetchRow($sql, array('mark' => $mark));
    }


    public function getUserByName($name)
    {
        $sql = "SELECT u_pass, id FROM users WHERE u_name = :uname";
        return $this->fetchRow($sql, array('uname' => $name));
    }


    public function delete($table, $id)
    {
        try
        {
            $sql = "DELETE FROM `" . strval($table) . "` WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}