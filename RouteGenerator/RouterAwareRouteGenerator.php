<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\RouteGenerator;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RouterAwareRouteGenerator implements RouteGeneratorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var array
     */
    private $options;

    public function __construct(UrlGeneratorInterface $router, array $options)
    {
        $this->router = $router;
        $this->options = $this->resolveOptions($options);
    }

    public function __invoke(int $page): string
    {
        $pagePropertyPath = new PropertyPath($this->options['pageParameter']);
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        if ($this->options['omitFirstPage']) {
            $propertyAccessor->setValue($this->options['routeParams'], $pagePropertyPath, $page > 1 ? $page : null);
        } else {
            $propertyAccessor->setValue($this->options['routeParams'], $pagePropertyPath, $page);
        }

        return $this->router->generate($this->options['routeName'], $this->options['routeParams']);
    }

    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(
            [
                'routeName',
            ]
        );

        $resolver->setDefaults(
            [
                'routeParams' => [],
                'pageParameter' => '[page]',
                'omitFirstPage' => false,
            ]
        );

        $resolver->setAllowedTypes('routeName', 'string');
        $resolver->setAllowedTypes('routeParams', 'array');
        $resolver->setAllowedTypes('pageParameter', 'string');
        $resolver->setAllowedTypes('omitFirstPage', 'boolean');

        return $resolver->resolve($options);
    }
}
