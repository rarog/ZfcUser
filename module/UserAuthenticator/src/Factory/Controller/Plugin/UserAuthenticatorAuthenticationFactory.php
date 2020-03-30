<?php

namespace UserAuthenticator\Factory\Controller\Plugin;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Authentication\Adapter\AdapterChain;
use UserAuthenticator\Controller\Plugin\UserAuthenticatorAuthentication;

class UserAuthenticatorAuthenticationFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceLocator, $requestedName, array $options = null)
    {
        $authService = $serviceLocator->get('zfcuser_auth_service');
        $authAdapter = $serviceLocator->get(AdapterChain::class);

        $controllerPlugin = new UserAuthenticatorAuthentication();
        $controllerPlugin->setAuthService($authService);
        $controllerPlugin->setAuthAdapter($authAdapter);

        return $controllerPlugin;
    }
}
