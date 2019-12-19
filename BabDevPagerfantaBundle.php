<?php

/*
 * This file is part of the Pagerfanta package.
 *
 * (c) Pablo Díez <pablodip@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BabDev\PagerfantaBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use BabDev\PagerfantaBundle\DependencyInjection\Compiler\AddPagerfantasPass;

/**
 * BabDevPagerfantaBundle.
 *
 * @author Pablo Díez <pablodip@gmail.com>
 */
class BabDevPagerfantaBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddPagerfantasPass());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $extension = $this->createContainerExtension();

            if (null !== $extension) {
                if (!$extension instanceof ExtensionInterface) {
                    throw new \LogicException(sprintf('Extension %s must implement %s.', get_class($extension), ExtensionInterface::class));
                }

                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }

        return parent::getContainerExtension();
    }
}
