<?php

namespace UserAuthenticator;

use Laminas\Db\Adapter\Adapter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;
use UserAuthenticator\Authentication\Adapter\AdapterChain;
use UserAuthenticator\Authentication\Adapter\Db as AdapterDb;
use UserAuthenticator\Authentication\Storage\Db as StorageDb;
use UserAuthenticator\Factory\AuthenticationServiceFactory;
use UserAuthenticator\Factory\UserHydratorFactory;
use UserAuthenticator\Factory\Authentication\Adapter\AdapterChainFactory;
use UserAuthenticator\Factory\Authentication\Adapter\DbFactory as AdapterDbFactory;
use UserAuthenticator\Factory\Authentication\Storage\DbFactory as StorageDbFactory;
use UserAuthenticator\Factory\Controller\RedirectCallbackFactory;
use UserAuthenticator\Factory\Controller\UserControllerFactory;
use UserAuthenticator\Factory\Controller\Plugin\UserAuthenticatorAuthenticationFactory;
use UserAuthenticator\Factory\Form\ChangeEmailFactory;
use UserAuthenticator\Factory\Form\ChangePasswordFactory;
use UserAuthenticator\Factory\Form\LoginFactory;
use UserAuthenticator\Factory\Form\RegisterFactory;
use UserAuthenticator\Factory\Mapper\UserFactory as MapperUserFactory;
use UserAuthenticator\Factory\Options\ModuleOptionsFactory;
use UserAuthenticator\Factory\Service\UserFactory as ServiceUserFactory;
use UserAuthenticator\Factory\View\Helper\UserAuthenticatorDisplayNameFactory;
use UserAuthenticator\Factory\View\Helper\UserAuthenticatorIdentityFactory;
use UserAuthenticator\Factory\View\Helper\UserAuthenticatorLoginWidgetFactory;

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
    public function getConfig($env = null)
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
                'zfcUserAuthentication' => UserAuthenticatorAuthenticationFactory::class,
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
                'zfcuser' => UserControllerFactory::class,
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
                'zfcUserDisplayName' => UserAuthenticatorDisplayNameFactory::class,
                'zfcUserIdentity' => UserAuthenticatorIdentityFactory::class,
                'zfcUserLoginWidget' => UserAuthenticatorLoginWidgetFactory::class,
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
                'zfcuser_zend_db_adapter' => Adapter::class,
            ],
            'invokables' => [
                'zfcuser_register_form_hydrator' => ClassMethodsHydrator::class,
            ],
            'factories' => [
                'zfcuser_redirect_callback' => RedirectCallbackFactory::class,
                'zfcuser_module_options' => ModuleOptionsFactory::class,
                AdapterChain::class => AdapterChainFactory::class,

                // We alias this one because it's UserAuthenticator's instance of
                // Laminas\Authentication\AuthenticationService. We don't want to
                // hog the FQCN service alias for a Laminas\* class.
                'zfcuser_auth_service' => AuthenticationServiceFactory::class,

                'zfcuser_user_hydrator' => UserHydratorFactory::class,
                'zfcuser_user_mapper' => MapperUserFactory::class,

                'zfcuser_login_form' => LoginFactory::class,
                'zfcuser_register_form' => RegisterFactory::class,
                'zfcuser_change_password_form' => ChangePasswordFactory::class,
                'zfcuser_change_email_form' => ChangeEmailFactory::class,

                AdapterDb::class => AdapterDbFactory::class,
                StorageDb::class => StorageDbFactory::class,

                'zfcuser_user_service' => ServiceUserFactory::class,
            ],
        ];
    }
}
