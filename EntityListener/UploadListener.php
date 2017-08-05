<?php

namespace Ruvents\UploadBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ruvents\UploadBundle\Entity\Upload;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadListener
{
    /**
     * @var string
     */
    private $webDir;

    /**
     * @var string
     */
    private $uploadsDir;

    /**
     * @param string $webDir
     * @param string $uploadsDir
     */
    public function __construct($webDir, $uploadsDir)
    {
        $this->webDir = $webDir;
        $this->uploadsDir = $uploadsDir;
    }

    public function prePersist(Upload $upload, LifecycleEventArgs $event)
    {
        $metadata = $event->getEntityManager()->getClassMetadata(Upload::class);

        $file = $upload->getFile();
        $name = bin2hex(random_bytes(20)).'.'.$file->guessExtension();

        if ($file instanceof UploadedFile) {
            $file = $file->move($this->webDir.'/'.$this->uploadsDir, $name);
        } else {
            $source = $upload->getFile()->getPathname();
            $target = $this->webDir.'/'.$this->uploadsDir.'/'.$name;

            if (!@copy($source, $target)) {
                throw new FileException(sprintf(
                    'Could not copy the file "%s" to "%s" (%s)',
                    $source, $target, strip_tags(error_get_last()['message'])
                ));
            }

            $file = new File($target);
        }

        $this->setFile($upload, $file, $metadata->getReflectionClass());
        $metadata->setFieldValue($upload, 'id', $this->uploadsDir.'/'.$name);
    }

    public function postLoad(Upload $upload, LifecycleEventArgs $event)
    {
        $reflection = $event->getEntityManager()
            ->getClassMetadata(Upload::class)
            ->getReflectionClass();

        $file = new File($this->webDir.'/'.$upload->getId(), false);

        $this->setFile($upload, $file, $reflection);
    }

    public function preRemove(Upload $upload)
    {
        @unlink($upload->getFile()->getPathname());
    }

    private function setFile(Upload $upload, File $file, \ReflectionClass $reflection = null)
    {
        $property = $reflection->getProperty('file');
        $property->setAccessible(true);
        $property->setValue($upload, $file);
    }
}
