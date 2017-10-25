<?php

/**
 * Created by PhpStorm.
 * User: GulidovEG
 * Date: 19.01.2017
 * Time: 11:47
 */
class Imaginator
{
    protected $image;
    protected $resource;
    protected $x;
    protected $y;
    protected $file;
    protected $id;
    private $rotationCorrections = [90 => 270,
                                    180 => 180,
                                    270 => 90,
                                    0 => 0];

    public function __construct($file, $id)
    {

        $mainFile = explode('.', $file);
        $this->id = $mainFile[0] . $id;
        $this->file = 'uploads/' . $file;
        $this->getResource();
    }

    /**
     * Просто сохранить если картинка по ссылке например
     */
    public function saveImage()
    {
        imagejpeg($this->resource, 'upload/image_'.$this->id.".jpg");
        $this->resource = null;
    }


    /**
     * Сохранить по заданным размерам
     * @param $x
     * @param $y
     */
    public function saveImageResize($x, $y)
    {
        $this->cropResize($x, $y);
        imagejpeg($this->resource, 'uploads/'.$this->id.".jpg");
        $this->resource = null;
    }

    /**
     * Крупная копия
     */
    public function saveBig()
    {
        $this->cropResize(1150, 450);
        imagejpeg($this->resource, 'uploads/image_'.$this->id.".jpg");
        $this->resource = null;
    }

    /**
     * Уменьшенная копия
     */
    public function saveCropped()
    {
        $this->defineDimensions();
        if ($this->x > $this->y)
        {
            $this->resizeCrop(330, 200);
        }
        else
        {
            $this->resizeCrop(300, 492);
        }

        imagejpeg ($this->resource, 'uploads/thumbs/'.$this->id.".jpg");
        $this->resource = null;
    }

    /**
     * Ресайзить
     * @param $w
     * @param $h
     * @param bool $force
     */
    protected function resize($w, $h, $force=false)
    {
        if (!$force)
        {
            $this->defineDimensions();
            $proportion = $w / $this->x;
            if ($h != $this->y * $proportion)
            {
                $h = $this->y * $proportion;
            }
        }
        $this->resource = imagescale($this->resource, $w, $h);
    }

    /**
     * Обрезать
     * @param $w
     * @param $h
     */
    protected function crop($w, $h)
    {
        $this->defineDimensions();
        if ($w <= $this->x && $h <= $this->y)
        {
            $newImage = imagecreatetruecolor($w, $h);
            //search for center point
            if ($this->x > $this->y)
            {
                $centerX = $this->x / 2;
                $centerY = $this->y / 2;
            }
            else
            {
                $centerX = $this->x / 2;
                $centerY = $this->y / 3;
            }

            $xNew = $centerX - $w / 2;
            $yNew = $centerY - $h / 2;

            imagecopy ($newImage, $this->resource, 0, 0, $xNew, $yNew, $w, $h);
            $this->resource = $newImage;

        }
    }

    /**
     * Ресайзить, затем обрезать
     * @param $w
     * @param $h
     */
    protected function resizeCrop($w, $h)
    {
        $this->defineDimensions();
        if ($this->x > $this->y)
        {
            $proportion = $h / $this->y;

            $this->resize($this->x * $proportion , $h);
        }
        else
        {
            $this->resize($w , $h);
        }
        $this->crop($w, $h);
    }

    /**
     * Обрезать и ресазить
     * @param $w
     * @param $h
     */
    protected function cropResize($w, $h)
    {
        $this->defineDimensions();
        $proportion = $w / $this->x;
        $this->resize($w, $this->y * $proportion, true);
        $this->crop($w, $h);
        $this->resize($w , $h, true);
    }


    /**
     * Сторона x
     * @return mixed
     */
    public function getDimension()
    {
        $this->defineDimensions();
        return $this->x;
    }

    /**
     * Определение пропорций
     */
    protected function defineDimensions()
    {
        $this->x = imagesx($this->resource);
        $this->y = imagesy($this->resource);
    }

    /**
     * Получение ресурса
     */
    protected function getResource()
    {
        $this->resource = imagecreatefromstring(file_get_contents($this->file));
    }


    /**
     * пропорции
     * @return float|int
     */
    public function getProportions()
    {
        $this->defineDimensions();
        if (!empty($this->x))
        {
            return $this->y / $this->x;
        }
        return null;
    }

    /**
     * поворот
     * @param $angle
     */
    public function rotate($angle)
    {
        if ($this->rotationCorrections[$angle])
        {
            $angle = $this->rotationCorrections[$angle];
        }
        $this->resource = imagerotate($this->resource, $angle, 0);
        imagejpeg($this->resource, 'uploads/'.$this->id.".jpg");
    }

}