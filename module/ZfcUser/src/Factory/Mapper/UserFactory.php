<?php

namespace ZfcUser\Factory\Mapper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use ZfcUser\Mapper\User;
use ZfcUser\Mapper\UserHydrator;
use ZfcUser\Options\ModuleOptions;

class UserFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $options */
        $options = $serviceLocator->get('zfcuser_module_options');
        $dbAdapter = $serviceLocator->get('zfcuser_zend_db_adapter');

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
