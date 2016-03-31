<?php

/*
 * This file is part of the FOSRestBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\RestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Remove the 'twig.exception_listener' service if 'fos_rest.exception_listener' is activated.
 *
 * @internal
 */
final class TwigExceptionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->has('fos_rest.exception_listener') && $container->has('twig.exception_listener')) {

            // wrap our exception listener around twig exception listener
            $wrappedExceptionListener = $container->findDefinition('twig.exception_listener');
            $wrappedExceptionListener->setPublic(false);
            $wrappedExceptionListener->setTags(array());

            $container->setDefinition('fos_rest.wrapped_exception_listener', $wrappedExceptionListener);
            $container->findDefinition('fos_rest.exception_listener')->addArgument(
                new Reference('fos_rest.wrapped_exception_listener')
            );
            $container->removeDefinition('twig.exception_listener');
        }

        if (!$container->has('templating.engine.twig')) {
            $container->removeDefinition('fos_rest.exception.twig_controller');
        }
    }
}
