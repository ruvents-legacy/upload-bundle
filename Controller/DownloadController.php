<?php

namespace Ruvents\UploadBundle\Controller;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Ruvents\UploadBundle\Entity\AbstractUpload;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadController
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function __invoke(string $entity, string $path)
    {
        $manager = $this->registry->getManagerForClass($entity);

        if (null === $manager || !$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException(sprintf('Class %s is not a valid entity.', $entity));
        }

        /** @var null|AbstractUpload $upload */
        $upload = $manager->find($entity, $path);

        if (null === $upload) {
            throw new NotFoundHttpException();
        }

        $name = $upload->getClientName() ?: $upload->getFile()->getBasename();

        return (new BinaryFileResponse($upload->getFile()))
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);
    }
}
