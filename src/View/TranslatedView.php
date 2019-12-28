<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\View;

use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class TranslatedView implements ViewInterface
{
    /**
     * @var ViewInterface
     */
    private $view;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(ViewInterface $view, TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->translator = $translator;
    }

    public function render(PagerfantaInterface $pagerfanta, $routeGenerator, array $options = [])
    {
        $optionsWithTranslations = $this->addTranslationOptions($options);

        return $this->view->render($pagerfanta, $routeGenerator, $optionsWithTranslations);
    }

    private function addTranslationOptions($options)
    {
        return $this->addNextTranslationOption(
            $this->addPreviousTranslationOption($options)
        );
    }

    private function addPreviousTranslationOption($options)
    {
        return $this->addTranslationOption($options, $this->previousMessageOption(), 'previousMessage');
    }

    private function addNextTranslationOption($options)
    {
        return $this->addTranslationOption($options, $this->nextMessageOption(), 'nextMessage');
    }

    private function addTranslationOption($options, $option, $messageMethod)
    {
        if (isset($options[$option])) {
            return $options;
        }

        $message = $this->$messageMethod();

        return array_merge($options, [$option => $message]);
    }

    abstract protected function previousMessageOption();

    abstract protected function nextMessageOption();

    private function previousMessage()
    {
        $previousText = $this->previousText();

        return $this->buildPreviousMessage($previousText);
    }

    private function nextMessage()
    {
        $nextText = $this->nextText();

        return $this->buildNextMessage($nextText);
    }

    private function previousText()
    {
        return $this->translator->trans('previous', [], 'pagerfanta');
    }

    private function nextText()
    {
        return $this->translator->trans('next', [], 'pagerfanta');
    }

    abstract protected function buildPreviousMessage($text);

    abstract protected function buildNextMessage($text);
}
