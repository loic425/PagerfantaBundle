<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class MaybeRemoveTranslatedViewsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('translator')) {
            $container->removeDefinition('pagerfanta.view.default_translated');
            $container->removeDefinition('pagerfanta.view.semantic_ui_translated');
            $container->removeDefinition('pagerfanta.view.twitter_bootstrap_translated');
            $container->removeDefinition('pagerfanta.view.twitter_bootstrap3_translated');
            $container->removeDefinition('pagerfanta.view.twitter_bootstrap4_translated');
        }
    }
}
