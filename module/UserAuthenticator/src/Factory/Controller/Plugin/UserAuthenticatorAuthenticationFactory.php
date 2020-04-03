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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get('zfcuser_auth_service');
        $authAdapter = $container->get(AdapterChain::class);

        $controllerPlugin = new UserAuthenticatorAuthentication();
        $controllerPlugin->setAuthService($authService);
        $controllerPlugin->setAuthAdapter($authAdapter);

        return $controllerPlugin;
    }
}
