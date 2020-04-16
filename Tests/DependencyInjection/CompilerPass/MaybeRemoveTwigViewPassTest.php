<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection\CompilerPass;

use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\MaybeRemoveTwigViewPass;
use BabDev\PagerfantaBundle\View\TwigView;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Environment;

final class MaybeRemoveTwigViewPassTest extends AbstractCompilerPassTestCase
{
    public function testTheTwigViewIsRemovedWhenTwigIsNotRegistered(): void
    {
        $this->registerService('pagerfanta.view.twig', TwigView::class)
            ->addTag('pagerfanta.view', ['alias' => 'twig']);

        $this->compile();

        $this->assertContainerBuilderNotHasService('pagerfanta.view.twig');
    }

    public function testTheTwigViewIsKeptWhenTwigIsRegistered(): void
    {
        $this->registerService('twig', Environment::class);
        $this->registerService('pagerfanta.view.twig', TwigView::class)
            ->addTag('pagerfanta.view', ['alias' => 'twig']);

        $this->compile();

        $this->assertContainerBuilderHasService('pagerfanta.view.twig');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MaybeRemoveTwigViewPass());
    }
}
