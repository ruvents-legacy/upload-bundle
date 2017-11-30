<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        return (new TreeBuilder())
            ->root('ruvents_upload')
                ->children()
                    ->scalarNode('default_class')
                        ->defaultNull()
                    ->end()
                    ->scalarNode('default_type')
                        ->cannotBeEmpty()
                        ->defaultNull()
                    ->end()
                    ->scalarNode('uploads_dir')
                        ->cannotBeEmpty()
                        ->defaultValue('uploads')
                        ->beforeNormalization()
                            ->always(function ($value) {
                                return trim($value, '/\\');
                            })
                        ->end()
                    ->end()
                    ->scalarNode('web_dir')
                        ->cannotBeEmpty()
                        ->defaultValue('%kernel.project_dir%/public')
                        ->beforeNormalization()
                            ->always(function ($value) {
                                return rtrim($value, '/\\');
                            })
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
