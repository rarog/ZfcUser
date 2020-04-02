<?php

namespace UserAuthenticatorTest\Factory\Service;

use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use UserAuthenticator\Factory\Service\UserServiceFactory;
use UserAuthenticator\Form\Register;
use UserAuthenticator\Mapper\User as UserMapper;
use UserAuthenticator\Options\ModuleOptions;
use UserAuthenticator\Service\UserService;

class UserServiceFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setService(
            UserMapper::class,
            $this->getMockBuilder(UserMapper::class)->getMock()
        );
        $serviceManager->setService(
            'zfcuser_auth_service',
            $this->getMockBuilder(AuthenticationService::class)->getMock()
        );
        $serviceManager->setService(
            ModuleOptions::class,
            $this->getMockBuilder(ModuleOptions::class)->getMock()
        );

        $factory = new UserServiceFactory();

        $this->assertInstanceOf(UserService::class, $factory->__invoke($serviceManager, Register::class));
    }
}
