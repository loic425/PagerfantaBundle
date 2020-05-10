# Adding Views

Views are added to the service container with the `pagerfanta.view` tag. You can also specify an alias which is used as the view's name in a `Pagerfanta\View\ViewFactoryInterface` instance, but if one is not given then the service ID is used instead.

{note} It is recommended that view services are *NOT* public services, the `ViewFactoryInterface` should be used to [retrieve views](/open-source/packages/babdevpagerfantabundle/docs/2.x/retrieving-views)

## XML Configuration

```xml
<container>
    <!-- Use in Twig by calling {{ pagerfanta(pager, 'default') }} -->
    <service id="pagerfanta.view.default" class="Pagerfanta\View\DefaultView" public="false">
        <tag name="pagerfanta.view" alias="default" />
    </service>

    <!-- Use in Twig by calling {{ pagerfanta(pager, 'pagerfanta.view.semantic_ui') }} -->
    <service id="pagerfanta.view.semantic_ui" class="Pagerfanta\View\SemanticUiView" public="false">
        <tag name="pagerfanta.view" />
    </service>
</container>
```

## YAML Configuration

```yaml
services:
    # Use in Twig by calling {{ pagerfanta(pager, 'default') }}
    pagerfanta.view.default:
        class: Pagerfanta\View\DefaultView
        public: false
        tags:
            - { name: pagerfanta.view, alias: default }

    # Use in Twig by calling {{ pagerfanta(pager, 'pagerfanta.view.semantic_ui') }}
    pagerfanta.view.semantic_ui:
        class: Pagerfanta\View\SemanticUiView
        public: false
        tags:
            - { name: pagerfanta.view }
```
