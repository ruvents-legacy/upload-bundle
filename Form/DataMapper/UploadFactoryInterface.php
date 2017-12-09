<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\Form\DataMapper;

use Ruvents\UploadBundle\Entity\AbstractUpload;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadFactoryInterface
{
    public function createUpload(UploadedFile $uploadedFile, string $path, \Traversable $forms): AbstractUpload;
}
