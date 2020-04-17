<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

@trigger_error(sprintf('The "%s" class is deprecated and will be removed in BabDevPagerfantaBundle 3.0. Use the "%s" class instead.', TranslatedView::class, TwigView::class));

/**
 * @deprecated to be removed in BabDevPagerfantaBundle 3.0. Use the Twig view class instead.
 */
abstract class TranslatedView implements ViewInterface
{
    private ViewInterface $view;
    private TranslatorInterface $translator;

    public function __construct(ViewInterface $view, TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->translator = $translator;
    }

    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = []): string
    {
        return $this->view->render($pagerfanta, $routeGenerator, $this->addTranslationOptions($options));
    }

    private function addTranslationOptions(array $options): array
    {
        return $this->addNextTranslationOption(
            $this->addPreviousTranslationOption($options)
        );
    }

    private function addPreviousTranslationOption(array $options): array
    {
        return $this->addTranslationOption($options, $this->previousMessageOption(), 'previousMessage');
    }

    private function addNextTranslationOption(array $options): array
    {
        return $this->addTranslationOption($options, $this->nextMessageOption(), 'nextMessage');
    }

    private function addTranslationOption(array $options, string $option, string $messageMethod): array
    {
        if (isset($options[$option])) {
            return $options;
        }

        $message = $this->$messageMethod();

        return array_merge($options, [$option => $message]);
    }

    abstract protected function previousMessageOption(): string;

    abstract protected function nextMessageOption(): string;

    private function previousMessage(): string
    {
        $previousText = $this->previousText();

        return $this->buildPreviousMessage($previousText);
    }

    private function nextMessage(): string
    {
        $nextText = $this->nextText();

        return $this->buildNextMessage($nextText);
    }

    private function previousText(): string
    {
        return $this->translator->trans('previous', [], 'pagerfanta');
    }

    private function nextText(): string
    {
        return $this->translator->trans('next', [], 'pagerfanta');
    }

    abstract protected function buildPreviousMessage(string $text): string;

    abstract protected function buildNextMessage(string $text): string;
}
