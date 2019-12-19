<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

class TwitterBootstrapTranslatedViewTest extends TranslatedViewTest
{
    protected function viewClass()
    {
        return 'Pagerfanta\View\TwitterBootstrapView';
    }

    protected function translatedViewClass()
    {
        return 'BabDev\PagerfantaBundle\View\TwitterBootstrapTranslatedView';
    }

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

    protected function translatedViewName()
    {
        return 'twitter_bootstrap_translated';
    }
}
