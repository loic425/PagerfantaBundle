# BabDevPagerfantaBundle

[![Latest Stable Version](https://poser.pugx.org/babdev/pagerfanta-bundle/v/stable)](https://packagist.org/packages/babdev/pagerfanta-bundle) [![Latest Unstable Version](https://poser.pugx.org/babdev/pagerfanta-bundle/v/unstable)](https://packagist.org/packages/babdev/pagerfanta-bundle) [![Total Downloads](https://poser.pugx.org/babdev/pagerfanta-bundle/downloads)](https://packagist.org/packages/babdev/pagerfanta-bundle) [![License](https://poser.pugx.org/babdev/pagerfanta-bundle/license)](https://packagist.org/packages/babdev/pagerfanta-bundle) [![Build Status](https://travis-ci.com/BabDev/BabDevPagerfantaBundle.svg?branch=master)](https://travis-ci.com/BabDev/BabDevPagerfantaBundle) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BabDev/BabDevPagerfantaBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BabDev/BabDevPagerfantaBundle/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/BabDev/BabDevPagerfantaBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/BabDev/BabDevPagerfantaBundle/?branch=master)

Bundle to use [Pagerfanta](https://github.com/whiteoctober/Pagerfanta) with [Symfony](https://github.com/symfony/symfony).

This bundle is a continuation of the [WhiteOctoberPagerfantaBundle](https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle).

The bundle includes:

  * Twig function to render Pagerfanta objects with views and options.
  * Pagerfanta view which supports Twig templates.
  * Way to use easily views.
  * Way to reuse options in views.
  * Basic CSS for the `Pagerfanta\View\DefaultView` class.

## Installation

1) Use [Composer](https://getcomposer.org/) to install the bundle in your application

```sh
composer require babdev/pagerfanta-bundle
```

2) Register the bundle with your application

If your application is based on the Symfony Standard structure, you will need to add the bundle to your `AppKernel` class' `registerBundles()` method.

```php
<?php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...

            new BabDev\PagerfantaBundle\BabDevPagerfantaBundle(),
        ];

        // ...
    }

    // ...
}
```

If your application is based on the Symfony Flex structure, the bundle should be automatically registered, otherwise you will need to add it to your `config/bundles.php` file.

```php
<?php

return [
    // ...

    BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class => ['all' => true],
];

```

3) Configure and use things!

A) **Creating a Pager** is shown on the [Pagerfanta](https://github.com/whiteoctober/Pagerfanta) documentation. If you're using the Doctrine ORM, you'll want to use the [DoctrineORMAdapter](https://github.com/whiteoctober/Pagerfanta#doctrineormadapter)

B) **Rendering in Twig** is shown below in the [Rendering Pagerfantas](#rendering-pagerfantas) section.

C) **Configuration** is shown through this document

## Rendering Pagerfantas

First, you'll need to pass an instance of Pagerfanta as a parameter into your template.

```php
<?php

namespace App\Controller;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class MyController extends AbstractController
{
    public function pagerfanta(): Response
    {
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);

        return $this->render(
            '@YourApp/Main/example.html.twig',
            [
                'my_pager' => $pagerfanta,
            ]
        );
    }
}
```

You then call the the `pagerfanta` function from the Twig extension, passing in the Pagerfanta instance. The routes are generated automatically for the current route using the variable "page" to propagate the page number. By default, the bundle uses the `Pagerfanta\View\DefaultView` class to render the pager.

```twig
{{ pagerfanta(my_pager) }}
```

By default, the "page" variable is also added for the link to the first page. To disable the generation of `?page=1` in the URL, set the `omitFirstPage` option to `true` when calling the `pagerfanta()` Twig function.

```twig
{{ pagerfanta(my_pager, 'default', {'omitFirstPage': true}) }}
```

You can omit the template parameter to make function call shorter, in this case the default template will be used.

```twig
{{ pagerfanta(my_pager, {'omitFirstPage': true }) }}
```

