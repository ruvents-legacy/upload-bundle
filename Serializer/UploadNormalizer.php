<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\Serializer;

use Ruvents\UploadBundle\Entity\AbstractUpload;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UploadNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param AbstractUpload $upload
     */
    public function normalize($upload, $format = null, array $context = [])
    {
        return $upload->getUrl();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof AbstractUpload;
    }
}
