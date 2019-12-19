<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\View;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class TranslatedViewTestCase extends TestCase
{
    /**
     * @var MockObject|ViewInterface
     */
    private $view;

    /**
     * @var MockObject|LegacyTranslatorInterface|TranslatorInterface
     */
    private $translator;

    /**
     * @var ViewInterface
     */
    private $translatedView;

    /**
     * @var MockObject|PagerfantaInterface
     */
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

    abstract protected function decoratedViewClass(): string;

    abstract protected function translatedViewClass(): string;

    abstract protected function previousMessageOption(): string;

    abstract protected function nextMessageOption(): string;

    abstract protected function buildPreviousMessage(string $text): string;

    abstract protected function buildNextMessage(string $text): string;

    abstract protected function translatedViewName(): string;

    public function testRenderShouldTranslatePreviuosAndNextMessage(): void
    {
        $this->translatorExpectsPreviousAt(0);
        $this->translatorExpectsNextAt(1);

        $this->assertRender([]);
    }

    public function testRenderAllowsCustomizingPreviousMessageWithOption(): void
    {
        $this->translatorExpectsNextAt(0);

        $previousMessageOption = $this->previousMessageOption();

        $this->assertRender([$previousMessageOption => $this->previousMessage()]);
    }

    public function testRenderAllowsCustomizingNextMessageWithOption(): void
    {
        $this->translatorExpectsPreviousAt(0);

        $nextMessageOption = $this->nextMessageOption();

        $this->assertRender([$nextMessageOption => $this->nextMessage()]);
    }

    public function testGetNameShouldReturnTheName(): void
    {
        $this->assertSame($this->translatedViewName(), $this->translatedView->getName());
    }

    /**
     * @return MockObject|ViewInterface
     */
    private function createViewMock(): MockObject
    {
        return $this->createMock($this->decoratedViewClass());
    }

    /**
     * @return MockObject|LegacyTranslatorInterface|TranslatorInterface
     */
    private function createTranslatorMock(): MockObject
    {
        $translator = interface_exists(TranslatorInterface::class) ? TranslatorInterface::class : LegacyTranslatorInterface::class;

        return $this->createMock($translator);
    }

    private function createTranslatedView(): ViewInterface
    {
        $class = $this->translatedViewClass();

        return new $class($this->view, $this->translator);
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
        return function (): void { };
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

        $this->view->expects($this->once())
            ->method('render')
            ->with($this->pagerfanta, $this->routeGenerator, $expectedOptions)
            ->willReturn($result);

        $this->assertSame(
            $result,
            $this->translatedView->render($this->pagerfanta, $this->routeGenerator, $options)
        );
    }

    private function previous(): string
    {
        return 'Anterior';
    }

    private function next(): string
    {
        return 'Siguiente';
    }

    private function previousMessage(): string
    {
        return $this->buildPreviousMessage($this->previous());
    }

    private function nextMessage(): string
    {
        return $this->buildNextMessage($this->next());
    }
}
