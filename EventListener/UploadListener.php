<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Ruvents\UploadBundle\Entity\AbstractUpload;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;

class UploadListener implements EventSubscriber
{
    private $requestStack;

    private $requestContext;

    private $webDir;

    private $uploadsDir;

    private $setter;

    /**
     * @var AbstractUpload[]
     */
    private $persistedUploads = [];

    /**
     * @var string[]
     */
    private $filesToUnlink = [];

    public function __construct(RequestStack $requestStack, RequestContext $requestContext, string $webDir, string $uploadsDir)
    {
        $this->webDir = $webDir;
        $this->uploadsDir = $uploadsDir;
        $this->requestStack = $requestStack;
        $this->requestContext = $requestContext;
        $this->setter = \Closure::bind(function ($upload, $property, $value) {
            $upload->$property = $value;
        }, null, AbstractUpload::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preRemove,
            Events::postLoad,
            Events::postFlush,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof AbstractUpload) {
            return;
        }

        $file = $entity->getFile();

        $extension = $file->guessExtension();
        $name = bin2hex(random_bytes(20)).($extension ? '.'.$extension : '');
        $path = $this->uploadsDir.'/'.substr($name, 0, 2).'/'.substr($name, 2);

        $this->setValue($entity, 'path', $path);
        $this->setValue($entity, 'url', $this->generateUrl($path));

        if ($file instanceof UploadedFile) {
            $this->setValue($entity, 'clientName', $file->getClientOriginalName());
        }

        $this->persistedUploads[] = $entity;
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof AbstractUpload) {
            $this->filesToUnlink[] = $entity->getFile()->getPathname();
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof AbstractUpload) {
            $this->setValue($entity, 'file', $this->generateFile($entity->getPath()));
            $this->setValue($entity, 'url', $this->generateUrl($entity->getPath()));
        }
    }

    public function postFlush()
    {
        while ($upload = array_shift($this->persistedUploads)) {
            $file = $this->moveFile($upload->getFile(), $this->webDir.'/'.$upload->getPath());
            $this->setValue($upload, 'file', $file);
        }

        while ($file = array_shift($this->filesToUnlink)) {
            @unlink($file);
        }
    }

    private function moveFile(File $file, string $target): File
    {
        $directory = dirname($target);
        $name = basename($target);

        if ($file instanceof UploadedFile) {
            return $file->move($directory, $name);
        }

        $source = $file->getPathname();

        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new FileException(sprintf('Unable to create the "%s" directory.', $directory));
            }
        } elseif (!is_writable($directory)) {
            throw new FileException(sprintf('Unable to write in the "%s" directory.', $directory));
        }

        if (!@copy($source, $target)) {
            throw new FileException(sprintf('Could not copy the file "%s" to "%s" (%s).', $source, $target, strip_tags(error_get_last()['message'])));
        }

        return new File($target);
    }

    private function generateFile(string $path): File
    {
        return new File($this->webDir.'/'.$path, false);
    }

    private function generateUrl(string $path): string
    {
        if ($this->requestStack && $request = $this->requestStack->getMasterRequest()) {
            return $request->getUriForPath('/'.$path);
        }

        if (!$this->requestContext || !$host = $this->requestContext->getHost()) {
            return $path;
        }

        $scheme = $this->requestContext->getScheme();
        $port = '';

        if ('http' === $scheme && 80 != $this->requestContext->getHttpPort()) {
            $port = ':'.$this->requestContext->getHttpPort();
        } elseif ('https' === $scheme && 443 != $this->requestContext->getHttpsPort()) {
            $port = ':'.$this->requestContext->getHttpsPort();
        }

        $baseUrl = rtrim($this->requestContext->getBaseUrl(), '/').'/';

        return $scheme.'://'.$host.$port.$baseUrl.$path;
    }

    private function setValue(AbstractUpload $upload, string $property, $value): void
    {
        call_user_func($this->setter, $upload, $property, $value);
    }
}
