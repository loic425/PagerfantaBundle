<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection\CompilerPass;

use Pagerfanta\Twig\Extension\PagerfantaExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddPackageTemplatePathToTwigPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('twig.loader.native_filesystem')) {
            $refl = new \ReflectionClass(PagerfantaExtension::class);
            $path = \dirname($refl->getFileName(), 2).'/templates';

            $container->getDefinition('twig.loader.native_filesystem')
                ->addMethodCall('addPath', [$path, 'Pagerfanta']);
        }
    }
}
