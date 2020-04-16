<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

@trigger_error(sprintf('The "%s" class is deprecated and will be removed in BabDevPagerfantaBundle 3.0. Use the "%s" class instead.', TwitterBootstrap3TranslatedView::class, TwigView::class));

/**
 * @deprecated to be removed in BabDevPagerfantaBundle 3.0. Use the Twig view class instead with the `twitter_bootstrap3.html.twig` template.
 */
class TwitterBootstrap3TranslatedView extends TwitterBootstrapTranslatedView
{
    public function getName()
    {
        return 'twitter_bootstrap3_translated';
    }
}
