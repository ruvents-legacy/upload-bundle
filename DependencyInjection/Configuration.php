<?php
declare(strict_types=1);

namespace Ruvents\UploadBundle\DependencyInjection;

use Ruvents\UploadBundle\Entity\AbstractUpload;
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
                    ->scalarNode('entity')
                        ->isRequired()
                        ->cannotBeEmpty()
                        ->validate()
                            ->always(function ($class) {
                                if (!class_exists($class)) {
                                    throw new \InvalidArgumentException(sprintf('Class %s does not exist.', $class));
                                }

                                if (!is_subclass_of($class, AbstractUpload::class)) {
                                    throw new \InvalidArgumentException(sprintf('Class %s must be a subclass of %s.', $class, AbstractUpload::class));
                                }

                                return $class;
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
