<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use BabDev\PagerfantaBundle\Twig\PagerfantaExtension;
use BabDev\PagerfantaBundle\Twig\PagerfantaRuntime;
use BabDev\PagerfantaBundle\View\TwigView;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewFactory;
use Pagerfanta\View\ViewFactoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

/**
 * Integration tests which simulates a real Twig environment to validate templates are correctly generated.
 */
final class TwigViewIntegrationTest extends TestCase
{
    /**
     * @var ViewFactoryInterface
     */
    public $viewFactory;

    /**
     * @var UrlGeneratorInterface
     */
    public $router;

    /**
     * @var RequestStack
     */
    public $requestStack;

    /**
     * @var Environment
     */
    public $twig;

    protected function setUp(): void
    {
        $filesystemLoader = new FilesystemLoader();
        $filesystemLoader->addPath(__DIR__.'/../../Resources/views', 'BabDevPagerfantaBundle');

        $this->twig = new Environment(new ChainLoader([new ArrayLoader(['integration.html.twig' => '{{ pagerfanta(pager, options) }}']), $filesystemLoader]));
        $this->twig->addExtension(new PagerfantaExtension());
        $this->twig->addExtension(new TranslationExtension($this->createTranslator()));
        $this->twig->addRuntimeLoader($this->createRuntimeLoader());

        $this->router = $this->createRouter();
        $this->requestStack = new RequestStack();
    }

    protected function tearDown(): void
    {
        do {
            $request = $this->requestStack->pop();
        } while (null !== $request);
    }

    private function createPagerfanta(): Pagerfanta
    {
        return new Pagerfanta(new FixedAdapter(100, range(1, 100)));
    }

    public function dataPagerfantaRenderer(): \Generator
    {
        yield 'default template at page 1' => [
            '@BabDevPagerfantaBundle/default.html.twig',
            1,
            false,
            '<nav>
    <span class="disabled">Previous</span>
    <span class="current">1</span>
    <a href="/pagerfanta-view?page=2">2</a>
    <a href="/pagerfanta-view?page=3">3</a>
    <a href="/pagerfanta-view?page=4">4</a>
    <a href="/pagerfanta-view?page=5">5</a>
    <span class="dots">...</span>
    <a href="/pagerfanta-view?page=10">10</a>
    <a href="/pagerfanta-view?page=2" rel="next">Next</a>
</nav>'
        ];

        yield 'default template at page 5 with first page omitted' => [
            '@BabDevPagerfantaBundle/default.html.twig',
            5,
            true,
            '<nav>
    <a href="/pagerfanta-view?page=4" rel="prev">Previous</a>
    <a href="/pagerfanta-view">1</a>
    <a href="/pagerfanta-view?page=2">2</a>
    <a href="/pagerfanta-view?page=3">3</a>
    <a href="/pagerfanta-view?page=4">4</a>
    <span class="current">5</span>
    <a href="/pagerfanta-view?page=6">6</a>
    <a href="/pagerfanta-view?page=7">7</a>
    <span class="dots">...</span>
    <a href="/pagerfanta-view?page=10">10</a>
    <a href="/pagerfanta-view?page=6" rel="next">Next</a>
</nav>'
        ];

        yield 'Semantic UI template at page 1' => [
            '@BabDevPagerfantaBundle/semantic_ui.html.twig',
            1,
            false,
            '<div class="ui stackable fluid pagination menu">
    <div class="item disabled">Previous</div>
    <div class="item active">1</div>
    <a class="item" href="/pagerfanta-view?page=2">2</a>
    <a class="item" href="/pagerfanta-view?page=3">3</a>
    <a class="item" href="/pagerfanta-view?page=4">4</a>
    <a class="item" href="/pagerfanta-view?page=5">5</a>
    <div class="item disabled">&hellip;</div>
    <a class="item" href="/pagerfanta-view?page=10">10</a>
    <a class="item next" href="/pagerfanta-view?page=2">Next</a>
</div>'
        ];

        yield 'Semantic UI template at page 5 with first page omitted' => [
            '@BabDevPagerfantaBundle/semantic_ui.html.twig',
            5,
            true,
            '<div class="ui stackable fluid pagination menu">
    <a class="item prev" href="/pagerfanta-view?page=4">Previous</a>
    <a class="item" href="/pagerfanta-view">1</a>
    <a class="item" href="/pagerfanta-view?page=2">2</a>
    <a class="item" href="/pagerfanta-view?page=3">3</a>
    <a class="item" href="/pagerfanta-view?page=4">4</a>
    <div class="item active">5</div>
    <a class="item" href="/pagerfanta-view?page=6">6</a>
    <a class="item" href="/pagerfanta-view?page=7">7</a>
    <div class="item disabled">&hellip;</div>
    <a class="item" href="/pagerfanta-view?page=10">10</a>
    <a class="item next" href="/pagerfanta-view?page=6">Next</a>
</div>'
        ];

        yield 'Twitter Bootstrap template at page 1' => [
            '@BabDevPagerfantaBundle/twitter_bootstrap.html.twig',
            1,
            false,
            '<div class="pagination">
    <ul>
        <li class="prev disabled"><span>&larr; Previous</span></li>
        <li class="active"><span>1</span></li>
        <li><a href="/pagerfanta-view?page=2">2</a></li>
        <li><a href="/pagerfanta-view?page=3">3</a></li>
        <li><a href="/pagerfanta-view?page=4">4</a></li>
        <li><a href="/pagerfanta-view?page=5">5</a></li>
        <li class="disabled"><span>&hellip;</span></li>
        <li><a href="/pagerfanta-view?page=10">10</a></li>
        <li class="next"><a href="/pagerfanta-view?page=2" rel="next">Next &rarr;</a></li>
    </ul>
</div>'
        ];

        yield 'Twitter Bootstrap template at page 5 with first page omitted' => [
            '@BabDevPagerfantaBundle/twitter_bootstrap.html.twig',
            5,
            true,
            '<div class="pagination">
    <ul>
        <li class="prev"><a href="/pagerfanta-view?page=4" rel="prev">&larr; Previous</a></li>
        <li><a href="/pagerfanta-view">1</a></li>
        <li><a href="/pagerfanta-view?page=2">2</a></li>
        <li><a href="/pagerfanta-view?page=3">3</a></li>
        <li><a href="/pagerfanta-view?page=4">4</a></li>
        <li class="active"><span>5</span></li>
        <li><a href="/pagerfanta-view?page=6">6</a></li>
        <li><a href="/pagerfanta-view?page=7">7</a></li>
        <li class="disabled"><span>&hellip;</span></li>
        <li><a href="/pagerfanta-view?page=10">10</a></li>
        <li class="next"><a href="/pagerfanta-view?page=6" rel="next">Next &rarr;</a></li>
    </ul>
</div>'
        ];

        yield 'Twitter Bootstrap 3 template at page 1' => [
            '@BabDevPagerfantaBundle/twitter_bootstrap3.html.twig',
            1,
            false,
            '<ul class="pagination">
    <li class="prev disabled"><span>&larr; Previous</span></li>
    <li class="active"><span>1</span></li>
    <li><a href="/pagerfanta-view?page=2">2</a></li>
    <li><a href="/pagerfanta-view?page=3">3</a></li>
    <li><a href="/pagerfanta-view?page=4">4</a></li>
    <li><a href="/pagerfanta-view?page=5">5</a></li>
    <li class="disabled"><span>&hellip;</span></li>
    <li><a href="/pagerfanta-view?page=10">10</a></li>
    <li class="next"><a href="/pagerfanta-view?page=2" rel="next">Next &rarr;</a></li>
</ul>'
        ];

        yield 'Twitter Bootstrap 3 template at page 5 with first page omitted' => [
            '@BabDevPagerfantaBundle/twitter_bootstrap3.html.twig',
            5,
            true,
            '<ul class="pagination">
    <li class="prev"><a href="/pagerfanta-view?page=4" rel="prev">&larr; Previous</a></li>
    <li><a href="/pagerfanta-view">1</a></li>
    <li><a href="/pagerfanta-view?page=2">2</a></li>
    <li><a href="/pagerfanta-view?page=3">3</a></li>
    <li><a href="/pagerfanta-view?page=4">4</a></li>
    <li class="active"><span>5</span></li>
    <li><a href="/pagerfanta-view?page=6">6</a></li>
    <li><a href="/pagerfanta-view?page=7">7</a></li>
    <li class="disabled"><span>&hellip;</span></li>
    <li><a href="/pagerfanta-view?page=10">10</a></li>
    <li class="next"><a href="/pagerfanta-view?page=6" rel="next">Next &rarr;</a></li>
</ul>'
        ];
    }

