<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle;

use BabDev\PagerfantaBundle\DependencyInjection\BabDevPagerfantaExtension;
use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\AddPagerfantasPass;
use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\MaybeRemoveTwigServicesPass;
use BabDev\PagerfantaBundle\DependencyInjection\CompilerPass\MaybeRemoveTwigViewPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class BabDevPagerfantaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // MaybeRemoveTwigServicesPass must be run before the TwigEnvironmentPass from TwigBundle
        $container->addCompilerPass(new AddPagerfantasPass());
        $container->addCompilerPass(new MaybeRemoveTwigViewPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 10);
        $container->addCompilerPass(new MaybeRemoveTwigServicesPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new BabDevPagerfantaExtension();
        }

        return $this->extension ?: null;
    }
}
