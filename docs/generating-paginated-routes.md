# Generating Paginated Routes

When rendering a Pagerfanta view, a route generator callable is required to generate the URLs for each item in the pagination list. As of BabDevPagerfantaBundle 2.2, the route generator can be customized for use within your application if you need to adjust the routing logic.

The route generators are defined by two interfaces, with their default implementations noted below:

- `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorInterface` - The class type that is used to generate routes
    - `BabDev\PagerfantaBundle\RouteGenerator\RouterAwareRouteGenerator` is used by default, which uses the Symfony Routing component to generate routes
- `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorFactoryInterface` - A factory service that is used to generate a `RouteGeneratorInterface` at runtime
    - `BabDev\PagerfantaBundle\RouteGenerator\RequestAwareRouteGeneratorFactory` is used by default, which uses the `Symfony\Component\HttpFoundation\Request` object to attempt to set the default route name and route parameters, this creates a `RouterAwareRouteGenerator`

The Twig integration uses a `RouteGeneratorFactoryInterface` instance to create the route generator used when rendering a Pagerfanta view.

The `pagerfanta.route_generator_factory` service is available for use in your application if you need to create a route generator. You may use a compiler pass to change this service to any class meeting the interface requirements.
