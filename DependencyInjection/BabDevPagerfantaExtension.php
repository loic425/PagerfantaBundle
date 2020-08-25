<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection;

use BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorFactoryInterface as BundleRouteGeneratorFactoryInterface;
use Pagerfanta\RouteGenerator\RouteGeneratorFactoryInterface as PagerfantaRouteGeneratorFactoryInterface;
use Pagerfanta\Twig\Extension\PagerfantaExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\KernelEvents;

final class BabDevPagerfantaExtension extends Extension implements PrependExtensionInterface
{
    private const DEPRECATED_ALIASES = [
        BundleRouteGeneratorFactoryInterface::class => PagerfantaRouteGeneratorFactoryInterface::class,
    ];

    private const DEPRECATED_VIEW_SERVICES = [
        'pagerfanta.view.default_translated' => 'default.html.twig',
        'pagerfanta.view.semantic_ui_translated' => 'semantic_ui.html.twig',
        'pagerfanta.view.twitter_bootstrap_translated' => 'twitter_bootstrap.html.twig',
        'pagerfanta.view.twitter_bootstrap3_translated' => 'twitter_bootstrap3.html.twig',
        'pagerfanta.view.twitter_bootstrap4_translated' => 'twitter_bootstrap.4html.twig',
    ];

    public function getAlias()
    {
        return 'babdev_pagerfanta';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);
        $container->setParameter('babdev_pagerfanta.default_twig_template', $config['default_twig_template']);
        $container->setParameter('babdev_pagerfanta.default_view', $config['default_view']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('pagerfanta.xml');

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['TwigBundle'])) {
            $loader->load('twig.xml');
        }

        if (Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND === $config['exceptions_strategy']['out_of_range_page']) {
            $container->getDefinition('pagerfanta.event_listener.convert_not_valid_max_per_page_to_not_found')
                ->addTag(
                    'kernel.event_listener',
                    [
                        'event' => KernelEvents::EXCEPTION,
                        'method' => 'onKernelException',
                        'priority' => 512,
                    ]
                );
        } else {
            $container->removeDefinition('pagerfanta.event_listener.convert_not_valid_max_per_page_to_not_found');
        }

        if (Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND === $config['exceptions_strategy']['not_valid_current_page']) {
            $container->getDefinition('pagerfanta.event_listener.convert_not_valid_current_page_to_not_found')
                ->addTag(
                    'kernel.event_listener',
                    [
                        'event' => KernelEvents::EXCEPTION,
                        'method' => 'onKernelException',
                        'priority' => 512,
                    ]
                );
        } else {
            $container->removeDefinition('pagerfanta.event_listener.convert_not_valid_current_page_to_not_found');
        }

        $this->deprecateAliases($container);
        $this->deprecateServices($container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('twig')) {
            return;
        }

        $refl = new \ReflectionClass(PagerfantaExtension::class);
        $path = \dirname($refl->getFileName(), 2).'/templates/';

        $container->prependExtensionConfig('twig', ['paths' => [$path => 'Pagerfanta']]);
    }

    private function deprecateAliases(ContainerBuilder $container): void
    {
        if (!method_exists(Alias::class, 'setDeprecated')) {
            return;
        }

        $usesSymfony51Api = method_exists(Alias::class, 'getDeprecation');

        foreach (self::DEPRECATED_ALIASES as $aliasId => $replacementAlias) {
            $alias = $container->getAlias($aliasId);

            if ($usesSymfony51Api) {
                $alias->setDeprecated(
                    'babdev/pagerfanta-bundle',
                    '2.5',
                    str_replace('%replacement_alias_id%', $replacementAlias, 'The "%alias_id%" alias is deprecated and will be removed in BabDevPagerfantaBundle 3.0. Use the "%replacement_alias_id%" alias instead.')
                );
            } else {
                $alias->setDeprecated(
                    true,
                    str_replace('%replacement_alias_id%', $replacementAlias, 'The "%alias_id%" alias is deprecated and will be removed in BabDevPagerfantaBundle 3.0. Use the "%replacement_alias_id%" alias instead.')
                );
            }
        }
    }

    private function deprecateServices(ContainerBuilder $container): void
    {
        $usesSymfony51Api = method_exists(Definition::class, 'getDeprecation');

        foreach (self::DEPRECATED_VIEW_SERVICES as $serviceId => $replacementTemplate) {
            $service = $container->getDefinition($serviceId);

            if ($usesSymfony51Api) {
                $service->setDeprecated(
                    'babdev/pagerfanta-bundle',
                    '2.2',
                    str_replace('%template%', $replacementTemplate, 'The "%service_id%" service is deprecated and will be removed in BabDevPagerfantaBundle 3.0. Use the "pagerfanta.view.twig" service with the "@BabDevPagerfanta/%template%" template instead.')
                );
            } else {
                $service->setDeprecated(
                    true,
                    str_replace('%template%', $replacementTemplate, 'The "%service_id%" service is deprecated and will be removed in BabDevPagerfantaBundle 3.0. Use the "pagerfanta.view.twig" service with the "@BabDevPagerfanta/%template%" template instead.')
                );
            }
        }
    }
}