    /**
     * @dataProvider dataPagerfantaRenderer
     */
    public function testPagerfantaRendering(string $template, int $page, bool $omitFirstPage, string $testOutput): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $pagerfanta = $this->createPagerfanta();
        $pagerfanta->setCurrentPage($page);

        $this->assertViewOutputMatches(
            $this->twig->render('integration.html.twig', ['pager' => $pagerfanta, 'options' => ['omitFirstPage' => $omitFirstPage, 'template' => $template]]),
            $testOutput
        );
    }

    private function createRouter(): UrlGeneratorInterface
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('pagerfanta_view', new Route('/pagerfanta-view'));

        return new UrlGenerator($routeCollection, new RequestContext());
    }

    private function createRuntimeLoader(): RuntimeLoaderInterface
    {
        return new class($this) implements RuntimeLoaderInterface {
            private $testCase;

            public function __construct(TwigViewIntegrationTest $testCase)
            {
                $this->testCase = $testCase;
            }

            public function load($class)
            {
                switch ($class) {
                    case PagerfantaRuntime::class:
                        $viewFactory = new ViewFactory();
                        $viewFactory->set('twig', new TwigView($this->testCase->twig));

                        return new PagerfantaRuntime(
                            'twig',
                            $viewFactory,
                            $this->testCase->router,
                            $this->testCase->requestStack
                        );

                    default:
                        return;
                }
            }
        };
    }

    private function createTranslator(): Translator
    {
        $translator = new Translator('en');
        $translator->addLoader('xliff', new XliffFileLoader());
        $translator->addResource('xliff', __DIR__.'/../../Resources/translations/pagerfanta.en.xliff', 'en', 'pagerfanta');

        return $translator;
    }

    private function assertViewOutputMatches(string $view, string $expected): void
    {
        $this->assertSame($this->removeWhitespacesBetweenTags($expected), $view);
    }

    private function removeWhitespacesBetweenTags($string)
    {
        return preg_replace('/>\s+</', '><', $string);
    }
}
