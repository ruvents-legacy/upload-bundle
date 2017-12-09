<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\DependencyInjection\Compiler;

use Ruvents\UploadBundle\Twig\Extension\AssetExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ReplaceTwigAssetExtensionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->has($name = 'twig.extension.assets')) {
            $container
                ->findDefinition($name)
                ->setClass(AssetExtension::class);
        }
    }
}
