<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use BabDev\PagerfantaBundle\View\DefaultTranslatedView;
use Pagerfanta\View\DefaultView;

/**
 * @group legacy
 */
final class DefaultTranslatedViewTest extends TranslatedViewTestCase
{
    protected function decoratedViewClass(): string
    {
        return DefaultView::class;
    }

    protected function translatedViewClass(): string
    {
        return DefaultTranslatedView::class;
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
        return sprintf('&#171; %s', $text);
    }

    protected function buildNextMessage(string $text): string
    {
        return sprintf('%s &#187;', $text);
    }

    protected function translatedViewName(): string
    {
        return 'default_translated';
    }
}
