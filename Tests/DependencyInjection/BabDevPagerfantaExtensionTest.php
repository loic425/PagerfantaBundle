<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection;

use BabDev\PagerfantaBundle\DependencyInjection\BabDevPagerfantaExtension;
use BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorFactoryInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Pagerfanta\View\ViewFactory;
use Pagerfanta\View\ViewFactoryInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\HttpKernel\KernelEvents;

final class BabDevPagerfantaExtensionTest extends AbstractExtensionTestCase
{
    public function testContainerIsLoadedWithDefaultConfiguration(): void
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('babdev_pagerfanta.default_view');
        $this->assertContainerBuilderHasParameter('babdev_pagerfanta.default_twig_template');

        $this->assertContainerBuilderHasAlias(ViewFactory::class, 'pagerfanta.view_factory');
        $this->assertContainerBuilderHasAlias(ViewFactoryInterface::class, 'pagerfanta.view_factory');

        $deprecatedViews = [
            'pagerfanta.view.default_translated',
            'pagerfanta.view.semantic_ui_translated',
            'pagerfanta.view.twitter_bootstrap_translated',
            'pagerfanta.view.twitter_bootstrap3_translated',
            'pagerfanta.view.twitter_bootstrap4_translated',
        ];

        foreach ($deprecatedViews as $deprecatedView) {
            $this->assertContainerBuilderHasService($deprecatedView);
            $this->assertTrue($this->container->getDefinition($deprecatedView)->isDeprecated());
        }

        if (method_exists(Alias::class, 'setDeprecated')) {
            $deprecatedAliases = [
                RouteGeneratorFactoryInterface::class,
            ];

            foreach ($deprecatedAliases as $deprecatedAlias) {
                $this->assertContainerBuilderHasAlias($deprecatedAlias);
                $this->assertTrue($this->container->getAlias($deprecatedAlias)->isDeprecated());
            }
        }

        $listeners = [
            'pagerfanta.event_listener.convert_not_valid_max_per_page_to_not_found',
            'pagerfanta.event_listener.convert_not_valid_current_page_to_not_found',
        ];

        foreach ($listeners as $listener) {
            $this->assertContainerBuilderHasServiceDefinitionWithTag(
                $listener,
                'kernel.event_listener',
                [
                    'event' => KernelEvents::EXCEPTION,
                    'method' => 'onKernelException',
                    'priority' => 512,
                ]
            );
        }
    }

    public function testContainerIsLoadedWhenBundleIsConfiguredWithCustomExceptionStrategies(): void
    {
        $bundleConfig = [
            'exceptions_strategy' => [
                'out_of_range_page' => 'custom_handler',
                'not_valid_current_page' => 'custom_handler',
            ],
        ];

        $this->load($bundleConfig);

        $this->assertContainerBuilderHasParameter('babdev_pagerfanta.default_view');
        $this->assertContainerBuilderHasParameter('babdev_pagerfanta.default_twig_template');

        $this->assertContainerBuilderHasAlias(ViewFactory::class, 'pagerfanta.view_factory');
        $this->assertContainerBuilderHasAlias(ViewFactoryInterface::class, 'pagerfanta.view_factory');

        $deprecatedViews = [
            'pagerfanta.view.default_translated',
            'pagerfanta.view.semantic_ui_translated',
            'pagerfanta.view.twitter_bootstrap_translated',
            'pagerfanta.view.twitter_bootstrap3_translated',
            'pagerfanta.view.twitter_bootstrap4_translated',
        ];

        foreach ($deprecatedViews as $deprecatedView) {
            $this->assertContainerBuilderHasService($deprecatedView);
            $this->assertTrue($this->container->getDefinition($deprecatedView)->isDeprecated());
        }

        if (method_exists(Alias::class, 'setDeprecated')) {
            $deprecatedAliases = [
                RouteGeneratorFactoryInterface::class,
            ];

            foreach ($deprecatedAliases as $deprecatedAlias) {
                $this->assertContainerBuilderHasAlias($deprecatedAlias);
                $this->assertTrue($this->container->getAlias($deprecatedAlias)->isDeprecated());
            }
        }

        $listeners = [
            'pagerfanta.event_listener.convert_not_valid_max_per_page_to_not_found',
            'pagerfanta.event_listener.convert_not_valid_current_page_to_not_found',
        ];

        foreach ($listeners as $listener) {
            $this->assertContainerBuilderNotHasService($listener);
        }
    }

    protected function getContainerExtensions(): array
    {
        return [
            new BabDevPagerfantaExtension(),
        ];
    }
}
