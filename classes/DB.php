<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 20.12.2016
 * Time: 17:38
 */
require_once('Sypher.php');
class DB
{
    private $db;
    private static $instance;

    /**
     * DB constructor.
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Получение экземпляра класса
     *
     * @return DB
     */
    public static function establishConnction()
    {
        if (empty(self::$instance))
        {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function getDB()
    {
        return $this->db;
    }

    private function connect()
    {
        try { // Создаем или открываем созданную ранее базу данных
            $db = new PDO('sqlite:db/project.db'); // Создаем таблицы, если не найдены
            $st = $db->query('SELECT name FROM sqlite_master WHERE type = \'sections\'');
            $result = $st->fetchAll();
            $st2 = $db->query('SELECT name FROM sqlite_master WHERE type = \'news\'');
            $result2 = $st2->fetchAll();
            $st3 = $db->query('SELECT name FROM sqlite_master WHERE type = \'users\'');
            $result3 = $st3->fetchAll();

            $st4 = $db->query("SELECT name FROM sqlite_master WHERE type = 'locations'");
            $result4 = $st4->fetchAll();
            if (sizeof($result) == 0 || sizeof($result2) == 0 || sizeof($result3) == 0 || sizeof($st4) == 0)
            {
                $this->makeTable($db);
                $this->db = $db;
            }
            else
            {

                $this->db = $db;
            }

        }
        catch (PDOException $e)
        {
            die($e->getMessage());
        }


    }

    private function makeTable(PDO $db)
    {
        $db->exec('CREATE TABLE IF NOT EXISTS sections ( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                                                             name VARCHAR(255) NOT NULL,
                                                             mark VARCHAR(255) NOT NULL,
                                                             html TEXT NOT NULL,
                                                             insides TEXT NOT NULL);');
        $db->exec('CREATE TABLE IF NOT EXISTS news ( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                                                             mark VARCHAR(255) NOT NULL,
                                                             header VARCHAR(255) NOT NULL,
                                                             date_added DATE NOT NULL,
                                                             image VARCHAR(255) NULL,
                                                             html TEXT NOT NULL);');
        $db->exec('CREATE TABLE IF NOT EXISTS users ( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                                                             u_name VARCHAR(255) NOT NULL,
                                                             date_added DATE NOT NULL,
                                                             u_mail VARCHAR(255) NOT NULL,
                                                             u_pass VARCHAR(255) NOT NULL);');
        $db->exec('CREATE TABLE IF NOT EXISTS locations ( id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                                                             ip VARCHAR(255) NOT NULL,
                                                             hostname VARCHAR(255),
                                                             city VARCHAR(255),
                                                             region VARCHAR(255),
                                                             country VARCHAR(5),
                                                             loc VARCHAR(255),
                                                             date DATE NOT NULL,
                                                             org VARCHAR(255),
                                                             visit INTEGER);');
        $st = $db->query("SELECT id FROM users WHERE u_name = 'developer'");
        $isAdmin = $st->fetchAll();
        if (sizeof($isAdmin) == 0)
        {
            $credentials = Sypher::createFirstUser();
            $db->exec("INSERT INTO `users`(u_name, date_added, u_mail, u_pass) VALUES('{$credentials['u_name']}',
                                                                                      '{$credentials['date_added']}',
                                                                                      '{$credentials['u_mail']}',
                                                                                      '{$credentials['u_pass']}')");
        }

    }
}