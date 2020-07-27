<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

use Pagerfanta\Twig\View\TwigView;

trigger_deprecation('babdev/pagerfanta-bundle', '2.2', 'The "%s" class is deprecated and will be removed in 3.0. Use the "%s" class instead.', SemanticUiTranslatedView::class, TwigView::class);

/**
 * @deprecated to be removed in BabDevPagerfantaBundle 3.0. Use the Twig view class instead with the `semantic_ui.html.twig` template.
 */
class SemanticUiTranslatedView extends TranslatedView
{
    protected function previousMessageOption()
    {
        return 'prev_message';
    }

    protected function nextMessageOption()
    {
        return 'next_message';
    }

    protected function buildPreviousMessage($text)
    {
        return sprintf('&larr; %s', $text);
    }

    protected function buildNextMessage($text)
    {
        return sprintf('%s &rarr;', $text);
    }

    public function getName()
    {
        return 'semantic_ui_translated';
    }
}
