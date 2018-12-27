<?php
/**
 * Created by PhpStorm.
 * User: dosim
 * Date: 09.06.18
 * Time: 21:49
 */

namespace App\Service;

class ImageHandler
{
    private $filePath;

    /** @var \Imagick */
    private $image;

    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle(array $options)
    {
        $this->image = new \Imagick($this->filePath);

        if ($cropCoords = $options['crop_coords'] ?? null) {
            $this->crop($cropCoords);
        }

        $this->compress();

        $this->image->writeImage($this->filePath);
    }

    private function crop($cropCoords)
    {
        $coords = explode('/', $cropCoords);

        $start = explode(':', $coords[0]);
        $size = explode(':', $coords[1]);

        $this->image->cropImage(
            $size[0],
            $size[1],
            $start[0],
            $start[1]
        );
    }

    private function compress($quality = 20)
    {
        $this->image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $this->image->setImageCompressionQuality($quality);
    }
}