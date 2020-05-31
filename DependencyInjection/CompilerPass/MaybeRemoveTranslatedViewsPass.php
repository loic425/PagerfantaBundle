<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @deprecated to be removed in BabDevPagerfantaBundle 3.0.
 */
final class MaybeRemoveTranslatedViewsPass implements CompilerPassInterface
{
    /** @var bool */
    private $internalUse;

    public function __construct($internalUse = false)
    {
        $this->internalUse = $internalUse;
    }

    public function process(ContainerBuilder $container): void
    {
        if (false === $this->internalUse) {
            trigger_deprecation('babdev/pagerfanta-bundle', '2.2', 'The "%s" class is deprecated and will be removed in 3.0.', MaybeRemoveTranslatedViewsPass::class);
        }

        if (!$container->hasDefinition('translator')) {
            $container->removeDefinition('pagerfanta.view.default_translated');
            $container->removeDefinition('pagerfanta.view.semantic_ui_translated');
            $container->removeDefinition('pagerfanta.view.twitter_bootstrap_translated');
            $container->removeDefinition('pagerfanta.view.twitter_bootstrap3_translated');
            $container->removeDefinition('pagerfanta.view.twitter_bootstrap4_translated');
        }
    }
}
