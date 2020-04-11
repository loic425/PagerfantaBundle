<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection;

use BabDev\PagerfantaBundle\View\TwigView;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const EXCEPTION_STRATEGY_CUSTOM = 'custom';
    public const EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND = 'to_http_not_found';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('babdev_pagerfanta', 'array');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('babdev_pagerfanta', 'array');
        }

        $rootNode
            ->children()
                ->scalarNode('default_view')->defaultValue('default')->end()
                ->scalarNode('default_twig_template')->defaultValue(TwigView::DEFAULT_TEMPLATE)->end()
                ->arrayNode('exceptions_strategy')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('out_of_range_page')
                            ->defaultValue(self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND)
                            ->validate()
                                ->ifTrue(static function ($v) { return !\in_array($v, [self::EXCEPTION_STRATEGY_CUSTOM, self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND]); })
                                ->then(static function ($v) {
                                    @trigger_error(
                                        sprintf(
                                            'Setting the "babdev_pagerfanta.exceptions_strategy.out_of_range_page" configuration option to "%s" is deprecated since BabDevPagerfantaBundle 2.2, set the option to one of the allowed values: [%s]',
                                            $v,
                                            implode(', ', [self::EXCEPTION_STRATEGY_CUSTOM, self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND])
                                        ),
                                        E_USER_DEPRECATED
                                    );

                                    return $v;
                                })
                            ->end()
                        ->end()
                        ->scalarNode('not_valid_current_page')
                            ->defaultValue(self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND)
                            ->validate()
                                ->ifTrue(static function ($v) { return !\in_array($v, [self::EXCEPTION_STRATEGY_CUSTOM, self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND]); })
                                ->then(static function ($v) {
                                    @trigger_error(
                                        sprintf(
                                            'Setting the "babdev_pagerfanta.exceptions_strategy.not_valid_current_page" configuration option to "%s" is deprecated since BabDevPagerfantaBundle 2.2, set the option to one of the allowed values: [%s]',
                                            $v,
                                            implode(', ', [self::EXCEPTION_STRATEGY_CUSTOM, self::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND])
                                        ),
                                        E_USER_DEPRECATED
                                    );

                                    return $v;
                                })
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
