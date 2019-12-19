<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle;

use BabDev\PagerfantaBundle\DependencyInjection\Compiler\AddPagerfantasPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BabDevPagerfantaBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AddPagerfantasPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $extension = $this->createContainerExtension();

            if (null !== $extension) {
                if (!$extension instanceof ExtensionInterface) {
                    throw new \LogicException(sprintf('Extension %s must implement %s.', \get_class($extension), ExtensionInterface::class));
                }

                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }

        return parent::getContainerExtension();
    }
}
