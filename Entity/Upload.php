<?php

namespace Ruvents\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Table("upload")
 * @ORM\Entity(readOnly=true)
 * @ORM\EntityListeners({"Ruvents\UploadBundle\EntityListener\UploadListener"})
 */
class Upload
{
    /**
     * @ORM\Column(type="string", name="id")
     * @ORM\Id()
     *
     * @var string
     */
    private $id;

    /**
     * @var File
     */
    private $file;

    /**
     * @param string|object|File $file
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($file)
    {
        if ($file instanceof File) {
            $this->file = $file;

            return;
        }

        if (is_string($file) || is_object($file) && method_exists($file, '__toString')) {
            $this->file = new File((string)$file, false);

            return;
        }

        throw new \InvalidArgumentException('Argument $file must be a string.');
    }

    public function __toString()
    {
        return (string)$this->file;
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function getId()
    {
        if (null === $this->id) {
            throw new \RuntimeException('You should not access the id property on a non-managed entity.');
        }

        return $this->id;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}
