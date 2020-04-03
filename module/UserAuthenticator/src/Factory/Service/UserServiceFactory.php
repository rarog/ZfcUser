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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserService(
            $container->get(UserMapper::class),
            $container->get('zfcuser_auth_service'),
            $container->get(ModuleOptions::class)
        );
    }
}
