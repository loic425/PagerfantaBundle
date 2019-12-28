<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use BabDev\PagerfantaBundle\View\TwitterBootstrapTranslatedView;
use Pagerfanta\View\TwitterBootstrapView;

class TwitterBootstrapTranslatedViewTest extends TranslatedViewTestCase
{
    protected function decoratedViewClass(): string
    {
        return TwitterBootstrapView::class;
    }

    protected function translatedViewClass(): string
    {
        return TwitterBootstrapTranslatedView::class;
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
        return 'twitter_bootstrap_translated';
    }
}
