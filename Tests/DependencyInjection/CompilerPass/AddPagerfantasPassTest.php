<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection\CompilerPass;

use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\AddPagerfantasPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Pagerfanta\View\DefaultView;
use Pagerfanta\View\ViewFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class AddPagerfantasPassTest extends AbstractCompilerPassTestCase
{
    public function testViewsAreAddedToTheRegistry(): void
    {
        $this->registerService('pagerfanta.view_factory', ViewFactory::class);
        $this->registerService('pagerfanta.view.default', DefaultView::class)
            ->addTag('pagerfanta.view', ['alias' => 'default']);

        $this->compile();

        $this->assertContainerBuilderHasService('pagerfanta.view.default', DefaultView::class);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'pagerfanta.view_factory',
            'set',
            ['default', new Reference('pagerfanta.view.default')]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddPagerfantasPass());
    }
}
