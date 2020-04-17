# Upgrade from 2.x to 3.0

The below guide will assist in upgrading from the 2.x versions to 3.0.

## Bundle Requirements

- Symfony 4.4, or any 5.x release
- PHP 7.4 or later

## General Changes

- Added parameter, return, and property typehints where not previously in place
- The values for the `babdev_pagerfanta.exceptions_strategy.out_of_range_page` and `babdev_pagerfanta.exceptions_strategy.not_valid_current_page` configuration options must be one of "to_http_not_found" or "custom", other values will raise an exception
- The Twig functions no longer accept deprecated `Pagerfanta\PagerfantaInterface` implementations, `Pagerfanta\Pagerfanta` objects must be used

## Removed Features

- Removed the translated view classes, use the Twig view instead (note, the default Twig templates use translations for the Previous and Next messages)
