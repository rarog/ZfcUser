<?php

namespace UserAuthenticatorTest\Factory\Controller;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Controller\RedirectCallback;
use UserAuthenticator\Controller\UserController;
use UserAuthenticator\Factory\Controller\UserControllerFactory;
use UserAuthenticator\Form\ChangeEmailForm;
use UserAuthenticator\Form\ChangePasswordForm;
use UserAuthenticator\Form\LoginForm;
use UserAuthenticator\Form\RegisterForm;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Service\UserService;

class UserControllerFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            RedirectCallback::class,
            $this->getMockBuilder(RedirectCallback::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
        $serviceManager->setService(
            UserService::class,
            $this->getMockBuilder(UserService::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
        $serviceManager->setService(
            ChangeEmailForm::class,
            $this->getMockBuilder(ChangeEmailForm::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
        $serviceManager->setService(
            ChangePasswordForm::class,
            $this->getMockBuilder(ChangePasswordForm::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
        $serviceManager->setService(
            LoginForm::class,
            $this->getMockBuilder(LoginForm::class)
            ->disableOriginalConstructor()
            ->getMock()
        );
        $serviceManager->setService(
            RegisterForm::class,
            $this->getMockBuilder(RegisterForm::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
        $serviceManager->setService(
            ModuleOptions::class,
            $this->getMockBuilder(ModuleOptions::class)->getMock()
        );

        $factory = new UserControllerFactory();

        $this->assertInstanceOf(
            UserController::class,
            $factory->__invoke($serviceManager, UserController::class)
        );
    }
}
