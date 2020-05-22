<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

trigger_deprecation('babdev/pagerfanta-bundle', '2.2', 'The "%s" class is deprecated and will be removed in 3.0.', MaybeRemoveTranslatedViewsPass::class);

/**
 * @deprecated to be removed in BabDevPagerfantaBundle 3.0.
 */
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
