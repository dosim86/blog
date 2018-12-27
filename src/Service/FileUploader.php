<?php
/**
 * Created by PhpStorm.
 * User: dosim
 * Date: 09.06.18
 * Time: 21:49
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $uploadDir;

    private $imageHandler;

    public function __construct($uploadDir, ImageHandler $imageHandler)
    {
        $this->uploadDir = $uploadDir;
        $this->imageHandler = $imageHandler;
    }

    public function upload(UploadedFile $file, array $options = [])
    {
        if ($options && $options['image'] ?? $options['image'] ?: false) {
            $this->imageHandler->setFilePath($file->getRealPath());
            $this->imageHandler->handle($options);
        }

        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->uploadDir, $fileName);

        return $fileName;
    }
}