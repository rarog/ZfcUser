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
use ZfcUser\Factory\AuthenticationService;
use ZfcUser\Factory\UserHydrator;
use ZfcUser\Factory\Authentication\Adapter\AdapterChainFactory;
use ZfcUser\Factory\Authentication\Adapter\DbFactory as AdapterDbFactory;
use ZfcUser\Factory\Authentication\Storage\DbFactory as StorageDbFactory;
use ZfcUser\Factory\Controller\RedirectCallbackFactory;
use ZfcUser\Factory\Controller\UserControllerFactory;
use ZfcUser\Factory\Controller\Plugin\ZfcUserAuthentication;
use ZfcUser\Factory\Form\ChangeEmail;
use ZfcUser\Factory\Form\ChangePassword;
use ZfcUser\Factory\Form\Login;
use ZfcUser\Factory\Form\Register;
use ZfcUser\Factory\Mapper\User;
use ZfcUser\Factory\Options\ModuleOptions;
use ZfcUser\Factory\Service\UserFactory;
use ZfcUser\Factory\View\Helper\ZfcUserDisplayName;
use ZfcUser\Factory\View\Helper\ZfcUserIdentity;
use ZfcUser\Factory\View\Helper\ZfcUserLoginWidget;

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
                'zfcUserAuthentication' => ZfcUserAuthentication::class,
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
                'zfcUserDisplayName' => ZfcUserDisplayName::class,
                'zfcUserIdentity' => ZfcUserIdentity::class,
                'zfcUserLoginWidget' => ZfcUserLoginWidget::class,
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
                'zfcuser_module_options' => ModuleOptions::class,
                AdapterChain::class => AdapterChainFactory::class,

                // We alias this one because it's ZfcUser's instance of
                // Laminas\Authentication\AuthenticationService. We don't want to
                // hog the FQCN service alias for a Laminas\* class.
                'zfcuser_auth_service' => AuthenticationService::class,

                'zfcuser_user_hydrator' => UserHydrator::class,
                'zfcuser_user_mapper' => User::class,

                'zfcuser_login_form' => Login::class,
                'zfcuser_register_form' => Register::class,
                'zfcuser_change_password_form' => ChangePassword::class,
                'zfcuser_change_email_form' => ChangeEmail::class,

                AdapterDb::class => AdapterDbFactory::class,
                StorageDb::class => StorageDbFactory::class,

                'zfcuser_user_service' => UserFactory::class,
            ],
        ];
    }
}
