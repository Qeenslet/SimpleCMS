<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 18.01.2017
 * Time: 17:30
 *
 * получаем ссылки на видео с указанного канала на ютюбе через парс верстки.
 * Возвращает цифро-буквенный идентификатор видео, который может быть ссылкой или юзаться в айфрейме
 *
 */
class Youtuber
{

    private $url = 'https://www.youtube.com/channel/UCZD2-uY72ST1m_CjWw11WJQ';
    private $markerClass = 'yt-lockup-title ';
    private $replaceHref = '/watch?v=';
    private $videos;

    public function getAllVideos()
    {
        $this->upload();
        return $this->videos;
    }



    private function upload()
    {
        $content = file_get_contents($this->url);
        $this->read($content);
    }

    private function read($content)
    {
        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $h3 = $doc->getElementsByTagName('h3');
        foreach ($h3 as $one)
        {
            $class = $one->getAttribute('class');
            if ($class == $this->markerClass)
            {
                $child = $one->firstChild;
                $href = $child->getAttribute('href');
                $this->videos[] = str_replace($this->replaceHref, '', $href);
                //echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $href . '" frameborder="0" allowfullscreen></iframe><br>';
            }

        }
    }
}