<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

class TwitterBootstrap3TranslatedViewTest extends TwitterBootstrapTranslatedViewTest
{
    protected function viewClass()
    {
        return 'Pagerfanta\View\TwitterBootstrap3View';
    }

    protected function translatedViewClass()
    {
        return 'BabDev\PagerfantaBundle\View\TwitterBootstrap3TranslatedView';
    }

    protected function translatedViewName()
    {
        return 'twitter_bootstrap3_translated';
    }
}
