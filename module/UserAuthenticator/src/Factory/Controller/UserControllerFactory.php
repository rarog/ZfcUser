<?php

namespace UserAuthenticator\Factory\Controller;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Controller\UserController;
use UserAuthenticator\Form\ChangeEmailForm;
use UserAuthenticator\Form\ChangePasswordForm;
use UserAuthenticator\Form\LoginForm;
use UserAuthenticator\Form\RegisterForm;
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
