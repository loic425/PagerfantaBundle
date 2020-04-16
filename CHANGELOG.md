# Changelog

## 2.2.0 (TBD)

- Added a `Pagerfanta\View\ViewInterface` implementation which supports rendering Twig templates
- Deprecated `BabDev\PagerfantaBundle\View\TranslatedView` and all child classes in favor of the Twig view and the respective Twig template for the translated view
- Deprecate setting `babdev_pagerfanta.exceptions_strategy.out_of_range_page` and `babdev_pagerfanta.exceptions_strategy.not_valid_current_page` configuration options to any value, as of 3.0 they must be either "to_http_not_found" (default) or "custom"

## 2.1.0 (2020-01-29)

- Remove event listener services from the container when they are not used as their respective exception strategy
- Move the Twig function code into a Twig runtime service
- Deprecate support for the deprecated `Pagerfanta\PagerfantaInterface` in Twig functions, as of 3.0 all pagers must be a subclass of `Pagerfanta\Pagerfanta`
