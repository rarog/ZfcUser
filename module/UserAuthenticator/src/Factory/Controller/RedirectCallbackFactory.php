<?php

namespace UserAuthenticator\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Application;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Options\ModuleOptions;

class RedirectCallbackFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new RedirectCallback(
            $container->get(Application::class),
            $container->get('Router'),
            $container->get(ModuleOptions::class)
        );
    }
}
