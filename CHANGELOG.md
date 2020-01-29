# Changelog

## 2.1.0 (2020-01-29)

- Remove event listener services from the container when they are not used as their respective exception strategy
- Move the Twig function code into a Twig runtime service
- Deprecate support for the deprecated `Pagerfanta\PagerfantaInterface` in Twig functions, as of 3.0 all pagers must be a subclass of `Pagerfanta\Pagerfanta`
