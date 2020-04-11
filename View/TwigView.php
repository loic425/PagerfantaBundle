<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use Twig\Environment;

final class TwigView implements ViewInterface
{
    public const DEFAULT_TEMPLATE = '@BabDevPagerfantaBundle/default.html.twig';

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string|null
     */
    private $defaultTemplate;

    public function __construct(Environment $twig, ?string $defaultTemplate = null)
    {
        $this->twig = $twig;
        $this->defaultTemplate = $defaultTemplate;
    }

    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = [])
    {
        if (isset($options['template'])) {
            $template = $options['template'];
        } elseif ($this->defaultTemplate !== null) {
            $template = $this->defaultTemplate;
        } else {
            $template = self::DEFAULT_TEMPLATE;
        }

        return $this->twig->render(
            $template,
            [
                'pagerfanta' => $pagerfanta,
                'route_generator' => $routeGenerator,
                'options' => $options,
            ]
        );
    }

    public function getName()
    {
        return 'twig';
    }
}
