<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use BabDev\PagerfantaBundle\View\TwigView;
use Pagerfanta\PagerfantaInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class TwigViewTest extends TestCase
{
    /**
     * @var MockObject|Environment
     */
    private $twig;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
    }

    public function testRendersWithATemplateSpecifiedInTheOptions(): void
    {
        $pagerfanta = $this->createPagerfantaMock();
        $routeGenerator = $this->createRouteGenerator();
        $options = ['template' => 'test.html.twig'];

        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                $options['template'],
                [
                    'pagerfanta' => $pagerfanta,
                    'route_generator' => $routeGenerator,
                    'options' => $options,
                ]
            )
            ->willReturn('Twig template');

        $this->assertSame('Twig template', (new TwigView($this->twig, 'constructor.html.twig'))->render($pagerfanta, $routeGenerator, $options));
    }

    public function testRendersWithATemplateSpecifiedInTheConstructorWhenNotSetInTheOptions(): void
    {
        $pagerfanta = $this->createPagerfantaMock();
        $routeGenerator = $this->createRouteGenerator();
        $options = [];

        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                'constructor.html.twig',
                [
                    'pagerfanta' => $pagerfanta,
                    'route_generator' => $routeGenerator,
                    'options' => $options,
                ]
            )
            ->willReturn('Twig template');

        $this->assertSame('Twig template', (new TwigView($this->twig, 'constructor.html.twig'))->render($pagerfanta, $routeGenerator, $options));
    }

    public function testRendersWithTheDefaultTemplateWhenNotSetInConstructorOrOptions(): void
    {
        $pagerfanta = $this->createPagerfantaMock();
        $routeGenerator = $this->createRouteGenerator();
        $options = [];

        $this->twig->expects($this->once())
            ->method('render')
            ->with(
                TwigView::DEFAULT_TEMPLATE,
                [
                    'pagerfanta' => $pagerfanta,
                    'route_generator' => $routeGenerator,
                    'options' => $options,
                ]
            )
            ->willReturn('Twig template');

        $this->assertSame('Twig template', (new TwigView($this->twig))->render($pagerfanta, $routeGenerator, $options));
    }

    /**
     * @return MockObject|PagerfantaInterface
     */
    private function createPagerfantaMock(): MockObject
    {
        return $this->createMock(PagerfantaInterface::class);
    }

    private function createRouteGenerator(): callable
    {
        return static function (): void { };
    }
}
