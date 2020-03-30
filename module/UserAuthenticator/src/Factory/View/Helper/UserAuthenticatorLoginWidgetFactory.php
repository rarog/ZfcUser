<?php

namespace UserAuthenticator\Factory\View\Helper;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use UserAuthenticator\View\Helper\UserAuthenticatorLoginWidget;

class UserAuthenticatorLoginWidgetFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $viewHelper = new UserAuthenticatorLoginWidget();
        $viewHelper->setViewTemplate($container->get('zfcuser_module_options')->getUserLoginWidgetViewTemplate());
        $viewHelper->setLoginForm($container->get('zfcuser_login_form'));

        return $viewHelper;
    }
}
