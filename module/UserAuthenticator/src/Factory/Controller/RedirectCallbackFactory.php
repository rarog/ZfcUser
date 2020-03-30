<?php

namespace UserAuthenticator\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\Mvc\Application;
use Laminas\Router\RouteInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Options\ModuleOptions;

class RedirectCallbackFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /* @var RouteInterface $router */
        $router = $serviceLocator->get('Router');

        /* @var Application $application */
        $application = $serviceLocator->get(Application::class);

        /* @var ModuleOptions $options */
        $options = $serviceLocator->get(ModuleOptions::class);

        return new RedirectCallback($application, $router, $options);
    }
}
