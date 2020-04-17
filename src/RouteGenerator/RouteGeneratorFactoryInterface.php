<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\RouteGenerator;

interface RouteGeneratorFactoryInterface
{
    public function create(array $options = []): RouteGeneratorInterface;
}
