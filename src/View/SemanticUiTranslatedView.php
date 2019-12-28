<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

class SemanticUiTranslatedView extends TranslatedView
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
        return sprintf('&larr; %s', $text);
    }

    protected function buildNextMessage(string $text): string
    {
        return sprintf('%s &rarr;', $text);
    }

    public function getName(): string
    {
        return 'semantic_ui_translated';
    }
}
