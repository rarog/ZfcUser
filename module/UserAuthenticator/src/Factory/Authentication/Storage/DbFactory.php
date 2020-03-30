<?php

namespace UserAuthenticator\Factory\Authentication\Storage;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Authentication\Storage\Db;

class DbFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $db = new Db();
        $db->setServiceManager($serviceLocator);

        return $db;
    }
}
