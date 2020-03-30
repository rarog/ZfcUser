<?php

namespace UserAuthenticator\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Controller\UserController;
use UserAuthenticator\Form\ChangeEmail;
use UserAuthenticator\Form\Login;
use UserAuthenticator\Form\Register;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Service\User;
use UserAuthenticator\Form\ChangePassword;

class UserControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $serviceManager, $requestedName, array $options = null)
    {
        /* @var RedirectCallback $redirectCallback */
        $redirectCallback = $serviceManager->get(RedirectCallback::class);

        /* @var UserController $controller */
        $controller = new UserController($redirectCallback);
        $controller->setServiceLocator($serviceManager);

        $controller->setChangeEmailForm($serviceManager->get(ChangeEmail::class));
        $controller->setOptions($serviceManager->get(ModuleOptions::class));
        $controller->setChangePasswordForm($serviceManager->get(ChangePassword::class));
        $controller->setLoginForm($serviceManager->get(Login::class));
        $controller->setRegisterForm($serviceManager->get(Register::class));
        $controller->setUserService($serviceManager->get(User::class));

        return $controller;
    }
}
