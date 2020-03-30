<?php

namespace UserAuthenticator\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\View\Helper\UserAuthenticatorIdentity;

class UserAuthenticatorIdentityFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = new UserAuthenticatorIdentity();
        $viewHelper->setAuthService($container->get('zfcuser_auth_service'));

        return $viewHelper;
    }
}
