# Changelog

## 2.10.1 (2021-06-09)

- [#36](https://github.com/BabDev/PagerfantaBundle/pull/36) Items not correctly serialized when using `symfony/serializer` (Fixes [#35](https://github.com/BabDev/PagerfantaBundle/issues/35))

## 2.10.0 (2021-03-31)

- Drop support for Symfony 5.1, it is no longer maintained
- Fix errors caused by optional Twig dependency not being installed

## 2.9.0 (2021-02-18)

- Add support for Bootstrap 5
- Fix hardcoded "Next" message in the Tailwind template's `next_page_link` block

## 2.8.0 (2020-11-10)

- Allow install on PHP 8

## 2.7.0 (2020-10-14)

- Added support for the Symfony and JMS Serializers

## 2.6.1 (2020-09-30)

- Remove deprecations from view services

## 2.6.0 (2020-09-29)

- Trigger runtime deprecations for deprecated views when the view is rendered
- Drop support for Symfony 5.0, it is no longer maintained

## 2.5.2 (2020-08-25)

- [#23](https://github.com/BabDev/PagerfantaBundle/issues/23) Invalid Twig configuration for Pagerfanta namespace

## 2.5.1 (2020-08-24)

- [#22](https://github.com/BabDev/PagerfantaBundle/pull/22) Pagerfanta namespace not always registered to Twig (Fixes [#21](https://github.com/BabDev/PagerfantaBundle/issues/21))

## 2.5.0 (2020-07-25)

- Add the `referenceType` option to the `RouterAwareRouteGenerator` to allow specifying the `$referenceType` parameter when calling `Symfony\Component\Routing\Generator\UrlGeneratorInterface::generate()`
- Deprecate the `RouteGeneratorFactoryInterface` and `RouteGeneratorInterface` in favor of the interfaces from the `pagerfanta/core` package
- Deprecate the Twig extension, runtime extension, and Pagerfanta view in favor of the classes from the `pagerfanta/twig` package

## 2.4.3 (2020-06-30)

- [#13](https://github.com/BabDev/PagerfantaBundle/pull/18) Translated views are always removed from the container (Fixes [#17](https://github.com/BabDev/PagerfantaBundle/issues/17))

## 2.4.2 (2020-06-09)

- Fix `pagerfanta/pagerfanta` minimum version

## 2.4.1 (2020-06-09)

- Change `babdev/pagerfanta` dependency back to `pagerfanta/pagerfanta`

## 2.4.0 (2020-06-06)

- Change `pagerfanta/pagerfanta` dependency to `babdev/pagerfanta` (direct replacement/upgrade without B/C breaks)

## 2.3.2 (2020-05-31)

- [#13](https://github.com/BabDev/PagerfantaBundle/pull/13) Suppress runtime deprecation for `MaybeRemoveTranslatedViewsPass` (Fixes [#12](https://github.com/BabDev/PagerfantaBundle/issues/12))

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
