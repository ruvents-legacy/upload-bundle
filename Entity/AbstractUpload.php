<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\MappedSuperclass()
 */
abstract class AbstractUpload
{
    /**
     * @ORM\Column(name="id", type="string")
     * @ORM\Id()
     */
    protected $path = '';

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var null|string
     */
    protected $clientName;

    private $file;

    private $url = '';

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

        throw new \InvalidArgumentException(sprintf('File must be a string, an instance of "%s" or an object with __toString method.', File::class));
    }

    /**
     * @param string $url
     *
     * @return static
     *
     * @throws IOException
     */
    public static function fromUrl($url)
    {
        $filesystem = new Filesystem();

        $target = $filesystem->tempnam('ruvents_upload', sha1($url));
        $filesystem->copy($url, $target);

        return new static($target);
    }

    public function __toString(): string
    {
        return $this->clientName ?: $this->file->getBasename();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
