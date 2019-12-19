# Migrate from WhiteOctoberPagerfantaBundle 1.x to BabDevPagerfantaBundle 2.0

The below guide will assist in migrating from WhiteOctoberPagerfantaBundle to BabDevPagerfantaBundle

## Bundle Requirements

- Symfony 3.4, 4.4, or any 5.x release
- PHP 7.2 or later

## General Changes

- The bundle namespace has changed from `WhiteOctober\PagerfantaBundle` to `BabDev\PagerfantaBundle`, any references in your application should be updated
- The bundle class has changed from `WhiteOctober\PagerfantaBundle\WhiteOctoberPagerfantaBundle` to `BabDev\PagerfantaBundle\BabDevPagerfantaBundle`, you will need to update your application to reference the correct bundle (with Symfony Flex this may have been done for you already)
- The bundle's root configuration node has moved from `white_october_pagerfanta` to `babdev_pagerfanta`, you will need to update the configuration in your application

## Service Configuration Changes

These changes relate to the bundle's DI container configuration

- Removed the `white_october_pagerfanta.view_factory.class` parameter, use a compiler pass if you wish to change this class
- Renamed the `white_october_pagerfanta.view_factory` service to `pagerfanta.view_factory` to be consistent with other service IDs and made the service public
- Renamed the `twig.extension.pagerfanta` service to `pagerfanta.twig_extension`
