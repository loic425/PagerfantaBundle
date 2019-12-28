<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

class DefaultTranslatedView extends TranslatedView
{
    protected function previousMessageOption(): string
    {
        return 'prev_message';
    }

    protected function nextMessageOption(): string
    {
        return 'next_message';
    }

    protected function buildPreviousMessage(string $text): string
    {
        return sprintf('&#171; %s', $text);
    }

    protected function buildNextMessage(string $text): string
    {
        return sprintf('%s &#187;', $text);
    }

    public function getName(): string
    {
        return 'default_translated';
    }
}
