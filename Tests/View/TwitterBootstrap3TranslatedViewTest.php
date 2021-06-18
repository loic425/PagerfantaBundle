<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use BabDev\PagerfantaBundle\View\TwitterBootstrap3TranslatedView;
use Pagerfanta\View\TwitterBootstrap3View;

/**
 * @group legacy
 */
final class TwitterBootstrap3TranslatedViewTest extends TwitterBootstrapTranslatedViewTest
{
    protected function decoratedViewClass(): string
    {
        return TwitterBootstrap3View::class;
    }

    protected function translatedViewClass(): string
    {
        return TwitterBootstrap3TranslatedView::class;
    }

    protected function translatedViewName(): string
    {
        return 'twitter_bootstrap3_translated';
    }
}
