<?php

namespace UserAuthenticator;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;

class Module implements
    ControllerProviderInterface,
    ControllerPluginProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    ViewHelperProviderInterface
{
    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ConfigProviderInterface::getConfig()
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ControllerPluginProviderInterface::getControllerPluginConfig()
     */
    public function getControllerPluginConfig()
    {
        return [
            'factories' => [
                'zfcUserAuthentication' => Factory\Controller\Plugin\UserAuthenticatorAuthenticationFactory::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ControllerProviderInterface::getControllerConfig()
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                'zfcuser' => Factory\Controller\UserControllerFactory::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ViewHelperProviderInterface::getViewHelperConfig()
     */
    public function getViewHelperConfig()
    {
        return [
            'factories' => [
                'zfcUserDisplayName' => Factory\View\Helper\UserAuthenticatorDisplayNameFactory::class,
                'zfcUserIdentity' => Factory\View\Helper\UserAuthenticatorIdentityFactory::class,
                'zfcUserLoginWidget' => Factory\View\Helper\UserAuthenticatorLoginWidgetFactory::class,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     * @see \Laminas\ModuleManager\Feature\ServiceProviderInterface::getServiceConfig()
     */
    public function getServiceConfig()
    {
        return [
            'aliases' => [
                'user_authenticator_laminas_db_adapter' => AdapterInterface::class,
            ],
            'factories' => [
                Authentication\Adapter\AdapterChain::class =>
                    Factory\Authentication\Adapter\AdapterChainFactory::class,
                Authentication\Adapter\Db::class => Factory\Authentication\Adapter\DbFactory::class,
                Authentication\Storage\Db::class => Factory\Authentication\Storage\DbFactory::class,
                Controller\RedirectCallback::class => Factory\Controller\RedirectCallbackFactory::class,
                Form\ChangeEmailForm::class => Factory\Form\ChangeEmailFormFactory::class,
                Form\ChangePasswordForm::class => Factory\Form\ChangePasswordFormFactory::class,
                Form\LoginForm::class => Factory\Form\LoginFormFactory::class,
                Form\RegisterForm::class => Factory\Form\RegisterFormFactory::class,
                Mapper\User::class => Factory\Mapper\UserFactory::class,
                Options\ModuleOptions::class => Factory\Options\ModuleOptionsFactory::class,
                Service\UserService::class => Factory\Service\UserServiceFactory::class,

                // We alias this one because it's UserAuthenticator's instance of
                // Laminas\Authentication\AuthenticationService. We don't want to
                // hog the FQCN service alias for a Laminas\* class.
                'zfcuser_auth_service' => Factory\AuthenticationServiceFactory::class,
            ],
        ];
    }
}
