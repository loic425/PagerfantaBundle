<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection\CompilerPass;

use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddPackageTemplatePathToTwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('twig.loader.native_filesystem')) {
            $refl = new \ReflectionClass(Pagerfanta::class);
            $path = \dirname($refl->getFileName()).'/../templates';

            $container->getDefinition('twig.loader.native_filesystem')
                ->addMethodCall('addPath', [$path, 'Pagerfanta']);
        }
    }
}
