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

    public function __construct($file, $id)
    {

        $mainFile = explode('.', $file);
        $this->id = $mainFile[0] . $id;
        $this->file = 'uploads/' . $file;
    }

    public function saveImage()
    {
        $this->getResource();
        imagejpeg($this->resource, 'upload/image_'.$this->id.".jpg");
        $this->resource = null;
    }


    public function saveImageResize($x, $y)
    {
        $this->getResource();
        $this->cropResize($x, $y);
        imagejpeg($this->resource, 'uploads/image_'.$this->id.".jpg");
        $this->resource = null;
    }

    public function saveBig()
    {
        $this->getResource();
        $this->cropResize(1150, 450);
        imagejpeg($this->resource, 'uploads/image_'.$this->id.".jpg");
        $this->resource = null;
    }

    public function saveCropped()
    {
        $this->getResource();
        $this->resizeCrop(230, 140);
        imagejpeg ($this->resource, 'uploads/image_'.$this->id.".jpg");
        $this->resource = null;
    }

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

    protected function cropResize($w, $h)
    {
        $this->defineDimensions();
        $proportion = $w / $this->x;
        $this->resize($w, $this->y * $proportion, true);
        $this->crop($w, $h);
        $this->resize($w , $h, true);
    }


    public function getDimension()
    {
        $this->getResource();
        $this->defineDimensions();
        return $this->x;
    }

    protected function defineDimensions()
    {
        $this->x = imagesx($this->resource);
        $this->y = imagesy($this->resource);
    }

    protected function getResource()
    {
        $this->resource = imagecreatefromstring(file_get_contents($this->file));
    }


    public function getProportions()
    {
        $this->getResource();
        $this->defineDimensions();
        if (!empty($this->x))
        {
            return $this->y / $this->x;
        }
    }
}