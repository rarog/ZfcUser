<?php

namespace UserAuthenticator\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Controller\UserController;
use UserAuthenticator\Form\ChangeEmail;
use UserAuthenticator\Form\ChangePassword;
use UserAuthenticator\Form\Login;
use UserAuthenticator\Form\Register;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Service\UserService;

class UserControllerFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ServiceManager\Factory\FactoryInterface::__invoke()
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var RedirectCallback $redirectCallback */
        $redirectCallback = $container->get(RedirectCallback::class);

        /* @var UserController $controller */
        $controller = new UserController($redirectCallback);
        $controller->setServiceLocator($container);

        $controller->setChangeEmailForm($container->get(ChangeEmail::class));
        $controller->setOptions($container->get(ModuleOptions::class));
        $controller->setChangePasswordForm($container->get(ChangePassword::class));
        $controller->setLoginForm($container->get(Login::class));
        $controller->setRegisterForm($container->get(Register::class));
        $controller->setUserService($container->get(UserService::class));

        return $controller;
    }
}
