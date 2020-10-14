# Serializer

The PagerfantaBundle provides support for serializing `Pagerfanta/Pagerfanta` instances using either the [Symfony Serializer component](https://symfony.com/doc/current/components/serializer.html) or the [JMS Serializer](https://jmsyst.com/libs/serializer) (note, the `JMSSerializerBundle` must be installed to enable the serialization handler for the JMS serializer).

Below is an example of building a JSON response in a controller using the Symfony Serializer:

```php
<?php

namespace App\Controller\API;

use App\Entity\BlogPost;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class PostController extends AbstractController
{
    /**
     * @Route("/api/posts", name="app_api_post_list", methods={"GET"})
     */
    public function apiPostList(): JsonResponse
    {
        $queryBuilder = $this->get('doctrine')->getRepository(BlogPost::class)->createBlogListQueryBuilder();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );

        return $this->json($pagerfanta);
    }
}
```

Below is an example of how a `Pagerfanta\Pagerfanta` instance is serialized into JSON format (note the `items` array is a simplified example, it will be an array of items based on your serializer configuration):

```json
{
    "items": [
        {
            "id": 1
        },
        {
            "id": 2
        },
        {
            "id": 3
        }
    ],
    "pagination": {
        "current_page": 1,
        "has_previous_page": false,
        "has_next_page": true,
        "per_page": 10,
        "total_items": 35,
        "total_pages": 4
    }
}
```
