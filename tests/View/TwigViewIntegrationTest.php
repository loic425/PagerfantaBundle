<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use BabDev\PagerfantaBundle\RouteGenerator\RequestAwareRouteGeneratorFactory;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Twig\Extension\PagerfantaExtension;
use Pagerfanta\Twig\Extension\PagerfantaRuntime;
use Pagerfanta\Twig\View\TwigView;
use Pagerfanta\View\ViewFactory;
use Pagerfanta\View\ViewFactoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
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
     * @var PropertyAccessorInterface
     */
    public $propertyAccessor;

    /**
     * @var Environment
     */
    public $twig;

    public static function setUpBeforeClass(): void
    {
        if (!class_exists(PagerfantaExtension::class)) {
            self::markTestSkipped('Test requires Twig');
        }
    }

    protected function setUp(): void
    {
        $refl = new \ReflectionClass(PagerfantaExtension::class);
        $path = \dirname($refl->getFileName(), 2).'/templates';

        $filesystemLoader = new FilesystemLoader();
        $filesystemLoader->addPath(__DIR__.'/../../templates', 'BabDevPagerfanta');
        $filesystemLoader->addPath($path, 'Pagerfanta');

        $loader = new ChainLoader(
            [new ArrayLoader(['integration.html.twig' => '{{ pagerfanta(pager, options) }}']), $filesystemLoader]
        );
        $this->twig = new Environment($loader, ['strict_variables' => true]);
        $this->twig->addExtension(new PagerfantaExtension());
        $this->twig->addExtension(new TranslationExtension($this->createTranslator()));
        $this->twig->addRuntimeLoader($this->createRuntimeLoader());

        $this->router = $this->createRouter();
        $this->requestStack = new RequestStack();
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    protected function tearDown(): void
    {
        do {
            $request = $this->requestStack->pop();
        } while (null !== $request);
    }

    /**
     * @return Pagerfanta<int>
     */
    private function createPagerfanta(): Pagerfanta
    {
        return new Pagerfanta(new FixedAdapter(100, range(1, 100)));
    }

    public function dataPagerfantaRenderer(): \Generator
    {
        yield 'default template at page 1' => [
            1,
            ['omitFirstPage' => false, 'template' => '@BabDevPagerfanta/default.html.twig'],
            '<nav class="pagination">
    <span class="pagination__item pagination__item--previous-page pagination__item--disabled">Previous</span>
    <span class="pagination__item pagination__item--current-page" aria-current="page">1</span>
    <a class="pagination__item" href="/pagerfanta-view?page=2">2</a>
    <a class="pagination__item" href="/pagerfanta-view?page=3">3</a>
    <a class="pagination__item" href="/pagerfanta-view?page=4">4</a>
    <a class="pagination__item" href="/pagerfanta-view?page=5">5</a>
    <span class="pagination__item pagination__item--separator">&hellip;</span>
    <a class="pagination__item" href="/pagerfanta-view?page=10">10</a>
    <a class="pagination__item pagination__item--next-page" href="/pagerfanta-view?page=2" rel="next">Next</a>
</nav>',
        ];

        yield 'default template at page 1 with translated labels' => [
            1,
            ['omitFirstPage' => false, 'template' => '@BabDevPagerfanta/default.html.twig', 'prev_message' => 'Previous Page', 'next_message' => 'Next Page'],
            '<nav class="pagination">
    <span class="pagination__item pagination__item--previous-page pagination__item--disabled">Previous Page</span>
    <span class="pagination__item pagination__item--current-page" aria-current="page">1</span>
    <a class="pagination__item" href="/pagerfanta-view?page=2">2</a>
    <a class="pagination__item" href="/pagerfanta-view?page=3">3</a>
    <a class="pagination__item" href="/pagerfanta-view?page=4">4</a>
    <a class="pagination__item" href="/pagerfanta-view?page=5">5</a>
    <span class="pagination__item pagination__item--separator">&hellip;</span>
    <a class="pagination__item" href="/pagerfanta-view?page=10">10</a>
    <a class="pagination__item pagination__item--next-page" href="/pagerfanta-view?page=2" rel="next">Next Page</a>
</nav>',
        ];

        yield 'default template at page 5 with first page omitted' => [
            5,
            ['omitFirstPage' => true, 'template' => '@BabDevPagerfanta/default.html.twig'],
            '<nav class="pagination">
    <a class="pagination__item pagination__item--previous-page" href="/pagerfanta-view?page=4" rel="prev">Previous</a>
    <a class="pagination__item" href="/pagerfanta-view">1</a>
    <a class="pagination__item" href="/pagerfanta-view?page=2">2</a>
    <a class="pagination__item" href="/pagerfanta-view?page=3">3</a>
    <a class="pagination__item" href="/pagerfanta-view?page=4">4</a>
    <span class="pagination__item pagination__item--current-page" aria-current="page">5</span>
    <a class="pagination__item" href="/pagerfanta-view?page=6">6</a>
    <a class="pagination__item" href="/pagerfanta-view?page=7">7</a>
    <span class="pagination__item pagination__item--separator">&hellip;</span>
    <a class="pagination__item" href="/pagerfanta-view?page=10">10</a>
    <a class="pagination__item pagination__item--next-page" href="/pagerfanta-view?page=6" rel="next">Next</a>
</nav>',
        ];

        yield 'Semantic UI template at page 1' => [
            1,
            ['omitFirstPage' => false, 'template' => '@BabDevPagerfanta/semantic_ui.html.twig'],
            '<div class="ui pagination menu">
    <div class="disabled item">Previous</div>
    <div class="active item" aria-current="page">1</div>
    <a class="item" href="/pagerfanta-view?page=2">2</a>
    <a class="item" href="/pagerfanta-view?page=3">3</a>
    <a class="item" href="/pagerfanta-view?page=4">4</a>
    <a class="item" href="/pagerfanta-view?page=5">5</a>
    <div class="disabled item">&hellip;</div>
    <a class="item" href="/pagerfanta-view?page=10">10</a>
    <a class="item" href="/pagerfanta-view?page=2" rel="next">Next</a>
</div>',
        ];

        yield 'Semantic UI template at page 5 with first page omitted' => [
            5,
            ['omitFirstPage' => true, 'template' => '@BabDevPagerfanta/semantic_ui.html.twig'],
            '<div class="ui pagination menu">
    <a class="item" href="/pagerfanta-view?page=4" rel="prev">Previous</a>
    <a class="item" href="/pagerfanta-view">1</a>
    <a class="item" href="/pagerfanta-view?page=2">2</a>
    <a class="item" href="/pagerfanta-view?page=3">3</a>
    <a class="item" href="/pagerfanta-view?page=4">4</a>
    <div class="active item" aria-current="page">5</div>
    <a class="item" href="/pagerfanta-view?page=6">6</a>
    <a class="item" href="/pagerfanta-view?page=7">7</a>
    <div class="disabled item">&hellip;</div>
    <a class="item" href="/pagerfanta-view?page=10">10</a>
    <a class="item" href="/pagerfanta-view?page=6" rel="next">Next</a>
</div>',
        ];

        yield 'Twitter Bootstrap template at page 1' => [
            1,
            ['omitFirstPage' => false, 'template' => '@BabDevPagerfanta/twitter_bootstrap.html.twig'],
            '<div class="pagination">
    <ul>
        <li class="disabled"><span>Previous</span></li>
        <li class="active" aria-current="page"><span>1</span></li>
        <li><a href="/pagerfanta-view?page=2">2</a></li>
        <li><a href="/pagerfanta-view?page=3">3</a></li>
        <li><a href="/pagerfanta-view?page=4">4</a></li>
        <li><a href="/pagerfanta-view?page=5">5</a></li>
        <li class="disabled"><span>&hellip;</span></li>
        <li><a href="/pagerfanta-view?page=10">10</a></li>
        <li><a href="/pagerfanta-view?page=2" rel="next">Next</a></li>
    </ul>
</div>',
        ];

        yield 'Twitter Bootstrap template at page 5 with first page omitted' => [
            5,
            ['omitFirstPage' => true, 'template' => '@BabDevPagerfanta/twitter_bootstrap.html.twig'],
            '<div class="pagination">
    <ul>
        <li><a href="/pagerfanta-view?page=4" rel="prev">Previous</a></li>
        <li><a href="/pagerfanta-view">1</a></li>
        <li><a href="/pagerfanta-view?page=2">2</a></li>
        <li><a href="/pagerfanta-view?page=3">3</a></li>
        <li><a href="/pagerfanta-view?page=4">4</a></li>
        <li class="active" aria-current="page"><span>5</span></li>
        <li><a href="/pagerfanta-view?page=6">6</a></li>
        <li><a href="/pagerfanta-view?page=7">7</a></li>
        <li class="disabled"><span>&hellip;</span></li>
        <li><a href="/pagerfanta-view?page=10">10</a></li>
        <li><a href="/pagerfanta-view?page=6" rel="next">Next</a></li>
    </ul>
</div>',
        ];

        yield 'Twitter Bootstrap 3 template at page 1' => [
            1,
            ['omitFirstPage' => false, 'template' => '@BabDevPagerfanta/twitter_bootstrap3.html.twig'],
            '<ul class="pagination">
    <li class="disabled"><span>Previous</span></li>
    <li class="active" aria-current="page"><span>1</span></li>
    <li><a href="/pagerfanta-view?page=2">2</a></li>
    <li><a href="/pagerfanta-view?page=3">3</a></li>
    <li><a href="/pagerfanta-view?page=4">4</a></li>
    <li><a href="/pagerfanta-view?page=5">5</a></li>
    <li class="disabled"><span>&hellip;</span></li>
    <li><a href="/pagerfanta-view?page=10">10</a></li>
    <li><a href="/pagerfanta-view?page=2" rel="next">Next</a></li>
</ul>',
        ];

        yield 'Twitter Bootstrap 3 template at page 5 with first page omitted' => [
            5,
            ['omitFirstPage' => true, 'template' => '@BabDevPagerfanta/twitter_bootstrap3.html.twig'],
            '<ul class="pagination">
    <li><a href="/pagerfanta-view?page=4" rel="prev">Previous</a></li>
    <li><a href="/pagerfanta-view">1</a></li>
    <li><a href="/pagerfanta-view?page=2">2</a></li>
    <li><a href="/pagerfanta-view?page=3">3</a></li>
    <li><a href="/pagerfanta-view?page=4">4</a></li>
    <li class="active" aria-current="page"><span>5</span></li>
    <li><a href="/pagerfanta-view?page=6">6</a></li>
    <li><a href="/pagerfanta-view?page=7">7</a></li>
    <li class="disabled"><span>&hellip;</span></li>
    <li><a href="/pagerfanta-view?page=10">10</a></li>
    <li><a href="/pagerfanta-view?page=6" rel="next">Next</a></li>
</ul>',
        ];

        yield 'Twitter Bootstrap 4 template at page 1' => [
            1,
            ['omitFirstPage' => false, 'template' => '@BabDevPagerfanta/twitter_bootstrap4.html.twig'],
            '<ul class="pagination">
    <li class="page-item disabled"><span class="page-link">Previous</span></li>
    <li class="page-item active" aria-current="page"><span class="page-link">1</span></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=2">2</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=3">3</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=4">4</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=5">5</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=10">10</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=2" rel="next">Next</a></li>
</ul>',
        ];

        yield 'Twitter Bootstrap 4 template at page 5 with first page omitted' => [
            5,
            ['omitFirstPage' => true, 'template' => '@BabDevPagerfanta/twitter_bootstrap4.html.twig'],
            '<ul class="pagination">
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=4" rel="prev">Previous</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view">1</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=2">2</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=3">3</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=4">4</a></li>
    <li class="page-item active" aria-current="page"><span class="page-link">5</span></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=6">6</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=7">7</a></li>
    <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=10">10</a></li>
    <li class="page-item"><a class="page-link" href="/pagerfanta-view?page=6" rel="next">Next</a></li>
</ul>',
        ];

        yield 'Tailwind CSS template at page 1' => [
            1,
            ['omitFirstPage' => false, 'template' => '@BabDevPagerfanta/tailwind.html.twig'],
            '<nav role="navigation" class="flex items-center justify-between">
    <div class="flex-1 flex items-center justify-between">
        <div>
            <span class="relative z-0 inline-flex shadow-sm">
                <span aria-disabled="true">
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-l-md leading-5">Previous</span>
                </span>
                <span aria-current="page">
                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5">1</span>
                </span>
                <a href="/pagerfanta-view?page=2" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">2</a>
                <a href="/pagerfanta-view?page=3" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">3</a>
                <a href="/pagerfanta-view?page=4" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">4</a>
                <a href="/pagerfanta-view?page=5" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">5</a>
                <span aria-disabled="true">
                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5">&hellip;</span>
                </span>
                <a href="/pagerfanta-view?page=10" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">10</a>
                <a href="/pagerfanta-view?page=2" rel="next" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Next</a>
            </span>
        </div>
    </div>
</nav>',
        ];

        yield 'Tailwind CSS template at page 5 with first page omitted' => [
            5,
            ['omitFirstPage' => true, 'template' => '@BabDevPagerfanta/tailwind.html.twig'],
            '<nav role="navigation" class="flex items-center justify-between">
    <div class="flex-1 flex items-center justify-between">
        <div>
            <span class="relative z-0 inline-flex shadow-sm">
                <a href="/pagerfanta-view?page=4" rel="prev" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Previous</a>
                <a href="/pagerfanta-view" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">1</a>
                <a href="/pagerfanta-view?page=2" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">2</a>
                <a href="/pagerfanta-view?page=3" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">3</a>
                <a href="/pagerfanta-view?page=4" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">4</a>
                <span aria-current="page">
                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default leading-5">5</span>
                </span>
                <a href="/pagerfanta-view?page=6" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">6</a>
                <a href="/pagerfanta-view?page=7" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">7</a>
                <span aria-disabled="true">
                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default leading-5">&hellip;</span>
                </span>
                <a href="/pagerfanta-view?page=10" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">10</a>
                <a href="/pagerfanta-view?page=6" rel="next" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md leading-5 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">Next</a>
            </span>
        </div>
    </div>
</nav>',
        ];

        yield 'Foundation 6 template at page 1' => [
            1,
            ['omitFirstPage' => false, 'template' => '@BabDevPagerfanta/foundation6.html.twig'],
            '<nav aria-label="Pagination">
    <ul class="pagination">
    <li class="pagination-previous disabled">Previous</li>
    <li class="current" aria-current="page">1</li>
    <li><a href="/pagerfanta-view?page=2">2</a></li>
    <li><a href="/pagerfanta-view?page=3">3</a></li>
    <li><a href="/pagerfanta-view?page=4">4</a></li>
    <li><a href="/pagerfanta-view?page=5">5</a></li>
    <li class="ellipsis" aria-hidden="true"></li>
    <li><a href="/pagerfanta-view?page=10">10</a></li>
    <li class="pagination-next"><a href="/pagerfanta-view?page=2" rel="next">Next</a></li>
    </ul>
</nav>',
        ];

        yield 'Foundation 6 template at page 5 with first page omitted' => [
            5,
            ['omitFirstPage' => true, 'template' => '@BabDevPagerfanta/foundation6.html.twig'],
            '<nav aria-label="Pagination">
    <ul class="pagination">
    <li class="pagination-previous"><a href="/pagerfanta-view?page=4" rel="prev">Previous</a></li>
    <li><a href="/pagerfanta-view">1</a></li>
    <li><a href="/pagerfanta-view?page=2">2</a></li>
    <li><a href="/pagerfanta-view?page=3">3</a></li>
    <li><a href="/pagerfanta-view?page=4">4</a></li>
    <li class="current" aria-current="page">5</li>
    <li><a href="/pagerfanta-view?page=6">6</a></li>
    <li><a href="/pagerfanta-view?page=7">7</a></li>
    <li class="ellipsis" aria-hidden="true"></li>
    <li><a href="/pagerfanta-view?page=10">10</a></li>
    <li class="pagination-next"><a href="/pagerfanta-view?page=6" rel="next">Next</a></li>
    </ul>
</nav>',
        ];
    }

    /**
     * @dataProvider dataPagerfantaRenderer
     */
    public function testPagerfantaRendering(int $page, array $options, string $testOutput): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $pagerfanta = $this->createPagerfanta();
        $pagerfanta->setCurrentPage($page);

        $this->assertViewOutputMatches(
            $this->twig->render('integration.html.twig', ['pager' => $pagerfanta, 'options' => $options]),
            $testOutput
        );
    }

    public function testPagerfantaRenderingWithEmptyOptions(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        self::assertNotEmpty(
            (new TwigView($this->twig))->render(
                $this->createPagerfanta(),
                (new RequestAwareRouteGeneratorFactory($this->router, $this->requestStack, $this->propertyAccessor))->create()
            )
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
            /**
             * @var TwigViewIntegrationTest
             */
            private $testCase;

            public function __construct(TwigViewIntegrationTest $testCase)
            {
                $this->testCase = $testCase;
            }

            /**
             * @param string $class
             *
             * @return object|null
             */
            public function load($class)
            {
                switch ($class) {
                    case PagerfantaRuntime::class:
                        $viewFactory = new ViewFactory();
                        $viewFactory->set('twig', new TwigView($this->testCase->twig));

                        $routeGeneratorFactory = new RequestAwareRouteGeneratorFactory(
                            $this->testCase->router,
                            $this->testCase->requestStack,
                            $this->testCase->propertyAccessor
                        );

                        return new PagerfantaRuntime(
                            'twig',
                            $viewFactory,
                            $routeGeneratorFactory
                        );

                    default:
                        return null;
                }
            }
        };
    }

    private function createTranslator(): Translator
    {
        $translator = new Translator('en');
        $translator->addLoader('xliff', new XliffFileLoader());
        $translator->addResource('xliff', __DIR__.'/../../translations/pagerfanta.en.xliff', 'en', 'pagerfanta');

        return $translator;
    }

    private function assertViewOutputMatches(string $view, string $expected): void
    {
        self::assertSame($this->removeWhitespacesBetweenTags($expected), $view);
    }

    private function removeWhitespacesBetweenTags(string $string): string
    {
        return preg_replace('/>\s+</', '><', $string);
    }
}
