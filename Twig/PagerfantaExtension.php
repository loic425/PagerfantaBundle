<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Twig;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PagerfantaExtension extends AbstractExtension
{
    /**
     * @var string
     */
    private $defaultView;

    /**
     * @var ViewFactory
     */
    private $viewFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(string $defaultView, ViewFactory $viewFactory, UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->defaultView = $defaultView;
        $this->viewFactory = $viewFactory;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('pagerfanta', [$this, 'renderPagerfanta'], ['is_safe' => ['html']]),
            new TwigFunction('pagerfanta_page_url', [$this, 'getPageUrl']),
        ];
    }

    /**
     * @param string|array $viewName the view name
     *
     * @return string
     */
    public function renderPagerfanta(PagerfantaInterface $pagerfanta, $viewName = null, array $options = [])
    {
        if (\is_array($viewName)) {
            [$viewName, $options] = [null, $viewName];
        }

        $viewName = $viewName ?: $this->defaultView;

        $routeGenerator = $this->createRouteGenerator($options);

        return $this->viewFactory->get($viewName)->render($pagerfanta, $routeGenerator, $options);
    }

    /**
     * @throws \InvalidArgumentException if the page is out of bounds
     */
    public function getPageUrl(PagerfantaInterface $pagerfanta, int $page, array $options = [])
    {
        if ($page < 0 || $page > $pagerfanta->getNbPages()) {
            throw new \InvalidArgumentException("Page '{$page}' is out of bounds");
        }

        $routeGenerator = $this->createRouteGenerator($options);

        return $routeGenerator($page);
    }

    /**
     * @throws \RuntimeException if attempting to guess a route name during a sub-request
     */
    private function createRouteGenerator(array $options = []): callable
    {
        $options = array_replace(
            [
                'routeName' => null,
                'routeParams' => [],
                'pageParameter' => '[page]',
                'omitFirstPage' => false,
            ],
            $options
        );

        $router = $this->router;

        if (null === $options['routeName']) {
            $request = $this->getRequest();

            $options['routeName'] = $request->attributes->get('_route');

            if ('_internal' === $options['routeName']) {
                throw new \RuntimeException('PagerfantaBundle can not guess the route when used in a sub-request');
            }

            // make sure we read the route parameters from the passed option array
            $defaultRouteParams = array_merge($request->query->all(), $request->attributes->get('_route_params', []));

            if (\array_key_exists('routeParams', $options)) {
                $options['routeParams'] = array_merge($defaultRouteParams, $options['routeParams']);
            } else {
                $options['routeParams'] = $defaultRouteParams;
            }
        }

        $routeName = $options['routeName'];
        $routeParams = $options['routeParams'];
        $pagePropertyPath = new PropertyPath($options['pageParameter']);
        $omitFirstPage = $options['omitFirstPage'];

        return function ($page) use ($router, $routeName, $routeParams, $pagePropertyPath, $omitFirstPage) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            if ($omitFirstPage) {
                $propertyAccessor->setValue($routeParams, $pagePropertyPath, $page > 1 ? $page : null);
            } else {
                $propertyAccessor->setValue($routeParams, $pagePropertyPath, $page);
            }

            return $router->generate($routeName, $routeParams);
        };
    }

    private function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}
