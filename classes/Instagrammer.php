<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 18.01.2017
 * Time: 17:41
 *
 * получает данные от АПИ инстаграмма, но ключ и установочные данные были получены от заказчика
 */
class Instagrammer
{

    private $token = '3452909732.1677ed0.92c3d03c49644e509d579ca5013a2756';
    private $user_id = 'self';
    private $instagram_cnct;
    private $media;
    private $limit = 20;
    private $result;

    public function getImages()
    {
        $this->connect();
        $this->upload();
        $this->parse();
        return $this->result;
    }

    private function connect()
    {
        $this->instagram_cnct = curl_init();
        curl_setopt( $this->instagram_cnct, CURLOPT_URL, "https://api.instagram.com/v1/users/" . $this->user_id . "/media/recent?access_token=" . $this->token );
        curl_setopt( $this->instagram_cnct, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $this->instagram_cnct, CURLOPT_TIMEOUT, 15 );
    }

    private function upload()
    {
        $this->media = json_decode( curl_exec( $this->instagram_cnct ) );
        curl_close( $this->instagram_cnct );
    }


    private function parse()
    {

        foreach(array_slice($this->media->data, 0, $this->limit) as $data)
        {
            $bodytag = str_replace("s150x150", "s320x320", $data->images->thumbnail->url);
            $this->result[] = $bodytag;
        }
    }
}