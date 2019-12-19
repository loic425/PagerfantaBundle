<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddPagerfantasPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('pagerfanta.view_factory')) {
            return;
        }

        $views = [];

        foreach ($container->findTaggedServiceIds('pagerfanta.view') as $serviceId => $arguments) {
            $alias = isset($arguments[0]['alias']) ? $arguments[0]['alias'] : $serviceId;

            $views[$alias] = new Reference($serviceId);
        }

        $container->getDefinition('pagerfanta.view_factory')->addMethodCall('add', [$views]);
    }
}
