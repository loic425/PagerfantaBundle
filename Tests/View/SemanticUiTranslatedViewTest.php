<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use BabDev\PagerfantaBundle\View\SemanticUiTranslatedView;
use Pagerfanta\View\SemanticUiView;

class SemanticUiTranslatedViewTest extends TranslatedViewTestCase
{
    protected function decoratedViewClass(): string
    {
        return SemanticUiView::class;
    }

    protected function translatedViewClass(): string
    {
        return SemanticUiTranslatedView::class;
    }

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

    protected function translatedViewName(): string
    {
        return 'semantic_ui_translated';
    }
}
