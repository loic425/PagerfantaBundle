<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Twig;

use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PagerfantaRuntime
{
    /**
     * @var string
     */
    private $defaultView;

    /**
     * @var ViewFactoryInterface
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

    public function __construct(string $defaultView, ViewFactoryInterface $viewFactory, UrlGeneratorInterface $router, RequestStack $requestStack)
    {
        $this->defaultView = $defaultView;
        $this->viewFactory = $viewFactory;
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string|array|null $viewName the view name
     *
     * @return string
     *
     * @throws \InvalidArgumentException if the $viewName argument is an invalid type
     */
    public function renderPagerfanta(PagerfantaInterface $pagerfanta, $viewName = null, array $options = [])
    {
        if (\is_array($viewName)) {
            [$viewName, $options] = [null, $viewName];
        } elseif (null !== $viewName && !\is_string($viewName)) {
            throw new \InvalidArgumentException(sprintf('The $viewName argument of %s() must be an array, a string, or a null value; a %s was given.', __METHOD__, \gettype($viewName)));
        }

        if (!($pagerfanta instanceof Pagerfanta)) {
            @trigger_error(
                sprintf(
                    'The "pagerfanta" Twig function will no longer accept "%1$s" implementations that are not a subclass of "%2$s" as of BabDevPagerfantaBundle 3.0. Ensure your pager is a subclass of "%2$s".',
                    PagerfantaInterface::class,
                    Pagerfanta::class
                ),
                E_USER_DEPRECATED
            );
        }

        $viewName = $viewName ?: $this->defaultView;

        return $this->viewFactory->get($viewName)->render($pagerfanta, $this->createRouteGenerator($options), $options);
    }

    /**
     * @throws OutOfRangeCurrentPageException if the page is out of bounds
     */
    public function getPageUrl(PagerfantaInterface $pagerfanta, int $page, array $options = [])
    {
        if (!($pagerfanta instanceof Pagerfanta)) {
            @trigger_error(
                sprintf(
                    'The "pagerfanta_page_url" Twig function will no longer accept "%1$s" implementations that are not a subclass of "%2$s" as of BabDevPagerfantaBundle 3.0. Ensure your pager is a subclass of "%2$s".',
                    PagerfantaInterface::class,
                    Pagerfanta::class
                ),
                E_USER_DEPRECATED
            );
        }

        if ($page < 0 || $page > $pagerfanta->getNbPages()) {
            throw new OutOfRangeCurrentPageException("Page '{$page}' is out of bounds");
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

        if (null === $options['routeName']) {
            $request = $this->getRequest();

            if (null !== $this->requestStack->getParentRequest()) {
                throw new \RuntimeException('The Twig functions of BabDevPagerfantaBundle can not guess the route when used in a sub-request, pass the "routeName" option to use the pager.');
            }

            $options['routeName'] = $request->attributes->get('_route');

            // Make sure we read the route parameters from the passed option array
            $defaultRouteParams = array_merge($request->query->all(), $request->attributes->get('_route_params', []));

            $options['routeParams'] = array_merge($defaultRouteParams, $options['routeParams']);
        }

        return function ($page) use ($options) {
            $pagePropertyPath = new PropertyPath($options['pageParameter']);
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            if ($options['omitFirstPage']) {
                $propertyAccessor->setValue($options['routeParams'], $pagePropertyPath, $page > 1 ? $page : null);
            } else {
                $propertyAccessor->setValue($options['routeParams'], $pagePropertyPath, $page);
            }

            return $this->router->generate($options['routeName'], $options['routeParams']);
        };
    }

    private function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}
