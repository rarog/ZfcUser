<?php

namespace UserAuthenticator\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\View\Helper\UserAuthenticatorLoginWidget;
use UserAuthenticator\Form\Login;

class UserAuthenticatorLoginWidgetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = new UserAuthenticatorLoginWidget();
        $viewHelper->setViewTemplate($container->get(ModuleOptions::class)->getUserLoginWidgetViewTemplate());
        $viewHelper->setLoginForm($container->get(Login::class));

        return $viewHelper;
    }
}
