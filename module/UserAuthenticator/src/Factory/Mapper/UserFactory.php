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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);
        $dbAdapter = $container->get('user_authenticator_laminas_db_adapter');

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
