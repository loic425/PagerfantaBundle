# Upgrade from 2.x to 3.0

The below guide will assist in upgrading from the 2.x versions to 3.0.

## Bundle Requirements

- Symfony 4.4, or any 5.x release
- PHP 7.4 or later

## General Changes

- Added parameter, return, and property typehints where not previously in place
- The values for the `exceptions_strategy.out_of_range_page` and `exceptions_strategy.not_valid_current_page` must be one of "to_http_not_found" or "custom", other values will raise an exception
