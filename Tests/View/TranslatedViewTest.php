<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class TranslatedViewTest extends TestCase
{
    private $view;
    private $translator;

    private $translatedView;

    private $pagerfanta;
    private $routeGenerator;

    protected function setUp(): void
    {
        $this->view = $this->createViewMock();
        $this->translator = $this->createTranslatorMock();

        $this->translatedView = $this->createTranslatedView();

        $this->pagerfanta = $this->createPagerfantaMock();
        $this->routeGenerator = $this->createRouteGenerator();
    }

    protected function createOrGetMock($originalClassName)
    {
        if (method_exists($this, 'createMock')) {
            return $this->createMock($originalClassName);
        }

        return $this->createMock($originalClassName);
    }

    private function createViewMock()
    {
        return $this->createOrGetMock($this->viewClass());
    }

    abstract protected function viewClass();

    private function createTranslatorMock()
    {
        $translator = interface_exists(TranslatorInterface::class) ? TranslatorInterface::class : LegacyTranslatorInterface::class;

        return $this->createOrGetMock($translator);
    }

    private function createTranslatedView()
    {
        $class = $this->translatedViewClass();

        return new $class($this->view, $this->translator);
    }

    abstract protected function translatedViewClass();

    private function createPagerfantaMock()
    {
        return $this->getMockBuilder('Pagerfanta\Pagerfanta')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function createRouteGenerator()
    {
        return function (): void { };
    }

    public function testRenderShouldTranslatePreviuosAndNextMessage(): void
    {
        $this->translatorExpectsPreviousAt(0);
        $this->translatorExpectsNextAt(1);

        $options = [];

        $this->assertRender($options);
    }

    public function testRenderAllowsCustomizingPreviousMessageWithOption(): void
    {
        $this->translatorExpectsNextAt(0);

        $previousMessageOption = $this->previousMessageOption();
        $options = [$previousMessageOption => $this->previousMessage()];

        $this->assertRender($options);
    }

    public function testRenderAllowsCustomizingNextMessageWithOption(): void
    {
        $this->translatorExpectsPreviousAt(0);

        $nextMessageOption = $this->nextMessageOption();
        $options = [$nextMessageOption => $this->nextMessage()];

        $this->assertRender($options);
    }

    private function translatorExpectsPreviousAt($at): void
    {
        $previous = $this->previous();

        $this->translator
            ->expects($this->at($at))
            ->method('trans')
            ->with('previous', [], 'pagerfanta')
            ->willReturn($previous);
    }

    private function translatorExpectsNextAt($at): void
    {
        $next = $this->next();

        $this->translator
            ->expects($this->at($at))
            ->method('trans')
            ->with('next', [], 'pagerfanta')
            ->willReturn($next);
    }

    private function assertRender($options): void
    {
        $previousMessageOption = $this->previousMessageOption();
        $nextMessageOption = $this->nextMessageOption();

        $previous = $this->previous();
        $next = $this->next();

        $expectedOptions = [
            $previousMessageOption => $this->buildPreviousMessage($previous),
            $nextMessageOption => $this->buildNextMessage($next),
        ];

        $result = new \stdClass();

        $this->view
            ->expects($this->once())
            ->method('render')
            ->with($this->pagerfanta, $this->routeGenerator, $expectedOptions)
            ->willReturn($result);

        $rendered = $this->translatedView->render($this->pagerfanta, $this->routeGenerator, $options);

        $this->assertSame($result, $rendered);
    }

    abstract protected function previousMessageOption();

    abstract protected function nextMessageOption();

    private function previous()
    {
        return 'Anterior';
    }

    private function next()
    {
        return 'Siguiente';
    }

    private function previousMessage()
    {
        return $this->buildPreviousMessage($this->previous());
    }

    private function nextMessage()
    {
        return $this->buildNextMessage($this->next());
    }

    abstract protected function buildPreviousMessage($text);

    abstract protected function buildNextMessage($text);

    public function testGetNameShouldReturnTheName(): void
    {
        $name = $this->translatedViewName();

        $this->assertSame($name, $this->translatedView->getName());
    }

    abstract protected function translatedViewName();
}
