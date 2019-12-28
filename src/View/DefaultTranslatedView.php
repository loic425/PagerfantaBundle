<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

class DefaultTranslatedView extends TranslatedView
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
        return sprintf('&#171; %s', $text);
    }

    protected function buildNextMessage($text)
    {
        return sprintf('%s &#187;', $text);
    }

    public function getName()
    {
        return 'default_translated';
    }
}
