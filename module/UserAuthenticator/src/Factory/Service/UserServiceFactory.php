<?php

namespace UserAuthenticator\Factory\Service;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Mapper\User as UserMapper;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Service\UserService;

class UserServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        return new UserService(
            $serviceLocator->get(UserMapper::class),
            $serviceLocator->get('zfcuser_auth_service'),
            $serviceLocator->get(ModuleOptions::class)
        );
    }
}
