<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection;

use BabDev\PagerfantaBundle\BabDevPagerfantaBundle;
use BabDev\PagerfantaBundle\DependencyInjection\BabDevPagerfantaExtension;
use BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorFactoryInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Pagerfanta\Twig\Extension\PagerfantaExtension;
use Pagerfanta\View\ViewFactory;
use Pagerfanta\View\ViewFactoryInterface;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;

final class BabDevPagerfantaExtensionTest extends AbstractExtensionTestCase
{
    public function testContainerIsLoadedWithDefaultConfigurationWhenTwigBundleIsNotInstalled(): void
    {
        $this->container->setParameter(
            'kernel.bundles',
            [
                'BabDevPagerfantaBundle' => BabDevPagerfantaBundle::class,
            ]
        );

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

        $twigServices = [
            'pagerfanta.twig_extension',
            'pagerfanta.twig_runtime',
            'pagerfanta.view.twig',
        ];

        foreach ($twigServices as $twigService) {
            $this->assertContainerBuilderNotHasService($twigService);
        }
    }

    public function testContainerIsLoadedWithDefaultConfigurationWhenTwigBundleIsInstalled(): void
    {
        $this->container->registerExtension(new TwigExtension());

        $this->container->setParameter(
            'kernel.bundles',
            [
                'BabDevPagerfantaBundle' => BabDevPagerfantaBundle::class,
                'TwigBundle' => TwigBundle::class,
            ]
        );

        $this->container->setParameter(
            'kernel.bundles_metadata',
            [
                'BabDevPagerfantaBundle' => [
                    'path' => (__DIR__.'/../../'),
                    'namespace' => 'BabDev\\PagerfantaBundle',
                ],
                'TwigBundle' => [
                    'path' => (__DIR__.'/../../vendor/symfony/twig-bundle'),
                    'namespace' => 'Symfony\\Bundle\\TwigBundle',
                ],
            ]
        );

        $this->container->setParameter('kernel.debug', false);
        $this->container->setParameter('kernel.project_dir', __DIR__);

        if (method_exists(Kernel::class, 'getRootDir')) {
            $this->container->setParameter('kernel.root_dir', __DIR__);
        }

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

        $twigServices = [
            'pagerfanta.twig_extension',
            'pagerfanta.twig_runtime',
            'pagerfanta.view.twig',
        ];

        foreach ($twigServices as $twigService) {
            $this->assertContainerBuilderHasService($twigService);
        }

        $twigConfig = $this->container->getExtensionConfig('twig');

        $refl = new \ReflectionClass(PagerfantaExtension::class);
        $path = \dirname($refl->getFileName(), 2).'/templates/';

        $this->assertArrayHasKey($path, $twigConfig[0]['paths']);
    }

    public function testContainerIsLoadedWhenBundleIsConfiguredWithCustomExceptionStrategies(): void
    {
        $this->container->setParameter(
            'kernel.bundles',
            [
                'BabDevPagerfantaBundle' => BabDevPagerfantaBundle::class,
            ]
        );

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
