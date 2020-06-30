<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection\CompilerPass;

use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\MaybeRemoveTranslatedViewsPass;
use BabDev\PagerfantaBundle\View\DefaultTranslatedView;
use BabDev\PagerfantaBundle\View\SemanticUiTranslatedView;
use BabDev\PagerfantaBundle\View\TwitterBootstrap3TranslatedView;
use BabDev\PagerfantaBundle\View\TwitterBootstrap4TranslatedView;
use BabDev\PagerfantaBundle\View\TwitterBootstrapTranslatedView;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Translation\Translator;

final class MaybeRemoveTranslatedViewsPassTest extends AbstractCompilerPassTestCase
{
    private const TRANSLATED_VIEWS = [
        'pagerfanta.view.default_translated' => DefaultTranslatedView::class,
        'pagerfanta.view.semantic_ui_translated' => SemanticUiTranslatedView::class,
        'pagerfanta.view.twitter_bootstrap_translated' => TwitterBootstrapTranslatedView::class,
        'pagerfanta.view.twitter_bootstrap3_translated' => TwitterBootstrap3TranslatedView::class,
        'pagerfanta.view.twitter_bootstrap4_translated' => TwitterBootstrap4TranslatedView::class,
    ];

    public function testTheTranslatedViewsAreRemovedWhenTheTranslatorIsNotRegistered(): void
    {
        foreach (self::TRANSLATED_VIEWS as $serviceId => $serviceClass) {
            $this->registerService($serviceId, $serviceClass)
                 ->addTag('pagerfanta.view');
        }

        $this->compile();

        foreach (array_keys(self::TRANSLATED_VIEWS) as $serviceId) {
            $this->assertContainerBuilderNotHasService($serviceId);
        }
    }

    public function testTheTranslatedViewsAreNotRemovedWhenTheTranslatorIsRegistered(): void
    {
        $this->registerService('translator', Translator::class);

        foreach (self::TRANSLATED_VIEWS as $serviceId => $serviceClass) {
            $this->registerService($serviceId, $serviceClass)
                 ->addTag('pagerfanta.view');
        }

        $this->compile();

        foreach (array_keys(self::TRANSLATED_VIEWS) as $serviceId) {
            $this->assertContainerBuilderHasService($serviceId);
        }
    }

    public function testTheTranslatedViewsAreNotRemovedWhenTheTranslatorAliasIsRegistered(): void
    {
        $this->registerService('my-translator', Translator::class);
        $this->container->addAliases([
            'translator' => 'my-translator',
        ]);

        foreach (self::TRANSLATED_VIEWS as $serviceId => $serviceClass) {
            $this->registerService($serviceId, $serviceClass)
                 ->addTag('pagerfanta.view');
        }

        $this->compile();

        foreach (array_keys(self::TRANSLATED_VIEWS) as $serviceId) {
            $this->assertContainerBuilderHasService($serviceId);
        }
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new MaybeRemoveTranslatedViewsPass());
    }
}