If you are using a parameter other than `page` for pagination, you can set the parameter name by using the `pageParameter` option when rendering the pager.

```twig
{{ pagerfanta(my_pager, 'default', {'pageParameter': '[other_page]'}) }}
```

Note that the page parameter *MUST* be wrapped in brackets (i.e. `[other_page]`).

See the [Pagerfanta documentation](https://github.com/whiteoctober/Pagerfanta) for the list of supported options.

## Generating Paginated Routes

When rendering a Pagerfanta view, a route generator callable is required to generate the URLs for each item in the pagination list. As of BabDevPagerfantaBundle 2.2, the route generator can be customized for use within your application if you need to adjust the routing logic.

The route generators are defined by two interfaces, with their default implementations noted below:

- `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorInterface` - The class type that is used to generate routes
    - `BabDev\PagerfantaBundle\RouteGenerator\RouterAwareRouteGenerator` is used by default, which uses the Symfony Routing component to generate routes
- `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorFactoryInterface` - A factory service that is used to generate a `RouteGeneratorInterface` at runtime
    - `BabDev\PagerfantaBundle\RouteGenerator\RequestAwareRouteGeneratorFactory` is used by default, which uses the `Request` object to attempt to set the default route name and route parameters, this creates a `RouterAwareRouteGenerator`

The Twig integration uses a `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorFactoryInterface` instance to create the route generator used when rendering a Pagerfanta view.

The `pagerfanta.route_generator_factory` service is available for use in your application if you need to create a route generator. You may use a compiler pass to change this service to any class meeting the interface requirements.

## Available Views

### Default Views

All of the views provided in the `pagerfanta/pagerfanta` are available by default for use with this bundle.

The below table lists the view names and the corresponding class. 

| View Name            | Class Name                              |
| -------------------- | --------------------------------------- |
| `default`            | `Pagerfanta\View\DefaultView`           |
| `semantic_ui`        | `Pagerfanta\View\SemanticUiView`        |
| `twitter_bootstrap`  | `Pagerfanta\View\TwitterBootstrapView`  |
| `twitter_bootstrap3` | `Pagerfanta\View\TwitterBootstrap3View` |
| `twitter_bootstrap4` | `Pagerfanta\View\TwitterBootstrap4View` |

### Twig View

_This feature was introduced in BabDevPagerfantaBundle 2.2_

This bundle provides a Pagerfanta view which renders a Twig template.

The below table lists the available templates and the CSS framework they correspond to.

| Template Name                                    | Framework                                            |
| ------------------------------------------------ | ---------------------------------------------------- |
| `@BabDevPagerfanta/default.html.twig`            | None (Pagerfanta's default view)                     |
| `@BabDevPagerfanta/semantic_ui.html.twig`        | [Semantic UI](https://semantic-ui.com) (version 2.x) |
| `@BabDevPagerfanta/twitter_bootstrap.html.twig`  | [Bootstrap](https://getbootstrap.com) (version 2.x)  |
| `@BabDevPagerfanta/twitter_bootstrap3.html.twig` | [Bootstrap](https://getbootstrap.com) (version 3.x)  |
| `@BabDevPagerfanta/twitter_bootstrap4.html.twig` | [Bootstrap](https://getbootstrap.com) (version 4.x)  |

Labels of Previous and Next buttons are localizable in all of these Twig templates.

If creating a custom template, you are encouraged to extend the `default.html.twig` template and override only the blocks needed.

Generally, the `pager_widget` block should only be extended if you need to change the wrapping HTML for the paginator. The `pager` block should still be rendered from your extended block.

The `pager` block is designed to hold the structure of the pager and generally should not be extended unless the intent is to change the logic involved in rendering the paginator (such as removing the ellipsis separators or changing to only display previous/next buttons).

When rendering a Twig view, the following options are passed into the template for use. Note that for the most part, only the `pager` block will use these variables.

- `pagerfanta` - The `Pagerfanta\Pagerfanta` object
- `route_generator` - A `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorDecorator` object which decorates the route generator created by the `pagerfanta()` Twig function
    - The decorator is required because Twig does not allow direct execution of Closures within templates
- `options` - The options array passed through the `pagerfanta()` Twig function
- `start_page` - The calculated start page for the list of items displayed between separators, this is based on the `proximity` option and the total number of pages
- `end_page` - The calculated end page for the list of items displayed between separators, this is based on the `proximity` option and the total number of pages
- `current_page` - The current page in the paginated list
- `nb_pages` - The total number of pages in the paginated list

Additionally, for most page blocks (`previous_page_link`, `page_link`, `current_page_link`, and `next_page_link`), there are two additional variables available:

- `page` - The current page in the pager
- `path` - The generated URL for the item

Note: these variables are irrelevant and should not be considered available in `previous_page_link_disabled` and `next_page_link_disabled` blocks.

If you want to create your own Twig template, the quickest and easiest way to do that is to extend one of the supplied templates (typically the default one). Have a look at `semantic_ui.html.twig` to see the blocks you will likely want to override.

### Translated Views

_This feature is deprecated as of BabDevPagerfantaBundle 2.2 and will be removed in 3.0_

This bundle also provides translated views, which allows using translation messages for the "Previous" and "Next" text items. The translated views act as decorators around the base view to automatically set the appropriate view options with the translated text.

The below lists the view names, the corresponding class, and the class the view decorates. 

| View Name                       | Class Name                                                     | Decorated Class Name                    |
| ------------------------------- | -------------------------------------------------------------- | --------------------------------------- |
| `default_translated`            | `BabDev\PagerfantaBundle\View\DefaultTranslatedView`           | `Pagerfanta\View\DefaultView`           |
| `semantic_ui_translated`        | `BabDev\PagerfantaBundle\View\SemanticUiTranslatedView`        | `Pagerfanta\View\SemanticUiView`        |
| `twitter_bootstrap_translated`  | `BabDev\PagerfantaBundle\View\TwitterBootstrapTranslatedView`  | `Pagerfanta\View\TwitterBootstrapView`  |
| `twitter_bootstrap3_translated` | `BabDev\PagerfantaBundle\View\TwitterBootstrap3TranslatedView` | `Pagerfanta\View\TwitterBootstrap3View` |
| `twitter_bootstrap4_translated` | `BabDev\PagerfantaBundle\View\TwitterBootstrap4TranslatedView` | `Pagerfanta\View\TwitterBootstrap4View` |

## Adding Views

Views are added to the service container with the `pagerfanta.view` tag. You can also specify an alias which is used as the view's name in a `Pagerfanta\View\ViewFactoryInterface` instance, but if one is not given then the service ID is used instead.

### XML Configuration

```xml
<container>
    <!-- Use in Twig by calling {{ pagerfanta(my_pager, 'default') }} -->
    <service id="pagerfanta.view.default" class="Pagerfanta\View\DefaultView" public="false">
        <tag name="pagerfanta.view" alias="default" />
    </service>

    <!-- Use in Twig by calling {{ pagerfanta(my_pager, 'pagerfanta.view.semantic_ui') }} -->
    <service id="pagerfanta.view.semantic_ui" class="Pagerfanta\View\SemanticUiView" public="false">
        <tag name="pagerfanta.view" />
    </service>
</container>
```

### YAML Configuration

```yaml
services:
    # Use in Twig by calling {{ pagerfanta(my_pager, 'default') }}
    pagerfanta.view.default:
        class: Pagerfanta\View\DefaultView
        public: false
        tags:
            - { name: pagerfanta.view, alias: default }

    # Use in Twig by calling {{ pagerfanta(my_pager, 'pagerfanta.view.semantic_ui') }}
    pagerfanta.view.semantic_ui:
        class: Pagerfanta\View\SemanticUiView
        public: false
        tags:
            - { name: pagerfanta.view }
```

## Retrieving Views

You can access the Pagerfanta views through the `pagerfanta.view_factory` service, which is a `Pagerfanta\View\ViewFactoryInterface` instance. This is useful if your application does not use Twig but you still want to use Pagerfanta views for rendering pagination lists.

```php
<?php

namespace App\Service;

use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewFactoryInterface;

final class PagerfantaService
{
    private $viewFactory;

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

## Reusing Options

Sometimes you want to reuse options for a view in your project and you don't want to repeat those options each time you render a view, or you have different configurations for a view and you want to save those configurations to be able to change them easily.

For this you can define views with the `Pagerfanta\View\OptionableView` class, which is a decorator for any `Pagerfanta\View\ViewInterface` instance.

```yaml
services:
    pagerfanta.view.low_proximity_and_spanish_messages:
        class: Pagerfanta\View\OptionableView
        arguments:
            - @pagerfanta.view.default
            - { proximity: 2, prev_message: Anterior, next_message: Siguiente }
        public: false
        tags:
            - { name: pagerfanta.view, alias: low_proximity_and_spanish_messages }

    pagerfanta.view.high_proximity:
        class: Pagerfanta\View\OptionableView
        arguments:
            - @pagerfanta.view.default
            - { proximity: 5 }
        public: false
        tags:
            - { name: pagerfanta.view, alias: high_proximity }
```

## Default View CSS

The bundle comes with basic CSS for the default view so you can get started quickly.

```html
<link rel="stylesheet" href="{{ asset('bundles/babdevpagerfanta/css/pagerfantaDefault.css') }}">
```

## Bundle Configuration

### Default View
The default view for your application can be set with the `default_view` configuration node. This defaults to "default".

```yaml
// app/config/config.yml for Symfony Standard applications
// config/packages/babdev_pagerfanta.yaml for Symfony Flex applications
babdev_pagerfanta:
    default_view: my_view
```

### Default Twig Template
The default Twig template for Twig views in your application can be set with the `default_twig_template` configuration node. This defaults to "`@BabDevPagerfanta/default.html.twig`".

```yaml
// app/config/config.yml for Symfony Standard applications
// config/packages/babdev_pagerfanta.yaml for Symfony Flex applications
babdev_pagerfanta:
    default_view: twig
    default_twig_template: '@App/Pagerfanta/default.html.twig'
```

### Exception Strategies

By default, the bundle converts `Pagerfanta\Exception\NotValidCurrentPageException` and `Pagerfanta\Exception\NotValidMaxPerPageException` exceptions into 404 responses. If you would like to disable or change this behavior, you can change the strategies using the `exceptions_strategy` node by setting the value to "custom" for each behavior you want to change.

```yaml
// app/config/config.yml for Symfony Standard applications
// config/packages/babdev_pagerfanta.yaml for Symfony Flex applications
babdev_pagerfanta:
    exceptions_strategy:
        out_of_range_page: custom # Disables converting `Pagerfanta\Exception\NotValidMaxPerPageException` to a 404 response
        not_valid_current_page: to_http_not_found # Default behavior converting `Pagerfanta\Exception\NotValidCurrentPageException` to a 404 response
```

## More information

For more advanced documentation, check the [Pagerfanta documentation](https://github.com/whiteoctober/Pagerfanta/blob/master/README.md).

## Contributing

We welcome contributions to this project, including pull requests and issues (and discussions on existing issues).

If you'd like to contribute code but aren't sure what, the [issues list](https://github.com/BabDev/BabDevPagerfantaBundle/issues) is a good place to start.
You can also look at the [original bundle issues](https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle/issues) for any items which are still open from there.
If you're a first-time code contributor, you may find Github's guide to [forking projects](https://guides.github.com/activities/forking/) helpful.

## Acknowledgements

This bundle is a continuation of the [WhiteOctoberPagerfantaBundle](https://github.com/whiteoctober/WhiteOctoberPagerfantaBundle). The work from all past contributors to the previous bundle is greatly appreciated.

## License

Pagerfanta is licensed under the MIT License. See the [LICENSE file](/LICENSE) for full details.
