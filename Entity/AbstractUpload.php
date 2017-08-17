<?php

namespace Ruvents\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\MappedSuperclass()
 * @ORM\EntityListeners({"Ruvents\UploadBundle\EntityListener\UploadListener"})
 */
abstract class AbstractUpload
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

    /**
     * @param string $url
     *
     * @return static
     * @throws FileException
     */
    public static function fromUrl($url)
    {
        $target = rtrim(sys_get_temp_dir(), '/\\').DIRECTORY_SEPARATOR.sha1($url);

        if (!@copy($url, $target)) {
            throw new FileException(sprintf(
                'Could not copy the file "%s" to "%s" (%s).',
                $url, $target, strip_tags(error_get_last()['message'])
            ));
        }

        return new static($target);
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
     * @return string
     * @throws \RuntimeException
     */
    public function getAssetPath()
    {
        return $this->getId();
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}
