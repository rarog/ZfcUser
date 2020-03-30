<?php

namespace UserAuthenticator\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Controller\UserController;

class UserControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        /* @var RedirectCallback $redirectCallback */
        $redirectCallback = $serviceManager->get('zfcuser_redirect_callback');

        /* @var UserController $controller */
        $controller = new UserController($redirectCallback);
        $controller->setServiceLocator($serviceManager);

        $controller->setChangeEmailForm($serviceManager->get('zfcuser_change_email_form'));
        $controller->setOptions($serviceManager->get('zfcuser_module_options'));
        $controller->setChangePasswordForm($serviceManager->get('zfcuser_change_password_form'));
        $controller->setLoginForm($serviceManager->get('zfcuser_login_form'));
        $controller->setRegisterForm($serviceManager->get('zfcuser_register_form'));
        $controller->setUserService($serviceManager->get('zfcuser_user_service'));

        return $controller;
    }
}
