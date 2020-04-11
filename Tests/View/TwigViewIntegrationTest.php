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

    public function testTheDefaultPagerfantaViewIsRendered(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $this->assertViewOutputMatches(
            $this->twig->render('integration.html.twig', ['pager' => $this->createPagerfanta(), 'options' => []]),
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
</nav>
'
        );
    }

    public function testTheDefaultPagerfantaViewIsRenderedFromALaterPageWithFirstPageOmitted(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'pagerfanta_view');
        $request->attributes->set('_route_params', []);

        $this->requestStack->push($request);

        $pagerfanta = $this->createPagerfanta();
        $pagerfanta->setCurrentPage(5);

        $this->assertViewOutputMatches(
            $this->twig->render('integration.html.twig', ['pager' => $pagerfanta, 'options' => ['omitFirstPage' => true]]),
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
</nav>
'
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
