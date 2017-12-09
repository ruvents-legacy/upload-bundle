<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ruvents\UploadBundle\Controller\DownloadController;
use Ruvents\UploadBundle\EventListener\UploadDoctrineListener;
use Ruvents\UploadBundle\Form\Type\UploadType;
use Ruvents\UploadBundle\Form\TypeGuesser\UploadTypeGuesser;
use Ruvents\UploadBundle\Serializer\UploadNormalizer;
use Ruvents\UploadBundle\UploadManager;
use Ruvents\UploadBundle\Validator\UploadFileValidator;

return function (ContainerConfigurator $container) {
    $services = $container->services()
        ->defaults()
        ->private();

    $services->set(UploadManager::class)
        ->args([
            '$requestStack' => ref('request_stack')
                ->nullOnInvalid(),
            '$requestContext' => ref('router.request_context')
                ->nullOnInvalid(),
        ]);

    $services->set(UploadDoctrineListener::class)
        ->args([
            '$manager' => ref(UploadManager::class),
        ])
        ->tag('doctrine.event_subscriber');

    $services->set(UploadType::class)
        ->args([
            '$manager' => ref(UploadManager::class),
        ])
        ->tag('form.type');

    $services->set(UploadTypeGuesser::class)
        ->args([
            '$doctrine' => ref('doctrine'),
        ])
        ->tag('form.type_guesser');

    $services->set(UploadFileValidator::class)
        ->tag('validator.constraint_validator');

    $services->set(UploadNormalizer::class)
        ->args([
            '$manager' => ref(UploadManager::class),
        ])
        ->tag('serializer.normalizer');

    $services->set(DownloadController::class)
        ->public()
        ->args([
            '$manager' => ref(UploadManager::class),
            '$doctrine' => ref('doctrine'),
        ]);
};
