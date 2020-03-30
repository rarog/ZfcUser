<?php

namespace UserAuthenticator\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Authentication\Adapter\AdapterChain;
use UserAuthenticator\Authentication\Storage\Db;

class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        return new AuthenticationService(
            $serviceLocator->get(Db::class),
            $serviceLocator->get(AdapterChain::class)
        );
    }
}
