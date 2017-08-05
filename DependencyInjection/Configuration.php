<?php

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
                    ->scalarNode('web_dir')
                        ->cannotBeEmpty()
                        ->defaultValue('%kernel.root_dir%/../web')
                        ->beforeNormalization()
                            ->always(function ($value) {
                                return rtrim($value, '/\\');
                            })
                        ->end()
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
                ->end()
            ->end();
    }
}
