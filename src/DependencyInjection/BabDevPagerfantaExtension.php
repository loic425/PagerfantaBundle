<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection;

use BabDev\PagerfantaBundle\EventListener\ConvertNotValidCurrentPageToNotFoundListener;
use BabDev\PagerfantaBundle\EventListener\ConvertNotValidMaxPerPageToNotFoundListener;
use Pagerfanta\Twig\Extension\PagerfantaExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\KernelEvents;

final class BabDevPagerfantaExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'babdev_pagerfanta';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);
        $container->setParameter('babdev_pagerfanta.default_twig_template', $config['default_twig_template']);
        $container->setParameter('babdev_pagerfanta.default_view', $config['default_view']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('pagerfanta.xml');

        $bundles = $container->getParameter('kernel.bundles');

        if (isset($bundles['TwigBundle'])) {
            $loader->load('twig.xml');
        }

        if (Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND === $config['exceptions_strategy']['out_of_range_page']) {
            $container->register('pagerfanta.event_listener.convert_not_valid_max_per_page_to_not_found', ConvertNotValidCurrentPageToNotFoundListener::class)
                ->addTag(
                    'kernel.event_listener',
                    [
                        'event' => KernelEvents::EXCEPTION,
                        'method' => 'onKernelException',
                        'priority' => 512,
                    ]
                );
        }

        if (Configuration::EXCEPTION_STRATEGY_TO_HTTP_NOT_FOUND === $config['exceptions_strategy']['not_valid_current_page']) {
            $container->register('pagerfanta.event_listener.convert_not_valid_current_page_to_not_found', ConvertNotValidMaxPerPageToNotFoundListener::class)
                ->addTag(
                    'kernel.event_listener',
                    [
                        'event' => KernelEvents::EXCEPTION,
                        'method' => 'onKernelException',
                        'priority' => 512,
                    ]
                );
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('twig')) {
            return;
        }

        $refl = new \ReflectionClass(PagerfantaExtension::class);
        $path = \dirname($refl->getFileName(), 2).'/templates/';

        $container->prependExtensionConfig('twig', ['paths' => ['Pagerfanta' => $path]]);
    }
}
