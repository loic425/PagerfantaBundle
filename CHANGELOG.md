# Changelog

## 2.5.0 (2020-??-??)

- Add the `referenceType` option to the `RouterAwareRouteGenerator` to allow specifying the `$referenceType` parameter when calling `Symfony\Component\Routing\Generator\UrlGeneratorInterface::generate()`
- Deprecate the `RouteGeneratorFactoryInterface` and `RouteGeneratorInterface` in favor of the interfaces from the `pagerfanta/core` package
- Deprecate the Twig extension, runtime extension, and Pagerfanta view in favor of the classes from the `pagerfanta/twig` package

## 2.4.2 (2020-06-09)

- Fix `pagerfanta/pagerfanta` minimum version

## 2.4.1 (2020-06-09)

- Change `babdev/pagerfanta` dependency back to `pagerfanta/pagerfanta`

## 2.4.0 (2020-06-06)

- Change `pagerfanta/pagerfanta` dependency to `babdev/pagerfanta` (direct replacement/upgrade without B/C breaks)

## 2.3.2 (2020-05-31)

- [#13](https://github.com/BabDev/BabDevPagerfantaBundle/pull/13) Suppress runtime deprecation for `MaybeRemoveTranslatedViewsPass` (Fixes [#12](https://github.com/BabDev/BabDevPagerfantaBundle/issues/12))

## 2.3.1 (2020-05-31)

- Fix deprecation in Symfony 5.1 when marking services deprecated

## 2.3.0 (2020-05-29)

- Use the `symfony/deprecation-contracts` to trigger runtime deprecation notices
- Added a Tailwind CSS Twig View (based on the similar view added to Laravel's Pagination component)

## 2.2.1 (2020-04-20)

- Corrected namespace for Twig templates

## 2.2.0 (2020-04-18)

- Added a `Pagerfanta\View\ViewInterface` implementation which supports rendering Twig templates
- Deprecated `BabDev\PagerfantaBundle\View\TranslatedView` and all child classes in favor of the Twig view and the respective Twig template for the translated view
- Deprecate setting `babdev_pagerfanta.exceptions_strategy.out_of_range_page` and `babdev_pagerfanta.exceptions_strategy.not_valid_current_page` configuration options to any value, as of 3.0 they must be either "to_http_not_found" (default) or "custom"
- Removed the dependency to TwigBundle and made Twig an optional dependency
- Made the Symfony Translation component an optional dependency
- Extracted the logic for building the route generator used by the Twig extension into `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorFactoryInterface` and `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorInterface` with default implementations for the existing generator

## 2.1.0 (2020-01-29)

- Remove event listener services from the container when they are not used as their respective exception strategy
- Move the Twig function code into a Twig runtime service
- Deprecate support for the deprecated `Pagerfanta\PagerfantaInterface` in Twig functions, as of 3.0 all pagers must be a subclass of `Pagerfanta\Pagerfanta`
