<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\Serializer;

use Ruvents\UploadBundle\Entity\AbstractUpload;
use Ruvents\UploadBundle\UploadManager;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UploadNormalizer implements NormalizerInterface
{
    private $manager;

    public function __construct(UploadManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     *
     * @param AbstractUpload $upload
     */
    public function normalize($upload, $format = null, array $context = [])
    {
        return $this->manager->getUrl($upload);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AbstractUpload;
    }
}
