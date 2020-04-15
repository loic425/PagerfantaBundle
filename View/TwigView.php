<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

use BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorDecorator;
use Pagerfanta\Exception\InvalidArgumentException;
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

    /**
     * @var string
     */
    private $template;

    /**
     * @var PagerfantaInterface
     */
    private $pagerfanta;

    /**
     * @var int
     */
    private $proximity;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $nbPages;

    /**
     * @var int
     */
    private $startPage;

    /**
     * @var int
     */
    private $endPage;

    public function __construct(Environment $twig, ?string $defaultTemplate = null)
    {
        $this->twig = $twig;
        $this->defaultTemplate = $defaultTemplate;
    }

    public function getName()
    {
        return 'twig';
    }

    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = [])
    {
        $this->initializePagerfanta($pagerfanta);
        $this->initializeOptions($options);

        $this->calculateStartAndEndPage();

        $template = $this->twig->load($this->template);
        return $template->renderBlock(
            'pager_widget',
            [
                'pagerfanta' => $pagerfanta,
                'route_generator' => $this->decorateRouteGenerator($routeGenerator),
                'options' => $options,
                'start_page' => $this->startPage,
                'end_page' => $this->endPage,
                'current_page' => $this->currentPage,
                'nb_pages' => $this->nbPages,
            ]
        );
    }

    private function decorateRouteGenerator($routeGenerator): RouteGeneratorDecorator
    {
        if (!\is_callable($routeGenerator)) {
            throw new InvalidArgumentException(sprintf('The route generator for "%s" must be a callable, a "%s" was given.', self::class, \gettype($routeGenerator)));
        }

        return new RouteGeneratorDecorator($routeGenerator);
    }

    private function initializePagerfanta(PagerfantaInterface $pagerfanta): void
    {
        $this->pagerfanta = $pagerfanta;

        $this->currentPage = $pagerfanta->getCurrentPage();
        $this->nbPages = $pagerfanta->getNbPages();
    }

    private function initializeOptions(array $options): void
    {
        if (isset($options['template'])) {
            $this->template = $options['template'];
        } elseif (null !== $this->defaultTemplate) {
            $this->template = $this->defaultTemplate;
        } else {
            $this->template = self::DEFAULT_TEMPLATE;
        }

        $this->proximity = isset($options['proximity']) ? (int) $options['proximity'] : 2;
    }

    private function calculateStartAndEndPage(): void
    {
        $startPage = $this->currentPage - $this->proximity;
        $endPage = $this->currentPage + $this->proximity;

        if ($this->startPageUnderflow($startPage)) {
            $endPage = $this->calculateEndPageForStartPageUnderflow($startPage, $endPage);
            $startPage = 1;
        }

        if ($this->endPageOverflow($endPage)) {
            $startPage = $this->calculateStartPageForEndPageOverflow($startPage, $endPage);
            $endPage = $this->nbPages;
        }

        $this->startPage = $startPage;
        $this->endPage = $endPage;
    }

    private function startPageUnderflow(int $startPage): bool
    {
        return $startPage < 1;
    }

    private function endPageOverflow(int $endPage): bool
    {
        return $endPage > $this->nbPages;
    }

    private function calculateEndPageForStartPageUnderflow(int $startPage, int $endPage): int
    {
        return min($endPage + (1 - $startPage), $this->nbPages);
    }

    private function calculateStartPageForEndPageOverflow(int $startPage, int $endPage): int
    {
        return max($startPage - ($endPage - $this->nbPages), 1);
    }
}
