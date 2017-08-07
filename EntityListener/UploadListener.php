<?php

namespace Ruvents\UploadBundle\EntityListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Ruvents\UploadBundle\Entity\AbstractUpload;
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

    public function prePersist(AbstractUpload $upload, LifecycleEventArgs $event)
    {
        $metadata = $event->getEntityManager()->getClassMetadata(AbstractUpload::class);

        $file = $upload->getFile();
        $extension = $file->guessExtension();
        $name = bin2hex(random_bytes(20)).(empty($extension) ? '' : '.'.$extension);
        $directory = $this->webDir.'/'.$this->uploadsDir.'/'.substr($name, 0, 2);
        $name = substr($name, 2);

        if ($file instanceof UploadedFile) {
            $file = $file->move($directory, $name);
        } else {
            $source = $upload->getFile()->getPathname();
            $target = $directory.'/'.$name;

            if (!is_dir($directory)) {
                if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                    throw new FileException(sprintf('Unable to create the "%s" directory.', $directory));
                }
            } elseif (!is_writable($directory)) {
                throw new FileException(sprintf('Unable to write in the "%s" directory.', $directory));
            }

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

    public function postLoad(AbstractUpload $upload, LifecycleEventArgs $event)
    {
        $reflection = $event->getEntityManager()
            ->getClassMetadata(AbstractUpload::class)
            ->getReflectionClass();

        $file = new File($this->webDir.'/'.$upload->getId(), false);

        $this->setFile($upload, $file, $reflection);
    }

    public function preRemove(AbstractUpload $upload)
    {
        @unlink($upload->getFile()->getPathname());
    }

    private function setFile(AbstractUpload $upload, File $file, \ReflectionClass $reflection)
    {
        $property = $reflection->getProperty('file');
        $property->setAccessible(true);
        $property->setValue($upload, $file);
    }
}
