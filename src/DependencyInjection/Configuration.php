<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND = 'to_http_not_found';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('babdev_pagerfanta', 'array');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('default_view')->defaultValue('default')->end()
                ->arrayNode('exceptions_strategy')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('out_of_range_page')->defaultValue(self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND)->end()
                        ->scalarNode('not_valid_current_page')->defaultValue(self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
