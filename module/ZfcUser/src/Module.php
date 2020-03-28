<?php

namespace ZfcUser;

use Laminas\Db\Adapter\Adapter;
use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;
use Laminas\ModuleManager\Feature\ControllerPluginProviderInterface;
use Laminas\ModuleManager\Feature\ControllerProviderInterface;
use Laminas\ModuleManager\Feature\ServiceProviderInterface;
use Laminas\ModuleManager\Feature\ViewHelperProviderInterface;
use ZfcUser\Authentication\Adapter\AdapterChain;
use ZfcUser\Authentication\Adapter\Db as AdapterDb;
use ZfcUser\Authentication\Storage\Db as StorageDb;
use ZfcUser\Factory\AuthenticationServiceFactory;
use ZfcUser\Factory\UserHydratorFactory;
use ZfcUser\Factory\Authentication\Adapter\AdapterChainFactory;
use ZfcUser\Factory\Authentication\Adapter\DbFactory as AdapterDbFactory;
use ZfcUser\Factory\Authentication\Storage\DbFactory as StorageDbFactory;
use ZfcUser\Factory\Controller\RedirectCallbackFactory;
use ZfcUser\Factory\Controller\UserControllerFactory;
use ZfcUser\Factory\Controller\Plugin\ZfcUserAuthenticationFactory;
use ZfcUser\Factory\Form\ChangeEmailFactory;
use ZfcUser\Factory\Form\ChangePasswordFactory;
use ZfcUser\Factory\Form\LoginFactory;
use ZfcUser\Factory\Form\RegisterFactory;
use ZfcUser\Factory\Mapper\UserFactory as MapperUserFactory;
use ZfcUser\Factory\Options\ModuleOptionsFactory;
use ZfcUser\Factory\Service\UserFactory as ServiceUserFactory;
use ZfcUser\Factory\View\Helper\ZfcUserDisplayNameFactory;
use ZfcUser\Factory\View\Helper\ZfcUserIdentityFactory;
use ZfcUser\Factory\View\Helper\ZfcUserLoginWidgetFactory;

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
                'zfcUserAuthentication' => ZfcUserAuthenticationFactory::class,
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
                'zfcUserDisplayName' => ZfcUserDisplayNameFactory::class,
                'zfcUserIdentity' => ZfcUserIdentityFactory::class,
                'zfcUserLoginWidget' => ZfcUserLoginWidgetFactory::class,
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

                // We alias this one because it's ZfcUser's instance of
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
