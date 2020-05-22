# Reusable Pagerfanta Configurations

Sometimes you want to reuse options for a view in your project and you don't want to repeat those options each time you render a view, or you have different configurations for a view and you want to save those configurations to be able to change them easily.

For this you can define views with the `Pagerfanta\View\OptionableView` class, which is a decorator for any `Pagerfanta\View\ViewInterface` instance.

```yaml
services:
    # Use in Twig by calling {{ pagerfanta(pager, 'low_proximity_and_spanish_messages') }}
    pagerfanta.view.low_proximity_and_spanish_messages:
        class: Pagerfanta\View\OptionableView
        arguments:
            - @pagerfanta.view.default
            - { proximity: 2, prev_message: Anterior, next_message: Siguiente }
        public: false
        tags:
            - { name: pagerfanta.view, alias: low_proximity_and_spanish_messages }

    # Use in Twig by calling {{ pagerfanta(pager, 'high_proximity') }}
    pagerfanta.view.high_proximity:
        class: Pagerfanta\View\OptionableView
        arguments:
            - @pagerfanta.view.default
            - { proximity: 5 }
        public: false
        tags:
            - { name: pagerfanta.view, alias: high_proximity }
```
