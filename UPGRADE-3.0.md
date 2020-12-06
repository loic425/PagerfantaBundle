# Upgrade from 2.x to 3.0

The below guide will assist in upgrading from the 2.x versions to 3.0.

## Bundle Requirements

- Symfony 4.4, or any 5.x release
- PHP 7.4 or later
- Pagerfanta 3.0

## General Changes

- Added parameter, return, and property typehints where not previously in place
- The values for the `babdev_pagerfanta.exceptions_strategy.out_of_range_page` and `babdev_pagerfanta.exceptions_strategy.not_valid_current_page` configuration options must be one of "to_http_not_found" or "custom", other values will raise an exception
- The bundle no longer requires the `pagerfanta/pagerfanta` monopackage, only the `pagerfanta/core` and `pagerfanta/twig` packages are included now; you will need to install the appropriate package(s) to use the adapters your application requires
- The compiler passes are now internal, they are not intended for direct use by bundle users and B/C will no longer be guaranteed on them
- The bundle's Twig templates now all extend the templates from the `pagerfanta/twig` package
- Renamed the CSS file for the default template from `pagerfantaDefault.css` to `pagerfanta.css`

## Removed Features

- Removed the translated view classes, use the Twig view instead (note, the default Twig templates use translations for the Previous and Next messages)
- Removed the `RouteGeneratorDecorator`, `RouteGeneratorFactoryInterface`, and `RouteGeneratorInterface` from the bundle in favor of the class and interfaces from the `pagerfanta/core` package
- Removed the Twig extension and view from the bundle in favor of the extension from the `pagerfanta/twig` package
