<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use BabDev\PagerfantaBundle\View\TwitterBootstrap4TranslatedView;
use Pagerfanta\View\TwitterBootstrap4View;

final class TwitterBootstrap4TranslatedViewTest extends TwitterBootstrapTranslatedViewTest
{
    protected function decoratedViewClass(): string
    {
        return TwitterBootstrap4View::class;
    }

    protected function translatedViewClass(): string
    {
        return TwitterBootstrap4TranslatedView::class;
    }

    protected function translatedViewName(): string
    {
        return 'twitter_bootstrap4_translated';
    }
}
