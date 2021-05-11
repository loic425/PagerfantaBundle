# Available Views

## Default Views

All of the views provided in the `pagerfanta/pagerfanta` are available by default for use with this bundle.

The below table lists the view names and the corresponding class. 

| View Name            | Class Name                              |
| -------------------- | --------------------------------------- |
| `default`            | `Pagerfanta\View\DefaultView`           |
| `foundation6`        | `Pagerfanta\View\Foundation6View`       |
| `semantic_ui`        | `Pagerfanta\View\SemanticUiView`        |
| `twitter_bootstrap`  | `Pagerfanta\View\TwitterBootstrapView`  |
| `twitter_bootstrap3` | `Pagerfanta\View\TwitterBootstrap3View` |
| `twitter_bootstrap4` | `Pagerfanta\View\TwitterBootstrap4View` |
| `twitter_bootstrap5` | `Pagerfanta\View\TwitterBootstrap5View` |

## Twig View

This bundle provides a Pagerfanta view which renders a Twig template.

The below table lists the available templates and the CSS framework they correspond to.

| Template Name                                    | Framework                                                     |
| ------------------------------------------------ | ------------------------------------------------------------- |
| `@BabDevPagerfanta/default.html.twig`            | None (Pagerfanta's default view)                              |
| `@BabDevPagerfanta/foundation6.html.twig`        | [Foundation](https://get.foundation/index.html) (version 6.x) |
| `@BabDevPagerfanta/semantic_ui.html.twig`        | [Semantic UI](https://semantic-ui.com) (version 2.x)          |
| `@BabDevPagerfanta/tailwind.html.twig`           | [Tailwind CSS](https://tailwindcss.com/)                      |
| `@BabDevPagerfanta/twitter_bootstrap.html.twig`  | [Bootstrap](https://getbootstrap.com) (version 2.x)           |
| `@BabDevPagerfanta/twitter_bootstrap3.html.twig` | [Bootstrap](https://getbootstrap.com) (version 3.x)           |
| `@BabDevPagerfanta/twitter_bootstrap4.html.twig` | [Bootstrap](https://getbootstrap.com) (version 4.x)           |
| `@BabDevPagerfanta/twitter_bootstrap5.html.twig` | [Bootstrap](https://getbootstrap.com) (version 5.x)           |

The labels of the "Previous" and "Next" buttons are localizable in the Twig templates.

### Creating a Twig View Template

If creating a custom template, you are encouraged to extend the `@BabDevPagerfanta/default.html.twig` template (found at `Resources/views/default.html.twig`) and override only the blocks needed.

Generally, the `pager_widget` block should only be extended if you need to change the wrapping HTML for the paginator. The `pager` block should still be rendered from your extended block.

The `pager` block is designed to hold the structure of the pager and generally should not be extended unless the intent is to change the logic involved in rendering the paginator (such as removing the ellipsis separators or changing to only display previous/next buttons).

When rendering a Twig view, the following options are passed into the template for use. Note that for the most part, only the `pager` block will use these variables.

- `pagerfanta` - The `Pagerfanta\Pagerfanta` object
- `route_generator` - A `BabDev\PagerfantaBundle\RouteGenerator\RouteGeneratorDecorator` object which decorates the route generator created by the `pagerfanta()` Twig function
    - The decorator is required because Twig does not allow direct execution of Closures within templates
- `options` - The options array passed through the `pagerfanta()` Twig function
- `start_page` - The calculated start page for the list of items displayed between separators, this is based on the `proximity` option and the total number of pages
- `end_page` - The calculated end page for the list of items displayed between separators, this is based on the `proximity` option and the total number of pages
- `current_page` - The current page in the paginated list
- `nb_pages` - The total number of pages in the paginated list

Additionally, for most page blocks (`previous_page_link`, `page_link`, `current_page_link`, and `next_page_link`), there are two additional variables available:

- `page` - The current page in the pager
- `path` - The generated URL for the item

If you want to create your own Twig template, the quickest and easiest way to do that is to extend one of the supplied templates (typically the default one). Have a look at `semantic_ui.html.twig` to see the blocks you will likely want to override.

## Default View CSS

The bundle comes with basic CSS for the default view so you can get started quickly.

```html
<link rel="stylesheet" href="{{ asset('bundles/babdevpagerfanta/css/pagerfanta.css') }}">
```
