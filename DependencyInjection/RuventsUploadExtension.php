<?php

namespace Ruvents\UploadBundle\DependencyInjection;

use Ruvents\UploadBundle\EntityListener\UploadListener;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RuventsUploadExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $config, ContainerBuilder $container)
    {
        $container->register(UploadListener::class)
            ->setArguments([
                '$webDir' => $config['web_dir'],
                '$uploadsDir' => $config['uploads_dir'],
            ])
            ->addTag('doctrine.orm.entity_listener');
    }
}
