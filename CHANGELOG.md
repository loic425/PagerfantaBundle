# Changelog

## 3.3.0 (????-??-??)

- Drop support for Symfony 5.2, it is no longer maintained
- Throw exception in the route generator factory if there is not an active request

## 3.2.1 (2021-06-09)

- [#36](https://github.com/BabDev/PagerfantaBundle/pull/36) Items not correctly serialized when using `symfony/serializer` (Fixes [#35](https://github.com/BabDev/PagerfantaBundle/issues/35))

## 3.2.0 (2021-05-11)

- Add support for the Foundation 6 templates added to the `pagerfanta/core` and `pagerfanta/twig` packages

## 3.1.0 (2021-04-25)

- Inject the `property_accessor` service into the route generator factory and route generator when available
- Deprecate not passing a property accessor into the route generator factory and route generator

## 3.0.2 (2021-03-31)

- Fix more errors caused by optional Twig dependency not being installed

## 3.0.1 (2021-03-08)

- Fix errors caused by optional Twig dependency not being installed

## 3.0.0 (2021-03-07)

- Consult the UPGRADE guide for changes between 2.x and 3.0
