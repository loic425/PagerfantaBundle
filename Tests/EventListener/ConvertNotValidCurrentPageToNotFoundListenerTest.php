<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\DependencyInjection;

use BabDev\PagerfantaBundle\EventListener\ConvertNotValidCurrentPageToNotFoundListener;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ConvertNotValidCurrentPageToNotFoundListenerTest extends TestCase
{
    public function testListenerConvertsExceptionForLegacyEvent(): void
    {
        if (!class_exists(GetResponseForExceptionEvent::class)) {
            $this->markTestSkipped(sprintf('Test only applies to legacy "%s".', GetResponseForExceptionEvent::class));
        }

        $exception = new NotValidCurrentPageException();

        $event = new GetResponseForExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        (new ConvertNotValidCurrentPageToNotFoundListener())->onKernelException($event);

        $getter = method_exists($event, 'getThrowable') ? 'getThrowable' : 'getException';

        /** @var \Throwable $eventException */
        $eventException = $event->$getter();

        $this->assertInstanceOf(NotFoundHttpException::class, $eventException);
        $this->assertSame($exception, $eventException->getPrevious());
    }

    public function testListenerDoesNotConvertUnknownExceptionForLegacyEvent(): void
    {
        if (!class_exists(GetResponseForExceptionEvent::class)) {
            $this->markTestSkipped(sprintf('Test only applies to legacy "%s".', GetResponseForExceptionEvent::class));
        }

        $exception = new \RuntimeException();

        $event = new GetResponseForExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        (new ConvertNotValidCurrentPageToNotFoundListener())->onKernelException($event);

        $getter = method_exists($event, 'getThrowable') ? 'getThrowable' : 'getException';

        /** @var \Throwable $eventException */
        $eventException = $event->$getter();

        $this->assertSame($exception, $eventException);
    }

    public function testListenerConvertsExceptionForEvent(): void
    {
        if (!class_exists(ExceptionEvent::class)) {
            $this->markTestSkipped(sprintf('Test only applies to "%s".', ExceptionEvent::class));
        }

        $exception = new NotValidCurrentPageException();

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        (new ConvertNotValidCurrentPageToNotFoundListener())->onKernelException($event);

        $getter = method_exists($event, 'getThrowable') ? 'getThrowable' : 'getException';

        /** @var \Throwable $eventException */
        $eventException = $event->$getter();

        $this->assertInstanceOf(NotFoundHttpException::class, $eventException);
        $this->assertSame($exception, $eventException->getPrevious());
    }

    public function testListenerDoesNotConvertUnknownExceptionForEvent(): void
    {
        if (!class_exists(ExceptionEvent::class)) {
            $this->markTestSkipped(sprintf('Test only applies to "%s".', ExceptionEvent::class));
        }

        $exception = new \RuntimeException();

        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $this->createMock(Request::class),
            HttpKernelInterface::MASTER_REQUEST,
            $exception
        );

        (new ConvertNotValidCurrentPageToNotFoundListener())->onKernelException($event);

        $getter = method_exists($event, 'getThrowable') ? 'getThrowable' : 'getException';

        /** @var \Throwable $eventException */
        $eventException = $event->$getter();

        $this->assertSame($exception, $eventException);
    }

    public function testListenerRaisesAnErrorIfAnIncorrectObjectTypeIsGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new ConvertNotValidCurrentPageToNotFoundListener())->onKernelException(new GenericEvent());
    }
}
