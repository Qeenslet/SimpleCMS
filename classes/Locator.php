<?php
/**
 * Created by PhpStorm.
 * User: gulidoveg
 * Date: 11.09.17
 * Time: 17:13
 */

class Locator extends Dash_Model
{

    private $ip;
    private $record;
    private $dt;

    private $ch;
    private $result;

    /**
     * ip address logging
     */
    public function locate()
    {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->dt = new DateTime();
        if ($this->checkIP())
        {
            $this->updateIP();
        }
        else
        {
            $this->makeRequest();
            $this->saveLocation();
        }
    }


    /**
     * Check IP in database
     * @return bool
     */
    private function checkIP()
    {
        $sql = 'SELECT id, visit
                 FROM locations WHERE ip = :ip
                 AND date >= :dt';
        try
        {
            if ($this->record = $this->fetchRow($sql, array('ip' => $this->ip, 'dt' => $this->getFirstMonthDate())))
            {
                return true;
            }
            return false;
        }
        catch (Exception $e)
        {
            return false;
        }

    }


    /**
     * Updating existing IP
     */
    private function updateIP()
    {
        $this->record['visit'] += 1;
        $d = $this->getCurrentTimeString();
        $this->updateData(array('visit' => $this->record['visit'],
                                 'date' => $d), 'locations', $this->record['id']);
    }


    /**
     * Requesting IP information
     *
     */
    private function makeRequest()
    {
        $this->ch = curl_init();

        curl_setopt($this->ch, CURLOPT_URL, "https://ipinfo.io/{$this->ip}/json");
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);


        $output = curl_exec($this->ch);
        $this->result = json_decode($output);

    }


    /**
     * Saving IP results
     */
    private function saveLocation()
    {
        $save = [];
        if (!empty($this->result->hostname)) $save['hostname'] = $this->result->hostname;
        if (!empty($this->result->loc)) $save['loc'] = $this->result->loc;
        if (!empty($this->result->city)) $save['city'] = $this->result->city;
        if (!empty($this->result->region)) $save['region'] = $this->result->region;
        if (!empty($this->result->country)) $save['country'] = $this->result->country;
        $save['ip'] = $this->ip;
        $save['date'] = $this->getCurrentTimeString();
        $save['visit'] = 1;
        $this->insertData($save, 'locations');
    }


    /**
     * Making timestring to save/update DATABASE
     * @return string
     */
    private function getCurrentTimeString()
    {
        return $this->dt->format('Y-m-d');
    }


    private function getFirstMonthDate()
    {
        return $this->dt->format('Y-m-01');
    }

}