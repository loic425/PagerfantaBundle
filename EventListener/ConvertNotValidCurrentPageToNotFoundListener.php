<?php
namespace  BabDev\PagerfantaBundle\EventListener;

use Pagerfanta\Exception\NotValidCurrentPageException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConvertNotValidCurrentPageToNotFoundListener
{
    /**
     * @param GetResponseForExceptionEvent|ExceptionEvent $event
     */
    public function onKernelException(object $event)
    {
        if (!($event instanceof GetResponseForExceptionEvent) && !($event instanceof ExceptionEvent)) {
            throw new \InvalidArgumentException(sprintf('The $event argument of %s() must be an instance of %s or %s, a %s was given.', __METHOD__, GetResponseForExceptionEvent::class, ExceptionEvent::class, get_class($translator)));
        }

        if (method_exists($event, 'getThrowable')) {
            $throwable = $event->getThrowable();
        } else {
            // Support for Symfony 4.3 and before
            $throwable = $event->getException();
        }

        if ($throwable instanceof NotValidCurrentPageException) {
            $notFoundHttpException = new NotFoundHttpException('Page Not Found', $throwable);

            if (method_exists($event, 'setThrowable')) {
                $event->setThrowable($notFoundHttpException);
            } else {
                // Support for Symfony 4.3 and before
                $event->setException($notFoundHttpException);
            }
        }
    }
}
