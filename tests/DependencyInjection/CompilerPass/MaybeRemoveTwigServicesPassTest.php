<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection\CompilerPass;

use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\MaybeRemoveTwigServicesPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Pagerfanta\Twig\Extension\PagerfantaExtension;
use Pagerfanta\Twig\Extension\PagerfantaRuntime;
use Pagerfanta\Twig\View\TwigView;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Environment;

final class MaybeRemoveTwigServicesPassTest extends AbstractCompilerPassTestCase
{
    public function testTheTwigServicesAreRemovedWhenTwigIsNotRegistered(): void
    {
        $this->registerService('pagerfanta.twig_extension', PagerfantaExtension::class)
            ->addTag('twig.extension');
        $this->registerService('pagerfanta.twig_runtime', PagerfantaRuntime::class)
            ->addTag('twig.runtime');
        $this->registerService('pagerfanta.view.twig', TwigView::class)
            ->addTag('pagerfanta.view', ['alias' => 'twig']);

        $this->compile();

        $this->assertContainerBuilderNotHasService('pagerfanta.twig_extension');
        $this->assertContainerBuilderNotHasService('pagerfanta.twig_runtime');
        $this->assertContainerBuilderNotHasService('pagerfanta.view.twig');
    }

    public function testTheTwigServicesAreKeptWhenTwigIsRegistered(): void
    {
        $this->registerService('twig', Environment::class);
        $this->registerService('pagerfanta.twig_extension', PagerfantaExtension::class)
            ->addTag('twig.extension');
        $this->registerService('pagerfanta.twig_runtime', PagerfantaRuntime::class)
            ->addTag('twig.runtime');
        $this->registerService('pagerfanta.view.twig', TwigView::class)
            ->addTag('pagerfanta.view', ['alias' => 'twig']);

        $this->compile();

        $this->assertContainerBuilderHasService('pagerfanta.twig_extension');
        $this->assertContainerBuilderHasService('pagerfanta.twig_runtime');
        $this->assertContainerBuilderHasService('pagerfanta.view.twig');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MaybeRemoveTwigServicesPass());
    }
}
