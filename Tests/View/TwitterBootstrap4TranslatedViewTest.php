<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

class TwitterBootstrap4TranslatedViewTest extends TwitterBootstrapTranslatedViewTest
{
    protected function viewClass()
    {
        return 'Pagerfanta\View\TwitterBootstrap4View';
    }

    protected function translatedViewClass()
    {
        return 'BabDev\PagerfantaBundle\View\TwitterBootstrap4TranslatedView';
    }

    protected function translatedViewName()
    {
        return 'twitter_bootstrap4_translated';
    }
}
