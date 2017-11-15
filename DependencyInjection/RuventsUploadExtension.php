<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\DependencyInjection;

use Ruvents\UploadBundle\Controller\DownloadController;
use Ruvents\UploadBundle\EventListener\UploadListener;
use Ruvents\UploadBundle\Form\Type\UploadType;
use Ruvents\UploadBundle\Serializer\UploadNormalizer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\Serializer\Serializer;

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

        if (class_exists(Form::class)) {
            $container->register(UploadType::class)
                ->setPublic(false)
                ->setArguments([
                    $config['entity'],
                ])
                ->addTag('form.type');
        }

        $container->autowire(DownloadController::class)
            ->setPublic(true)
            ->setArguments([
                '$entity' => $config['entity'],
            ]);
    }
}
