<?php

namespace UserAuthenticator\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Controller\UserController;
use UserAuthenticator\Form\ChangeEmail as ChangeEmailForm;
use UserAuthenticator\Form\ChangePassword as ChangePasswordForm;
use UserAuthenticator\Form\Login as LoginForm;
use UserAuthenticator\Form\Register as RegisterForm;
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
        $controller = new UserController(
            $container->get(RedirectCallback::class),
            $container->get(UserService::class),
            $container->get(ChangeEmailForm::class),
            $container->get(ChangePasswordForm::class),
            $container->get(LoginForm::class),
            $container->get(RegisterForm::class),
            $container->get(ModuleOptions::class)
        );

        return $controller;
    }
}
