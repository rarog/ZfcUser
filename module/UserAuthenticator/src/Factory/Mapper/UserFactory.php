<?php

namespace UserAuthenticator\Factory\Mapper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Mapper\User;
use UserAuthenticator\Mapper\UserHydrator;
use UserAuthenticator\Options\ModuleOptions;

class UserFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $options */
        $options = $serviceLocator->get(ModuleOptions::class);
        $dbAdapter = $serviceLocator->get('user_authenticator_laminas_db_adapter');

        $entityClass = $options->getUserEntityClass();
        $tableName = $options->getTableName();

        $mapper = new User();
        $mapper->setDbAdapter($dbAdapter);
        $mapper->setTableName($tableName);
        $mapper->setEntityPrototype(new $entityClass());
        $mapper->setHydrator(new UserHydrator());

        return $mapper;
    }
}
