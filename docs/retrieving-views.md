# Retrieving Views

You can access the Pagerfanta views through the `pagerfanta.view_factory` service, which is a `Pagerfanta\View\ViewFactoryInterface` instance. This is useful if your application does not use Twig but you still want to use Pagerfanta views for rendering pagination lists.

```php
<?php

namespace App\Service;

use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewFactoryInterface;

final class PagerfantaService
{
    private ViewFactoryInterface $viewFactory;

    public function __construct(ViewFactoryInterface $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    public function render(Pagerfanta $pagerfanta, string $view, array $options = []): string
    {
        return $this->viewFactory->get($view)->render($pagerfanta, $this->createRouteGenerator($options), $options);
    }
}
```
