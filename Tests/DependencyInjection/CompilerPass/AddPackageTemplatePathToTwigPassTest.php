<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection\CompilerPass;

use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\AddPackageTemplatePathToTwigPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Twig\Loader\FilesystemLoader;

final class AddPackageTemplatePathToTwigPassTest extends AbstractCompilerPassTestCase
{
    public function testThePackateTemplatePathIsAddedToTheTwigFilesystemLoader(): void
    {
        $this->registerService('twig.loader.native_filesystem', FilesystemLoader::class);

        $this->compile();

        $refl = new \ReflectionClass(Pagerfanta::class);
        $path = \dirname($refl->getFileName()).'/../templates';

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'twig.loader.native_filesystem',
            'addPath',
            [$path, 'Pagerfanta']
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddPackageTemplatePathToTwigPass());
    }
}
