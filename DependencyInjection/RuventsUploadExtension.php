<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\DependencyInjection;

use Ruvents\UploadBundle\Controller\DownloadController;
use Ruvents\UploadBundle\EventListener\UploadListener;
use Ruvents\UploadBundle\Serializer\UploadNormalizer;
use Ruvents\UploadBundle\Validator\AssertUploadValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraint;

class RuventsUploadExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $config, ContainerBuilder $container)
    {
        $container->autowire(UploadListener::class)
            ->setPublic(false)
            ->setArguments([
                '$webDir' => $config['web_dir'],
                '$uploadsDir' => $config['uploads_dir'],
            ])
            ->addTag('doctrine.event_subscriber');

        if (class_exists(Serializer::class)) {
            $container->register(UploadNormalizer::class)
                ->setPublic(false)
                ->addTag('serializer.normalizer', ['priority' => -100]);
        }

        if (class_exists(Constraint::class)) {
            $container->register(AssertUploadValidator::class)
                ->setPublic(false)
                ->addTag('validator.constraint_validator');
        }

        $container->autowire(DownloadController::class)
            ->setPublic(true);
    }
}
