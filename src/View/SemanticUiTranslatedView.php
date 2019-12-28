<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

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
