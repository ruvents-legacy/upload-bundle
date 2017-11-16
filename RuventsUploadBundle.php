<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle;

use Ruvents\UploadBundle\DependencyInjection\Compiler\ReplaceTwigAssetExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RuventsUploadBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ReplaceTwigAssetExtensionPass());
    }
}
